<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GetsalesController extends AbstractController
{
    #[Route('/get_all_sales', name: 'app_get_all_sales_controller')]
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
            $filters[] = "(e.id LIKE :searchTerm OR e.payment_method LIKE :searchTerm OR d.email LIKE :searchTerm OR r.email LIKE :searchTerm OR p.name LIKE :searchTerm OR p.tariff LIKE :searchTerm)";
            $filterValues['searchTerm'] = '%' . trim($request->get('search')['value']) . '%';
        }

        if ($request->get('columns')) {
            foreach ($request->get('columns') as $column) {
                if (isset($column['search']['value']) && trim($column['search']['value']) != '') {
                    if ($column['name'] == 'email') {
                        $filters[] = " (r." . $column['name'] . " LIKE :" . $column['name'] . ")";
                    } else if ($column['name'] == 'firstname') {
                        $filters[] = "(d." . $column['name'] . " LIKE :" . $column['name'] . ")";
                    } else if ($column['name'] == 'name') {
                        $filters[] = "(p." . $column['name'] . " LIKE :" . $column['name'] . ")";
                    } else {
                        $filters[] = "(e." . $column['name'] . " LIKE :" . $column['name'] . ")";
                    }
                    $filterValues[$column['name']] = '%' . trim($column['search']['value']) . '%';
                }
            }
        }

        $sql1 = "SELECT e.* ,d.id as user_id, d.email as user_email, d.lastname as user_lastname , d.firstname as user_firstname, r.email as contact_email, r.name as contact_name,r.id as contact_id, p.name as plan_name, p.currency as plan_currency, p.tariff as plan_tariff
            FROM sales e
                left JOIN contacts r ON r.id = e.contact_id
                left JOIN user d ON d.id = e.user_id
                left JOIN plans p ON p.id = e.plan_id
                where p.account_id = :account_id 
                 " . (!empty($filters) ? ' and ' : '') . implode(' AND', $filters) . "
                GROUP BY e.id
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "
                LIMIT :limit OFFSET :offset
                ;";

        //dd($sql1,$filters);
        $sql2 = "SELECT e.* ,d.id as user_id, d.email as user_email, d.lastname as user_lastname , d.firstname as user_firstname, r.email as contact_email, r.name as contact_name, r.id as contact_id,p.name as plan_name, p.currency as plan_currency, p.tariff as plan_tariff
            FROM sales e
           left JOIN contacts r ON r.id = e.contact_id
           left JOIN user d ON d.id = e.user_id
           left JOIN plans p ON p.id = e.plan_id
           where p.account_id = :account_id 
                " . (!empty($filters) ? ' and ' : '') . implode(' AND', $filters) . "
                GROUP BY e.id
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "   
                ;";

        $sql3 = "SELECT * FROM sales as e left JOIN contacts r ON r.id = e.contact_id left JOIN plans p ON p.id = e.plan_id and p.account_id = :account_id";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        // dd($statement3);
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


    #[Route('/get_all_sales_contact/{contact_id}', name: 'app_get_all_sales_controller_contact')]
    public function getallsalesbycontact(Request $request, EntityManagerInterface $entityManagerInterface, $contact_id): JsonResponse
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
            $filters[] = "(e.id LIKE :searchTerm OR e.payment_method LIKE :searchTerm OR d.email LIKE :searchTerm OR r.email LIKE :searchTerm OR p.name LIKE :searchTerm OR p.tariff LIKE :searchTerm)";
            $filterValues['searchTerm'] = '%' . trim($request->get('search')['value']) . '%';
        }

        if ($request->get('columns')) {
            foreach ($request->get('columns') as $column) {
                if (isset($column['search']['value']) && trim($column['search']['value']) != '') {
                    if ($column['name'] == 'email') {
                        $filters[] = " (r." . $column['name'] . " LIKE :" . $column['name'] . ")";
                    } else if ($column['name'] == 'firstname') {
                        $filters[] = "(d." . $column['name'] . " LIKE :" . $column['name'] . ")";
                    } else if ($column['name'] == 'name') {
                        $filters[] = "(p." . $column['name'] . " LIKE :" . $column['name'] . ")";
                    } else {
                        $filters[] = "(e." . $column['name'] . " LIKE :" . $column['name'] . ")";
                    }
                    $filterValues[$column['name']] = '%' . trim($column['search']['value']) . '%';
                }
            }
        }

        $sql1 = "SELECT e.* ,d.id as user_id, d.email as user_email, d.lastname as user_lastname , d.firstname as user_firstname, r.email as contact_email, r.name as contact_name,r.id as contact_id, p.name as plan_name, p.currency as plan_currency, p.tariff as plan_tariff
            FROM sales e
                left JOIN contacts r ON r.id = e.contact_id
                left JOIN user d ON d.id = e.user_id
                left JOIN plans p ON p.id = e.plan_id
                where p.account_id = :account_id and e.contact_id = :contact_id
                 " . (!empty($filters) ? ' and ' : '') . implode(' AND', $filters) . "
                GROUP BY e.id
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "
                LIMIT :limit OFFSET :offset
                ;";

        //dd($sql1,$filters);
        $sql2 = "SELECT e.* ,d.id as user_id, d.email as user_email, d.lastname as user_lastname , d.firstname as user_firstname, r.email as contact_email, r.name as contact_name, r.id as contact_id,p.name as plan_name, p.currency as plan_currency, p.tariff as plan_tariff
            FROM sales e
           left JOIN contacts r ON r.id = e.contact_id
           left JOIN user d ON d.id = e.user_id
           left JOIN plans p ON p.id = e.plan_id
           where p.account_id = :account_id and e.contact_id = :contact_id
                " . (!empty($filters) ? ' and ' : '') . implode(' AND', $filters) . "
                GROUP BY e.id
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "   
                ;";

        $sql3 = "SELECT * FROM sales as e left JOIN contacts r ON r.id = e.contact_id left JOIN plans p ON p.id = e.plan_id and p.account_id = :account_id and e.contact_id = :contact_id";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        // dd($statement3);
        $statement3->bindValue('account_id', $user->accountId);
        $statement3->bindValue('contact_id', $contact_id);

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
        $statement->bindValue('contact_id', $contact_id);
        $statement1->bindValue('contact_id', $contact_id);
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
