<?php

namespace App\Controller;

use App\Repository\UserLogsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AgentLogsController extends AbstractController
{
   
    #[Route('/user/presentations/{id}', name: 'app_user_presentations_controller')]
    public function getuserPresentations(Request $request,EntityManagerInterface $entityManagerInterface,$id): Response
    {
        //$logs_user = $this->UserLogsRepository->loadlogsByUser($id);

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
        // $user = $tokenData->getUser();

        $draw = (int) $request->get('draw', 1);
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 5);
        $columns = $request->get('columns');

        $order = $request->get('order');

        $sort = [];
        foreach ($order as  $orders) {
            if (isset($columns[$orders['column']]['name'])) {
                if($columns[$orders['column']]['name']!="action")
                $sort[] = $columns[$orders['column']]['name'] . ' ' . $orders['dir'];
            }
        }
        $filters = [];
        $filterValues = [];
        if ($request->get('search')['value'] &&  trim($request->get('search')['value']) != '') {
            $filters[] = "(up.nickname LIKE :searchTerm  OR up.contact_phone LIKE :searchTerm OR up.contact_mail LIKE :searchTerm  OR up.contact_phone_comment LIKE :searchTerm   OR up.brand_name LIKE :searchTerm    OR up.presentation LIKE :searchTerm )";
            $filterValues['searchTerm'] = '%' . trim($request->get('search')['value']) . '%';
        }

        if ($request->get('columns')) {
            foreach ($request->get('columns') as $column) {
                if (isset($column['search']['value']) && trim($column['search']['value']) != '') {
                    $filters[] = "(" . $column['name'] . " LIKE :" . str_replace('.', '_', $column['name']) . ")";
                    $filterValues[str_replace('.', '_', $column['name'])] = $column['search']['value'];
                }
            }
        }

        $sql1 = "SELECT up.* FROM user_presentations up
      
        where up.user_id = :id 
                 " . (!empty($filters) ? '  and'  . implode(' AND', $filters)  : ''). "
               
                " . (!empty($sort) ? 'order BY '   . implode(' ,', $sort): '') . "
                LIMIT :limit OFFSET :offset             
                ;";

        // dd($sql1,$filters,$filterValues);
        $sql2 = "SELECT up.* FROM user_presentations up
        where up.user_id = :id 
                 " . (!empty($filters) ? '  and'  . implode(' AND', $filters)  : ''). "
               
                " . (!empty($sort) ? 'order BY '   . implode(' ,', $sort): '') . "
                ;";

        $sql3 = "SELECT up.* FROM user_presentations up
        where up.user_id = :id ;";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
         $statement3->bindValue('id', $id);

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
         $statement->bindValue('id', $id);
         $statement1->bindValue('id', $id);

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












        // $RAW_QUERY4 = 'SELECT p.browser_data, l.action FROM user_logs l 
        // INNER JOIN profiles p ON p.u_id = u.id  
        // WHERE l.user_id = :id and p.u_type = 1 and l.element != 25;';
        // $stmt4 = $entityManagerInterface->getConnection()->prepare($RAW_QUERY4);
        // $stmt4->bindValue('id', $id);
        // $result4 = $stmt4->executeQuery()->fetchAllAssociative();
        // //dd($);
        //  return new JsonResponse([
        //     'success' => 'true',
        //     'data' => $result4
        // ]);
    }

    #[Route('/AgentLogs/{id}', name: 'app_AgentLogs_controller')]
    public function AgentLogs(Request $request,EntityManagerInterface $entityManagerInterface,$id): Response
    {
        //$logs_user = $this->UserLogsRepository->loadlogsByUser($id);

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
        // $user = $tokenData->getUser();

        $draw = (int) $request->get('draw', 1);
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 5);
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
            $filters[] = "(l.action LIKE :searchTerm )";
            $filterValues['searchTerm'] = '%' . trim($request->get('search')['value']) . '%';
        }

        if ($request->get('columns')) {
            foreach ($request->get('columns') as $column) {
                if (isset($column['search']['value']) && trim($column['search']['value']) != '') {
                    $filters[] = "(" . $column['name'] . " LIKE :" . str_replace('.', '_', $column['name']) . ")";
                    $filterValues[str_replace('.', '_', $column['name'])] = $column['search']['value'];
                }
            }
        }

        $sql1 = "SELECT p.browser_data AS browserData, l.action FROM user_logs l 
        INNER JOIN user u ON u.id = l.user_id  

        INNER JOIN profiles p ON p.u_id = u.id  
        where l.user_id = :id and l.element = 28

                 " . (!empty($filters) ? '  and'  . implode(' AND', $filters)  : ''). "
                GROUP BY l.id
                " . (!empty($sort) ? 'order BY '   . implode(' ,', $sort): '') . "
                LIMIT :limit OFFSET :offset             
                ;";

        // dd($sql1,$filters,$filterValues);
        $sql2 = "SELECT p.browser_data AS browserData, l.action FROM user_logs l 
        INNER JOIN user u ON u.id = l.user_id  
        INNER JOIN profiles p ON p.u_id = u.id
        where l.user_id = :id and l.element = 28
                " . (!empty($filters) ? '  and' . implode(' AND', $filters)  : '') . "
                GROUP BY l.id
                " . (!empty($sort) ? 'order BY ' . implode(' ,', $sort) : '')  . "
                ;";

        $sql3 = "SELECT p.browser_data AS browserData, l.action FROM user_logs l 
        INNER JOIN user u ON u.id = l.user_id  

        INNER JOIN profiles p ON p.u_id = u.id  
        WHERE l.user_id = :id and l.element = 28 GROUP BY l.id";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
         $statement3->bindValue('id', $id);

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
         $statement->bindValue('id', $id);
         $statement1->bindValue('id', $id);

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












        // $RAW_QUERY4 = 'SELECT p.browser_data, l.action FROM user_logs l 
        // INNER JOIN profiles p ON p.u_id = u.id  
        // WHERE l.user_id = :id and p.u_type = 1 and l.element != 25;';
        // $stmt4 = $entityManagerInterface->getConnection()->prepare($RAW_QUERY4);
        // $stmt4->bindValue('id', $id);
        // $result4 = $stmt4->executeQuery()->fetchAllAssociative();
        // //dd($);
        //  return new JsonResponse([
        //     'success' => 'true',
        //     'data' => $result4
        // ]);
    }
}
