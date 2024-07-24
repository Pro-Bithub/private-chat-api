<?php

namespace App\Controller;

use App\Repository\PlansRepository;
use App\Repository\PredefindTextsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class getPricingPlansController extends AbstractController
{
    public $PlansRepository;
    public $jwtManager;
    public $tokenStorage;
    public function __construct(PlansRepository $PlansRepository, JWTTokenManagerInterface $jwtManager, TokenStorageInterface $tokenStorage)
    {
        $this->PlansRepository = $PlansRepository;
        $this->jwtManager = $jwtManager;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/planinfo', name: 'planinfo')]
    public function get_plan(Request $request, EntityManagerInterface $entityManagerInterface): JsonResponse
    {

      
        $data = json_decode($request->getContent(), true);

        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $sql = "SELECT info.* FROM plan_info info WHERE info.plan_tariff_id IN ($placeholders)";

        $stmt = $entityManagerInterface->getConnection()->prepare($sql);

    
        $result = $stmt->executeQuery($data)->fetchAllAssociative();

        return new JsonResponse([
            'success' => 'true',
            'data' =>   $result 
        ]);
      
        return new JsonResponse();
    }
    

    //     public function isValidToken($token, JWTTokenManagerInterface $jwtManager)
    // {
    //     try {
    //         // This will throw an exception if the token is invalid or expired
    //         $jwtManager->decode($token);
    //         return true;
    //     } catch (\Exception $e) {
    //         return false;
    //     }
    // }


    private function getUserIdFromToken($token, JWTTokenManagerInterface $jwtManager)
    {
        try {
            // Decode the token to get the data inside it
            $tokenData = $jwtManager->decode($token);

            // Assuming the user ID is stored in the 'id' claim of the token payload
            if (isset($tokenData['id'])) {
                return $tokenData['id'];
            } else {
                // If the 'id' claim doesn't exist, the token is invalid
                return null;
            }
        } catch (\Exception $e) {
            return null; // Token decoding failed, return null or handle the error as needed
        }
    }

    #[Route('/get_plan', name: 'app_get_plan_controller')]
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
            $filters[] = "(e.id LIKE :searchTerm OR e.name LIKE :searchTerm OR e.currency LIKE :searchTerm OR e.tariff LIKE :searchTerm OR e.billing_volume LIKE :searchTerm OR d.name LIKE :searchTerm)";
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

        if(empty($filters)){
            $filters[] = ' 1=1';
        }
      /*   return new JsonResponse([
         
            'filterValues' => $filterValues,
         
        ]); */
        
        $sql1 = "SELECT e.*, GROUP_CONCAT( DISTINCT r.user_id SEPARATOR ',') AS user_ids, d.name as dicount_name , count(DISTINCT tf.id)as tariff_counts
            FROM plans e
           left JOIN plan_users r ON r.plan_id = e.id and r.status = 1
           left JOIN plan_discounts d ON d.plan_id = e.id
           left JOIN plan_tariffs tf ON tf.plan_id = e.id and tf.status = 1
                 " . (!empty($filters) ? 'where e.account_id = :account_id and' : '') . implode(' AND', $filters) . "
                GROUP BY e.id
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "
                LIMIT :limit OFFSET :offset             
                ;";


        // dd($sql1,$filters,$filterValues);
        $sql2 = "SELECT e.*, GROUP_CONCAT(r.user_id SEPARATOR ',') AS user_ids, d.name as dicount_name , count(DISTINCT tf.id)as tariff_counts
                FROM plans e
                left  JOIN plan_users r ON r.plan_id = e.id and r.status = 1
                left JOIN plan_discounts d ON d.plan_id = e.id
                left JOIN plan_tariffs tf ON tf.plan_id = e.id and tf.status = 1
                " . (!empty($filters) ? 'where e.account_id = :account_id and' : '') . implode(' AND', $filters) . "
                GROUP BY e.id
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "
                ;";

        $sql3 = "SELECT e.*, GROUP_CONCAT(r.user_id SEPARATOR ',') AS user_ids, d.name as dicount_name , count(DISTINCT tf.id)as tariff_counts
        FROM plans e
        left  JOIN plan_users r ON r.plan_id = e.id and r.status = 1
        left JOIN plan_discounts d ON d.plan_id = e.id
        left JOIN plan_tariffs tf ON tf.plan_id = e.id and tf.status = 1
         where e.account_id = :account_id 
        GROUP BY e.id
        ;";
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
            'data' => $results
     
        ]);
    }


    
    #[Route('/get_plan_tariffs/{id}', name: 'app_get_plan_tariffs_controller')]
    public function get_plan_tariffs($id,Request $request, EntityManagerInterface $entityManagerInterface): JsonResponse
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
            $filters[] = "(tf.id LIKE :searchTerm OR tf.price LIKE :searchTerm OR tf.currency LIKE :searchTerm OR tf.country LIKE :searchTerm OR tf.language LIKE :searchTerm )";
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

        if(empty($filters)){
            $filters[] = ' 1=1';
        }
      /*   return new JsonResponse([
         
            'filterValues' => $filterValues,
         
        ]); */
        
        $sql1 = "SELECT tf.*
            FROM plans e
           left JOIN plan_tariffs tf ON tf.plan_id = e.id 
                 " . (!empty($filters) ? 'where e.account_id = :account_id and  tf.plan_id = :id and' : '') . implode(' AND', $filters) . "
                GROUP BY tf.id
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "
                LIMIT :limit OFFSET :offset             
                ;";


        // dd($sql1,$filters,$filterValues);
        $sql2 = "SELECT tf.*
                FROM plans e
         
                left JOIN plan_tariffs tf ON tf.plan_id = e.id 
                " . (!empty($filters) ? 'where e.account_id = :account_id and  tf.plan_id = :id and' : '') . implode(' AND', $filters) . "
                GROUP BY tf.id
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "
                ;";

        $sql3 = "SELECT tf.*
        FROM plans e
    
        left JOIN plan_tariffs tf ON tf.plan_id = e.id 
         where e.account_id = :account_id   and  tf.plan_id = :id
        GROUP BY tf.id
        ;";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('account_id', $user->accountId);
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
        $statement->bindValue('account_id', $user->accountId);
        $statement->bindValue('id', $id);
        $statement1->bindValue('account_id', $user->accountId);
        $statement1->bindValue('id', $id);
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


    #[Route('/tariffs/get/country', name: 'app_get_country_tariffs_controller')]
    public function getcounrty(Request $request, EntityManagerInterface $entityManagerInterface): JsonResponse
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


        $sql1 = "SELECT DISTINCT  UPPER( c.country) as country
        FROM plan_tariffs c
        where  c.country is not null and  trim(c.country) <> ''
            ;";

        $statement = $entityManagerInterface->getConnection()->prepare($sql1);
        $results = $statement->executeQuery()->fetchAllAssociative();

        return new JsonResponse([
            'data' => $results,

        ]);
    }

   



}
