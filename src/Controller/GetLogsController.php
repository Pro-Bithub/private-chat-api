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

class GetLogsController extends AbstractController
{
    /**
    * @var UserLogsRepository
    */
    private $UserLogsRepository;
    public function __construct(UserLogsRepository $UserLogsRepository)
    {
        $this->UserLogsRepository = $UserLogsRepository;
    }

    public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        // $logs_user_account = $this->UserLogsRepository->loadlogsByAccount($id);
        // //dd($);
        // return $logs_user_account;
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
foreach ($order as  $orders){
    if(isset($columns[$orders['column']]['name'])){
        $sort[] = $columns[$orders['column']]['name']. ' ' .$orders['dir'];
    } 
}

$filters = [];
$filterValues= [];
if($request->get('search')['value'] &&  trim($request->get('search')['value']) != '') {
$filters[] = " (u.id = :searchTerm OR u.action LIKE :searchTerm)";
$filterValues['searchTerm'] = '%'.trim($request->get('search')['value']).'%';
}

if($request->get('columns')) {
    foreach($request->get('columns') as $column){
        if(isset($column['search']['value']) && trim($column['search']['value']) != '') {
            $filters[] = " (u.".$column['name']." LIKE :".$column['name'].")";
            $filterValues[$column['name']] = $column['search']['value'];
        }
    }
    }


   
$RAW_QUERY3 = "SELECT  u.*, us.id as agent_id, us.firstname as user_firstname, us.lastname as user_lastname, cont.id as contact_id, cont.name as contact_username, cont.firstname as contact_firstname, cont.lastname as contact_lastname, p.name as plan_name, cli.name as link_name, con.friendly_name as form_name, custom_fields.field_name as field_name, o.operation as operation
FROM `user_logs` u 
LEFT JOIN `user` us ON (us.id = u.user_id and u.source = 2 and u.element in (28,25))
LEFT JOIN `contact_operations` o ON (o.id = u.element_id)
LEFT JOIN `plans` p ON (p.id = o.operation_id and o.operation = 1) or (p.id = u.element_id and u.source = 1 and u.element = 3)
LEFT JOIN `contact_forms` con ON (con.id = o.operation_id and o.operation = 2) or (con.id = u.element_id and u.source = 1 and u.element = 21)
LEFT JOIN `clickable_links` cli ON (cli.id = o.operation_id and o.operation = 3) or (cli.id = u.element_id and u.source = 1 and u.element = 7)

LEFT JOIN `contacts` cont ON cont.id = u.element_id  

LEFT JOIN `contact_form_fields` formfields ON (formfields.id = u.element_id)
LEFT JOIN `custom_fields` custom_fields on (custom_fields.id = formfields.field_id)
WHERE ((u.user_id = cont.id and u.source = 3 and u.element in (20,21,3,7,22)) or (u.element_id = cont.id and u.source = 3 and u.element in (20,25)) or (((p.account_id = :id) or (con.account_id = :id) or (cli.account_id = :id)) and u.element in (28,20) and u.source IN (2,3)) or (custom_fields.account_id = :id and u.element = 22 and u.source IN (2,3)) or (u.element_id = o.contact_id and u.element = 25 and u.source = 3))
". (!empty($filters) ? 'AND' : ''). implode(' AND ',$filters)."
". (!empty($sort) ? 'order BY ' : ''). implode(' ,',$sort)."
LIMIT :limit OFFSET :offset;";


$RAW_QUERY4 = "SELECT  u.*, us.id as agent_id, us.firstname as user_firstname, us.lastname as user_lastname, cont.id as contact_id, cont.name as contact_username, cont.firstname as contact_firstname, cont.lastname as contact_lastname, p.name as plan_name, cli.name as link_name, con.friendly_name as form_name, custom_fields.field_name as field_name, o.operation as operation
FROM `user_logs` u 
LEFT JOIN `user` us ON (us.id = u.user_id and u.source = 2 and u.element in (28,25))
LEFT JOIN `contact_operations` o ON (o.id = u.element_id)
LEFT JOIN `plans` p ON (p.id = o.operation_id and o.operation = 1) or (p.id = u.element_id and u.source = 1 and u.element = 3)
LEFT JOIN `contact_forms` con ON (con.id = o.operation_id and o.operation = 2) or (con.id = u.element_id and u.source = 1 and u.element = 21)
LEFT JOIN `clickable_links` cli ON (cli.id = o.operation_id and o.operation = 3) or (cli.id = u.element_id and u.source = 1 and u.element = 7)

LEFT JOIN `contacts` cont ON (cont.id = (u.element_id)) 

LEFT JOIN `contact_form_fields` formfields ON (formfields.id = u.element_id)
LEFT JOIN `custom_fields` custom_fields on (custom_fields.id = formfields.field_id)
WHERE ((u.user_id = cont.id and u.source = 3 and u.element in (20,21,3,7,22)) or (u.element_id = cont.id and u.source = 3 and u.element in (20,25)) or (((p.account_id = :id) or (con.account_id = :id) or (cli.account_id = :id)) and u.element in (28,20) and u.source IN (2,3)) or (custom_fields.account_id = :id and u.element = 22 and u.source IN (2,3)) or (u.element_id = o.contact_id and u.element = 25 and u.source = 3))
". (!empty($filters) ? 'AND' : ''). implode(' AND ',$filters)."

". (!empty($sort) ? 'order BY ' : ''). implode(' ,',$sort).";";


$RAW_QUERY5 = "SELECT  u.*, us.id as agent_id, us.firstname as user_firstname, us.lastname as user_lastname, cont.id as contact_id, cont.name as contact_username, cont.firstname as contact_firstname, cont.lastname as contact_lastname, p.name as plan_name, cli.name as link_name, con.friendly_name as form_name, custom_fields.field_name as field_name, o.operation as operation
FROM `user_logs` u 
LEFT JOIN `user` us ON (us.id = u.user_id and u.source = 2 and u.element in (28,25))
LEFT JOIN `contact_operations` o ON (o.id = u.element_id)
LEFT JOIN `plans` p ON (p.id = o.operation_id and o.operation = 1) or (p.id = u.element_id and u.source = 3)
LEFT JOIN `contact_forms` con ON (con.id = o.operation_id and o.operation = 2)
LEFT JOIN `clickable_links` cli ON (cli.id = o.operation_id and o.operation = 3)

LEFT JOIN `contacts` cont ON (cont.id = (u.element_id)) 

LEFT JOIN `contact_form_fields` formfields ON (formfields.id = u.element_id)
LEFT JOIN `custom_fields` custom_fields on (custom_fields.id = formfields.field_id)
WHERE ((u.user_id = cont.id and u.source = 3 and u.element in (20,21,3,7,22)) or (u.element_id = cont.id and u.source = 3 and u.element in (20,25)) or (((p.account_id = :id) or (con.account_id = :id) or (cli.account_id = :id)) and u.element in (28,20) and u.source IN (2,3)) or (custom_fields.account_id = :id and u.element = 22 and u.source IN (2,3)) or (u.element_id = o.contact_id and u.element = 25 and u.source = 3))";
                    $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY3);
                    $stmt1 = $entityManagerInterface->getConnection()->prepare($RAW_QUERY4);
                    //  dd($stmt1);

                    $stmt2 = $entityManagerInterface->getConnection()->prepare($RAW_QUERY5);
                    foreach($filterValues AS $key => $value) {
                        $stmt->bindValue($key, $value);
                        $stmt1->bindValue($key, $value);
                        }
                        // $statement->bindValue('searchTerm', '%' . $search . '%');
                        $stmt->bindValue('limit', $length, \PDO::PARAM_INT);
                        $stmt->bindValue('offset', $start, \PDO::PARAM_INT);
                    $stmt->bindValue('id', $user->accountId);
                    $stmt1->bindValue('id', $user->accountId);
                    $stmt2->bindValue('id', $user->accountId);
                    $result = $stmt->executeQuery()->fetchAllAssociative();
                    $results1 = $stmt1->executeQuery()->rowCount();
                    $results3 = $stmt2->executeQuery()->rowCount();


                    return new JsonResponse([
                    'draw' => $draw,
                    'recordsTotal' => $results3,
                    'recordsFiltered' => $results1,
                    'data' => $result,
                
                    ]);
    }
}
