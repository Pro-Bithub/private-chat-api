<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GetContactFormsController extends AbstractController
{
    #[Route('/get_forms', name: 'app_get_forms_controller')]
    public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface): JsonResponse
    {



        $authorizationHeader = $request->headers->get('Authorization');

        // Check if the token is present and in the expected format (Bearer TOKEN)
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            throw new AccessDeniedException('Invalid or missing authorization token.');
        }

        // Extract the token value (without the "Bearer " prefix)
        $token = substr($authorizationHeader, 7);

        $tokenData = $this->get('security.token_storage')->getToken();

        if ($tokenData === null) {
            throw new AccessDeniedException('Invalid token.');
        }

        // Now you can access the user data from the token (assuming your User class has a `getUsername()` method)
        $user = $tokenData->getUser();


        $draw = (int) $request->get('draw', 1);
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 5);
        $search = $request->get('columns')[1]['search']['value'] ?? null;

        $columns = $request->get('columns');

        $order = $request->get('order');

        $sort = [];
        foreach ($order as  $orders) {
            if (isset($columns[$orders['column']]['name'])) {
                $sort[] = $columns[$orders['column']]['name'] . ' ' . $orders['dir'];
            }
        }


        $filters = [];
        $filterValues = [];
        if ($request->get('search')['value'] &&  trim($request->get('search')['value']) != '') {
            $filters[] = "(e.id LIKE :searchTerm OR e.friendly_name LIKE :searchTerm)";
            $filterValues['searchTerm'] = '%' . trim($request->get('search')['value']) . '%';
        }

        if ($request->get('columns')) {
            foreach ($request->get('columns') as $column) {
                if (isset($column['search']['value']) && trim($column['search']['value']) != '') {
                    $filters[] = "(e." . $column['name'] . " LIKE :" . $column['name'] . ")";
                    $filterValues[$column['name']] = $column['search']['value'];
                }
            }
        }
        if (empty($filters)) {
            $filters[] = ' 1=1';
        }

        $sql1 = "SELECT e.*
            FROM contact_forms e
                 " . (!empty($filters) ? 'where (e.account_id = :account_id  ) and' : '') . implode(' AND', $filters) . "
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "
                LIMIT :limit OFFSET :offset;";

        //dd($sql1,$filters);
        $sql2 = "SELECT e.* FROM contact_forms e
                
                " . (!empty($filters) ? 'where (e.account_id = :account_id  ) and ' : '') . implode(' AND', $filters) . "
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . ";";

        $sql3 = "SELECT * FROM contact_forms p0_ where p0_.account_id = :account_id";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('account_id', $user->accountId);

        $results3 = $statement3->executeQuery()->rowCount();

        $statement = $entityManagerInterface->getConnection()->prepare($sql1);
        $statement1 = $entityManagerInterface->getConnection()->prepare($sql2);
        foreach ($filterValues as $key => $value) {
            $statement->bindValue($key, $value);
            $statement1->bindValue($key, $value);
        }
        // $statement->bindValue('searchTerm', '%' . $search . '%');
        $statement->bindValue('limit', $length, \PDO::PARAM_INT);
        $statement->bindValue('offset', $start, \PDO::PARAM_INT);
        $statement->bindValue('account_id', $user->accountId);
        $statement1->bindValue('account_id', $user->accountId);


        $results = $statement->executeQuery()->fetchAllAssociative();
        $results1 = $statement1->executeQuery()->rowCount();
        // $data1 = $this->PredefindTextsRepository->findDataBySearch($search);
        // dd($results);
        /* 
               $sql3= "SELECT * FROM contact_forms cf where cf.account_id = :account_id";
               $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
               $statement3->bindValue('account_id',$user->accountId);
       
               $results3 = $statement3->executeQuery() */

        if ((isset($filterValues['status']) && $filterValues['status'] == "1") && $start ==0) {
            $onlyRegistration = false;
            if (isset($filterValues['form_type'])) {
                if ($filterValues['form_type'] == "1")
                    $onlyRegistration = true;
            } else {
                $onlyRegistration = true;
            }

            if ($onlyRegistration) {
/* 
                $issetformRegistration = false;
                foreach ($results as $form) {
                    if ($form['form_type'] == '1') {
                        $issetformRegistration = true;
                        break;
                    }
                } */
       /*          if ($issetformRegistration == false) { */
                    $sqlr = "SELECT e.*
                    FROM contact_forms e
              
                    WHERE e.account_id IS NULL AND e.form_type = 1 
                    AND (
                        SELECT COUNT(ch.id) 
                        FROM contact_forms ch 
                        WHERE ch.account_id = :account_id
                        AND ch.form_type = 1  AND ch.status = 1
                    ) = 0
    
                    ORDER BY e.id DESC
                    LIMIT 1;  ";

                    $statement = $entityManagerInterface->getConnection()->prepare($sqlr);
                    $statement->bindValue('account_id', $user->accountId);
                    $resultssqlr = $statement->executeQuery()->fetchAllAssociative();

                    if (count($resultssqlr) > 0) {
                        $results = array_merge($results, $resultssqlr);
                    }
             /*    } */
            }
        }

        return new JsonResponse([
            'draw' => $draw,
            'recordsTotal' => $results3,
            'recordsFiltered' => $results1,
            'data' => $results,
            'filterValues' => $filterValues,

        ]);
    }
}
