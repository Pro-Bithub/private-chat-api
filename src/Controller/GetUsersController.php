<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GetUsersController extends AbstractController
{
    #[Route('/get_user/{id}', name: 'app_get_user_controller')]
    public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface, $id): JsonResponse
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
            $filters[] = " and (e.id LIKE :searchTerm OR e.email LIKE :searchTerm OR p.nickname OR e.firstname LIKE :searchTerm)";

            $filterValues['searchTerm'] = '%' . trim($request->get('search')['value']) . '%';
        }

        if ($request->get('columns')) {
            foreach ($request->get('columns') as $column) {
                if (isset($column['search']['value']) && trim($column['search']['value']) != '') {

                    if ($column['name'] == 'role' || $column['name'] == 'nickname') {
                        $filters[] = "and (p." . $column['name'] . " LIKE :" . $column['name'] . ")";
                    } else {
                        $filters[] = "and (e." . $column['name'] . " LIKE :" . $column['name'] . ")";
                    }
                    $filterValues[$column['name']] = ($column['search']['value']);
                }
            }
        }

        $sql1 = "SELECT e.* , p.nickname as nickname , p.role as role
            FROM user e
            left join user_presentations p on p.user_id = e.id
            where e.id != :id and e.account_id = :account_id 
            " . implode(' ', $filters) . "
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "
                LIMIT :limit OFFSET :offset;";

        //dd($sql1,$filters);
        $sql2 = "SELECT e.* FROM user e
                left join user_presentations p on p.user_id = e.id
                where e.id != :id and e.account_id = :account_id 
                " . implode(' ', $filters) . "
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . ";";

        $sql3 = "SELECT * FROM user e where e.id != :id and e.account_id = :account_id";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('account_id', $user->accountId);

        $statement3->bindValue('id', $id);

        $results3 = $statement3->executeQuery()->rowCount();

        $statement = $entityManagerInterface->getConnection()->prepare($sql1);
        $statement->bindValue('id', $id);

        $statement1 = $entityManagerInterface->getConnection()->prepare($sql2);
        $statement1->bindValue('id', $id);

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
            'data' => $results
        ]);
    }

    #[Route('/account_user/{id}')]
    public function getaccounts(Request $request, EntityManagerInterface $entityManagerInterface, $id)
    {
        $sql3 = "SELECT * FROM accounts a where a.id = :id and a.status = 1 and (CURRENT_DATE BETWEEN a.date_start AND a.date_end)";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $id);
        $results = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => 'success',
            'data' => $results
        ]);
    }

    #[Route('/getAgentByProfileId/{id}', name: 'app_get_Agent_By_Profile_Id_details')]
    public function getAgentByProfileId(EntityManagerInterface $entityManagerInterface, $id): Response
    {
        $sql = "SELECT c.lastname , c.firstname, c.id , pr.picture
        FROM `profiles` AS p
        LEFT JOIN `user` AS c ON c.id = p.u_id 
        left join user_presentations as pr on pr.user_id = c.id
        WHERE p.id = :id and c.status = 1";

        $statement = $entityManagerInterface->getConnection()->prepare($sql);
        $statement->bindValue('id', $id);
        $profiles = $statement->executeQuery()->fetchAssociative();


        return new JsonResponse([
            'success' => true,
            'data' => $profiles,
        ]);
    }


    #[Route('/checkMail')]
    public function checkMail(Request $request, EntityManagerInterface $entityManagerInterface): Response
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

        $slug = $request->query->get('email');
        // $account = $request->query->get('account');
        
        $sql3 = "SELECT u.email FROM `user` as u WHERE u.email = :email ";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('email', $slug);
        
        $results3 = $statement3->executeQuery()->fetchAllAssociative();

        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }

}
