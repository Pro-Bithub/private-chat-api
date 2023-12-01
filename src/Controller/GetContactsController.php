<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GetContactsController extends AbstractController
{
    #[Route('/get_contacts', name: 'app_get_contacts_controller')]
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
                if ($columns[$orders['column']]['name'] == 'created_at')
                    $sort[] =  ' e.id ' . $orders['dir'];
                else
                    $sort[] = $columns[$orders['column']]['name'] . ' ' . $orders['dir'];
            }
        }


        $filters = [];
        $filterValues = [];

        $filtershaving = ['  created_at IS NOT NULL'];
        $filterValueshaving = [];
        if ($request->get('search')['value'] &&  trim($request->get('search')['value']) != '') {
            $filters[] = "(e.id LIKE :searchTerm OR e.name LIKE :searchTerm OR e.email LIKE :searchTerm OR e.lastname LIKE :searchTerm OR e.firstname LIKE :searchTerm OR e.country LIKE :searchTerm OR e.phone LIKE :searchTerm)";
            $filterValues['searchTerm'] = '%' . trim($request->get('search')['value']) . '%';
        }


        if ($request->get('columns')) {
            foreach ($request->get('columns') as $column) {


                if (isset($column['search']['value']) && trim($column['search']['value']) != '') {

                    if ($column['name'] == 'total_sales') {
                        $filtershaving[] = "(total_sales = :total_sales)";
                        $filterValueshaving[str_replace('.', '_', $column['name'])] = str_replace('%', '', $column['search']['value']);
                    } else if ($column['name'] == 'created_at') {
                        $filters[] = "( DATE_FORMAT(cd.created_at, '%Y-%m-%d') LIKE :" . str_replace('.', '_', $column['name']) . ")";
                        $filterValues[str_replace('.', '_', $column['name'])] = $column['search']['value'];
                    } else {
                        $filters[] = "(" . $column['name'] . " LIKE :" . str_replace('.', '_', $column['name']) . ")";
                        $filterValues[str_replace('.', '_', $column['name'])] = $column['search']['value'];
                    }
                }
            }
        }




        if (empty($filters)) {
            $filters[] = ' 1=1';
        }

        $sql1 = "  SELECT SUBSTRING_INDEX(GROUP_CONCAT(cd.created_at ORDER BY cd.created_at), ',', 1) as created_at, e.* , IFNULL(subquery.total_sales, 0) as total_sales 
            FROM contacts e
             LEFT JOIN (
                SELECT contact_id, COUNT(*) as total_sales
                FROM sales
                GROUP BY contact_id
            ) subquery ON subquery.contact_id = e.id
                        LEFT JOIN contact_custom_fields cd ON cd.contact_id = e.id
                            " . (!empty($filters) ? 'where e.account_id = :account_id and' : '') . implode(' AND', $filters) . "
                            GROUP BY e.id
                            " . (!empty($filtershaving) ? ' having ' : '') . implode(' AND', $filtershaving) . "
                            " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "
                        
                            LIMIT :limit OFFSET :offset
                            ;";


        /*           return new JsonResponse([
                'sql1' => $sql1,
              
            ]); */

        //dd($sql1,$filters);
        /*  , SUBSTRING_INDEX(GROUP_CONCAT(cb.balance), ',', -1) as balance
                   , SUBSTRING_INDEX(GROUP_CONCAT(cb.balance_type), ',', -1) as type
                   , SUBSTRING_INDEX(GROUP_CONCAT(d.date_creation), ',', -1) as last_payment_date 
                     left JOIN contact_balances cb ON cb.contact_id = e.id*/
        $sql2 = "SELECT SUBSTRING_INDEX(GROUP_CONCAT(cd.created_at ORDER BY cd.created_at), ',', 1) as created_at, e.* ,  IFNULL(subquery.total_sales, 0) as total_sales 
                FROM contacts e
                 LEFT JOIN (
                    SELECT contact_id, COUNT(*) as total_sales
                    FROM sales
                    GROUP BY contact_id
                ) subquery ON subquery.contact_id = e.id
                                LEFT JOIN contact_custom_fields cd ON cd.contact_id = e.id
                                " . (!empty($filters) ? 'where e.account_id = :account_id and' : '') . implode(' AND', $filters) . "
                                GROUP BY e.id
                                " . (!empty($filtershaving) ? ' having ' : '') . implode(' AND', $filtershaving) . "
                                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "
                            
                                ;";

        $sql3 = "SELECT * FROM contacts where account_id = :account_id";

        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('account_id', $user->accountId);

        $results3 = $statement3->executeQuery()->rowCount();

        $statement = $entityManagerInterface->getConnection()->prepare($sql1);
        $statement1 = $entityManagerInterface->getConnection()->prepare($sql2);
        // dd($statement1,$filters);

        foreach ($filterValues as $key => $value) {
            $statement->bindValue($key, $value);
            $statement1->bindValue($key, $value);
        }

        foreach ($filterValueshaving as $key => $value) {
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


    #[Route('/contacts/get/country', name: 'app_get_country_contacts_controller')]
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
        FROM contacts c
        where  c.country is not null and  trim(c.country) <> ''
            ;";

        $statement = $entityManagerInterface->getConnection()->prepare($sql1);
        $results = $statement->executeQuery()->fetchAllAssociative();

        return new JsonResponse([
            'data' => $results,

        ]);
    }
}
