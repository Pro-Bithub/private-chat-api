<?php

namespace App\Controller;

use App\Entity\Supportickets;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SupportTableController extends AbstractController
{
    protected $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }


    #[Route('/support/get_tickets', name: 'support_get_tickets')]
    public function supportnewticket(Request $request, EntityManagerInterface $entityManagerInterface): Response
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
                if ($columns[$orders['column']]['name'] == 'name')
                    $sort[] =  ' st.first_name ' . $orders['dir'];
                else
                    $sort[] = $columns[$orders['column']]['name'] . ' ' . $orders['dir'];
            }
        }

        $filters = [];
        $filterValues = [];
        if ($request->get('search')['value'] &&  trim($request->get('search')['value']) != '') {
            $filters[] = "(st.id LIKE :searchTerm OR st.profile_id LIKE :searchTerm OR  st.last_name LIKE :searchTerm OR  st.first_name LIKE :searchTerm OR st.email LIKE :searchTerm OR st.profile_type LIKE :searchTerm )";
            $filterValues['searchTerm'] = '%' . trim($request->get('search')['value']) . '%';
        }

        if ($request->get('columns')) {
            foreach ($request->get('columns') as $column) {
                if (isset($column['search']['value']) && trim($column['search']['value']) != '') {
                    if ($column['name'] == 'name') {
                        $filters[] = " (  LOWER(st.first_name) LIKE LOWER(:" . $column['name'] . ") OR LOWER(st.last_name )  LIKE LOWER(:" . $column['name'] . ") OR   LOWER(CONCAT(st.first_name, ' ', st.last_name))   LIKE LOWER(:" . $column['name'] . ")   OR  st.profile_id like :" . $column['name'] . "  )";
                        $filterValues[$column['name']] = $column['search']['value'];

                    } else if ($column['name'] == 'created_at') {
                        $filters[] = "( DATE_FORMAT(st.created_at, '%Y-%m-%d') LIKE :" . str_replace('.', '_', $column['name']) . ")";
                        $filterValues[str_replace('.', '_', $column['name'])] = $column['search']['value'];
                    } else {
                        $filters[] = "(st." . $column['name'] . " LIKE :" . $column['name'] . ")";
                        $filterValues[$column['name']] = $column['search']['value'];
                    }
                }
            }
        }


        
        if (empty($filters)) {
            $filters[] = ' 1=1';
        }

        $sql1 = "SELECT st.*
            FROM support_tickets st
                 " . (!empty($filters) ? 'where st.customer_account_id = :account_id and' : '') . implode(' AND', $filters) . "
          
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "
                LIMIT :limit OFFSET :offset
                ;";

        //dd($sql1,$filters);
        $sql2 = "SELECT st.*
                 FROM support_tickets st
             
                " . (!empty($filters) ? 'where st.customer_account_id  = :account_id and' : '') . implode(' AND', $filters) . "
           
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "
                ;";

        $sql3 = "SELECT st.*
                 FROM support_tickets st
                where st.customer_account_id = :account_id 
                  ";


/* return new JsonResponse([
    'draw' => $draw,
    'sql1' => $sql1,

]);
 */
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

        return new JsonResponse([
            'draw' => $draw,
            'recordsTotal' => $results3,
            'recordsFiltered' => $results1,
            'data' => $results,
        ]);
    }
}
