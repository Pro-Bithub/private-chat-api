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
                if ($columns[$orders['column']]['name'] == 'firstname')
                    $sort[] =  'user_firstname ' . $orders['dir'];
                else if ($columns[$orders['column']]['name'] == 'name')
                    $sort[] = 'plan_name ' . $orders['dir'];
                else if ($columns[$orders['column']]['name'] == 'email')
                    $sort[] = 'contact_email ' . $orders['dir'];
                else
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
                        $filters[] = " (r." . $column['name'] . " LIKE :" . $column['name'] . " OR r.name " . " LIKE :" . $column['name'] .  " OR r.firstname " . " LIKE :" . $column['name'] . " OR r.lastname " . " LIKE :" . $column['name'] . " OR r.id " . " LIKE :" . $column['name'] . ")";
                    } else if ($column['name'] == 'firstname') {
                        $filters[] = "(d." . $column['name'] . " LIKE :" . $column['name'] . " OR d.email " . " LIKE :" . $column['name'] .  " OR d.firstname " . " LIKE :" . $column['name'] . " OR d.lastname " . " LIKE :" . $column['name'] . ")";
                    } else if ($column['name'] == 'name') {
                        $filters[] = "(p." . $column['name'] . " LIKE :" . $column['name'] . ")";
                    } else {
                        $filters[] = "(e." . $column['name'] . " LIKE :" . $column['name'] . ")";
                    }
                    $filterValues[$column['name']] = '%' . trim($column['search']['value']) . '%';
                }
            }
        }

        $sql1 = "SELECT  e.* ,up.picture as user_img , up.contact_mail as user_email, ''as user_lastname , up.nickname as user_firstname, r.email as contact_email, r.name as contact_name,p.name as plan_name, pf.currency as plan_currency, pf.price as plan_tariff
           FROM sales e
                left JOIN contacts r ON r.id = e.contact_id
                left JOIN user d ON d.id = e.user_id
                left JOIN user_presentations up ON up.id = e.p_id  
                left JOIN plans p ON p.id = e.plan_id
                left JOIN plan_tariffs pf ON pf.id = e.tariff_id
                where p.account_id = :account_id 
                 " . (!empty($filters) ? ' and ' : '') . implode(' AND', $filters) . "
                GROUP BY e.id
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "
                LIMIT :limit OFFSET :offset
                ;";

        //dd($sql1,$filters);
        $sql2 = "SELECT e.* ,up.picture as p_id ,up.picture as user_img , up.contact_mail as user_email, ''as user_lastname , up.nickname as user_firstname, r.email as contact_email, r.name as contact_name,p.name as plan_name, p.currency as plan_currency, p.tariff as plan_tariff
            FROM sales e
           left JOIN contacts r ON r.id = e.contact_id
           left JOIN user d ON d.id = e.user_id
           left JOIN user_presentations up ON up.id = e.p_id  
           left JOIN plans p ON p.id = e.plan_id
           where p.account_id = :account_id 
                " . (!empty($filters) ? ' and ' : '') . implode(' AND', $filters) . "
                GROUP BY e.id
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "   
                ;";

        $sql3 = "SELECT e.* ,d.id as user_id, d.email as user_email, d.lastname as user_lastname , d.firstname as user_firstname, r.email as contact_email, r.name as contact_name,p.name as plan_name, p.currency as plan_currency, p.tariff as plan_tariff
        FROM sales e
       left JOIN contacts r ON r.id = e.contact_id
       left JOIN user d ON d.id = e.user_id
       left JOIN plans p ON p.id = e.plan_id
       where p.account_id = :account_id 
            GROUP BY e.id";
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
            'data' => $results
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
                if ($columns[$orders['column']]['name'] == 'firstname')
                    $sort[] =  'user_firstname ' . $orders['dir'];
                else if ($columns[$orders['column']]['name'] == 'name')
                    $sort[] = 'plan_name ' . $orders['dir'];
                else
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
                        $filters[] = "(d." . $column['name'] . " LIKE :" . $column['name'] . " OR d.email " . " LIKE :" . $column['name'] .  " OR d.firstname " . " LIKE :" . $column['name'] . " OR d.lastname " . " LIKE :" . $column['name'] . ")";
                    } else if ($column['name'] == 'name') {
                        $filters[] = "(p." . $column['name'] . " LIKE :" . $column['name'] . ")";
                    } else {
                        $filters[] = "(e." . $column['name'] . " LIKE :" . $column['name'] . ")";
                    }
                    $filterValues[$column['name']] = '%' . trim($column['search']['value']) . '%';
                }
            }
        }

        $sql1 = "SELECT e.* ,up.picture as user_img , up.contact_mail as user_email, '' as user_lastname ,  up.nickname as user_firstname, r.email as contact_email, r.name as contact_name, p.name as plan_name, pf.currency as plan_currency, pf.price as plan_tariff
            FROM sales e
                left JOIN contacts r ON r.id = e.contact_id
                left JOIN user d ON d.id = e.user_id       
                left JOIN user_presentations up ON up.id = e.p_id and  up.status =1
                
                left JOIN plans p ON p.id = e.plan_id
                left JOIN plan_tariffs pf ON pf.id = e.tariff_id
                where p.account_id = :account_id and e.contact_id = :contact_id
                 " . (!empty($filters) ? ' and ' : '') . implode(' AND', $filters) . "
                GROUP BY e.id
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "
                LIMIT :limit OFFSET :offset
                ;";

        //dd($sql1,$filters);
        $sql2 = "SELECT e.* ,SUBSTRING_INDEX(GROUP_CONCAT(up.picture ), ',', 1) as user_img , d.email as user_email, d.lastname as user_lastname , d.firstname as user_firstname, r.email as contact_email, r.name as contact_name,p.name as plan_name, p.currency as plan_currency, p.tariff as plan_tariff
            FROM sales e
           left JOIN contacts r ON r.id = e.contact_id
           left JOIN user d ON d.id = e.user_id
           left JOIN user_presentations up ON up.user_id = e.user_id  and  up.status =1
           left JOIN plans p ON p.id = e.plan_id
           where p.account_id = :account_id and e.contact_id = :contact_id
                " . (!empty($filters) ? ' and ' : '') . implode(' AND', $filters) . "
                GROUP BY e.id
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "   
                ;";

        $sql3 = "SELECT * FROM sales as e left JOIN contacts r ON r.id = e.contact_id  left JOIN user d ON d.id = e.user_id left JOIN plans p ON p.id = e.plan_id where p.account_id = :account_id and e.contact_id = :contact_id GROUP BY e.id";
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
            'data' => $results
        ]);
    }


    #[Route('/get_forms/{contact_id}', name: 'get_forms')]
    public function getforms(Request $request, EntityManagerInterface $entityManagerInterface, $contact_id): JsonResponse
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
            $filters[] = "cf.field_value LIKE :searchTerm OR c.field_name LIKE :searchTerm ";
            $filterValues['searchTerm'] = '%' . trim($request->get('search')['value']) . '%';
        }

        if ($request->get('columns')) {
            foreach ($request->get('columns') as $column) {
                if (isset($column['search']['value']) && trim($column['search']['value']) != '') {
                    if ($column['name'] == 'field') {
                        $filters[] = " (c.field_name LIKE :" . $column['name'] . ")";
                    } else if ($column['name'] == 'value') {
                        $filters[] = "(cf.field_value  LIKE :" . $column['name'] . ")";
                    }
                    $filterValues[$column['name']] = '%' . trim($column['search']['value']) . '%';
                }
            }
        }

        $sql1 = "SELECT cf.id , cf.field_value as value , cf.created_at  ,c.field_name as field , f.friendly_name ,c.field_type
            FROM contact_custom_fields cf
            left JOIN `contact_form_fields` AS cff ON cff.id = cf.form_field_id
            left JOIN contact_forms f ON f.id = cff.form_id
            left JOIN custom_fields c ON c.id = cff.field_id

                where cf.contact_id = :contact_id
                 " . (!empty($filters) ? ' and ' : '') . implode(' AND', $filters) . "
                GROUP BY cf.id
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "
                LIMIT :limit OFFSET :offset
                ;";
        //dd($sql1,$filters);
        $sql2 = "SELECT  cf.id , cf.field_value as value , cf.created_at  ,c.field_name as field , f.friendly_name
       FROM contact_custom_fields cf
            left JOIN `contact_form_fields` AS cff ON cff.id = cf.form_field_id
            left JOIN contact_forms f ON f.id = cff.form_id
            left JOIN custom_fields c ON c.id = cff.field_id
            where cf.contact_id = :contact_id
             " . (!empty($filters) ? ' and ' : '') . implode(' AND', $filters) . "
            GROUP BY cf.id
            " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "  
                ;";
        $sql3 = "SELECT cf.id , cf.field_value as value , cf.created_at  ,c.field_name as field , f.friendly_name
       FROM contact_custom_fields cf
            left JOIN `contact_form_fields` AS cff ON cff.id = cf.form_field_id
            left JOIN contact_forms f ON f.id = cff.form_id
            left JOIN custom_fields c ON c.id = cff.field_id   
         
          where cf.contact_id = :contact_id";


        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        // dd($statement3);

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
            'data' => $results
        ]);
    }
}

