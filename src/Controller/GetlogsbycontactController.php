<?php

namespace App\Controller;

use App\Repository\UserLogsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GetlogsbycontactController extends AbstractController
{
    /**
    * @var UserLogsRepository
    */
    private $UserLogsRepository;
    public function __construct(UserLogsRepository $UserLogsRepository)
    {
        $this->UserLogsRepository = $UserLogsRepository;
    }
    #[Route('/logs_contact')]
    public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        // $logs_user = $this->UserLogsRepository->loadlogsBycontact($id);
        // dd($logs_user);
        // return $logs_user;

        //$em = $doctrine->getManager();
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
        
        $data = [];
        $time = new \DateTime();

    
            //contact_form_fields
            $RAW_QUERY = 'SELECT u.id as log_id, u.user_id, u.action , u.element, u.element_id, u.source , u.log_date , f.field_id, c.field_value, cu.field_name,null as plan_id, null as plan_name, null as currency , null as tariff, null as billing_type, null as billing_volume, null as id_op,null as contact_id, null as operation, null as operation_id , null as link_name , null as url, null as id_form, null as form_type, null as introduction, null as text_capture, null as sendable_agents, null as waiting_time FROM user_logs u  INNER JOIN contact_form_fields f ON f.field_id = u.element_id INNER JOIN contact_custom_fields c ON c.form_field_id = f.id LEFT JOIN custom_fields cu ON cu.id = u.element_id WHERE c.contact_id = :id and u.element = 22 and u.source = 3  
             UNION 
             SELECT u.id as log_id, u.user_id, u.action , u.element, u.element_id, u.source , u.log_date , null as field_id, null as field_value,null as field_name,plan.id  as plan_id, plan.name as plan_name , plan.currency , plan.tariff, plan.billing_type, plan.billing_volume ,o.id as id_op, o.contact_id, o.operation, o.operation_id ,null as links_name , null as url , null as id_form, null as form_type, null as introduction, null as text_capture, null as sendable_agents, null as waiting_time  FROM user_logs u  INNER JOIN contact_operations o ON o.id = u.element_id  INNER JOIN plans plan ON plan.id = o.operation_id  WHERE o.contact_id = :id and o.operation = 1 and u.element = 28 and u.source IN (2,3)   
              UNION 
              SELECT u.id as log_id, u.user_id, u.action , u.element, u.element_id, u.source , u.log_date , null as field_id , null as field_value,null as field_name,null  as plan_id, null as plan_name  , null as currency , null as tariff, null as billing_type, null as billing_volume ,ope.id as id_op, ope.contact_id, ope.operation, ope.operation_id ,null as links_name, null as url , f.id as id_form, f.form_type, f.introduction, f.text_capture, f.sendable_agents, f.waiting_time  FROM user_logs u INNER JOIN contact_operations ope ON ope.id = u.element_id   INNER JOIN contact_forms f ON f.id = ope.operation_id WHERE ope.contact_id = :id and ope.operation = 2 and u.element = 28 and u.source IN (2,3) 
                UNION 
                SELECT u.id as log_id, u.user_id, u.action , u.element, u.element_id, u.source , u.log_date , null as field_id , null as field_value,null as field_name,null  as plan_id, null as plan_name , null as currency , null as tariff, null as billing_type, null as billing_volume , oper.id as id_op, oper.contact_id, oper.operation, oper.operation_id ,l.name as links_name , l.url, null as id_form,null as form_type, null as introduction, null as text_capture, null as sendable_agents, null as waiting_time  FROM user_logs u   INNER JOIN contact_operations oper ON oper.id = u.element_id  INNER JOIN clickable_links l ON l.id = oper.operation_id WHERE oper.contact_id = :id and oper.operation = 3 and u.element = 28 and u.source IN (2,3)  
                 UNION 
                 SELECT u.id as log_id, u.user_id, u.action , u.element, u.element_id, u.source , u.log_date , null as field_id , null as field_value,null as field_name,null as plan_id, null as plan_name , null as currency , null as tariff, null as billing_type, null as billing_volume ,null as id_op, null as contact_id, null as operation, null as operation_id, null as links_name , null as url, null as id_form,  null as form_type, null as introduction, null as text_capture, null as sendable_agents, null as waiting_time  FROM user_logs u  LEFT JOIN notes note ON note.id = u.element_id  INNER JOIN profiles pro ON pro.u_id = :id WHERE note.contact_id = :id and u.element_id = :id and pro.u_id = :id  and u.element IN (20,27,29,30,31)
                  ORDER BY log_date DESC;';

                  $RAW_QUERY1 = 'SELECT u.*, f.*, o.*
                  FROM user_logs u 
                  LEFT JOIN 
                      (CASE WHEN f.field_id = u.element_id THEN table2_1
                       END) contact_form_fields f ON f.field_id = u.element_id WHERE u.element = 22 and u.source = 3 and c.contact_id = :id
                  LEFT JOIN 
                      (CASE WHEN o.operation = 1 THEN table3_1
                       END) contact_operations o ON o.id = u.element_id WHERE c.contact_id = :id u.element = 28 and u.source IN (2,3);';

                    $RAW_QUERY2 ='SELECT u.*, p.name as plan_name,  cli.name as link_name , cu.field_name as field_name, con.friendly_name as form_name, bal.balance 
                    FROM `user_logs` u 
                     
                    LEFT JOIN 
                        `notes` note ON (note.id = u.element_id)
                    LEFT JOIN 
                        `profiles` pro ON pro.id = u.element_id 
                    LEFT JOIN 
                        `contact_balances` bal ON (bal.id = u.element_id and u.element = 24)
                    LEFT JOIN 
                        `contact_form_fields` f ON (f.field_id = u.element_id)
                        LEFT JOIN 
                        `custom_fields` cu ON (cu.id = f.field_id)
                        LEFT JOIN 
                        `contact_custom_fields` c ON (c.form_field_id = f.id )  
                    LEFT JOIN 
                        `contact_operations` o ON (o.id = u.element_id)
                    LEFT JOIN 
                        `plans` p ON (p.id = o.operation_id and o.operation = 1)
                        LEFT JOIN 
                        `contact_forms` con ON (con.id = o.operation_id and o.operation = 2) 
                    LEFT JOIN 
                        `clickable_links` cli ON (cli.id = o.operation_id and o.operation = 3)    
                        

                        where ((bal.contact_id = :id ) or (c.contact_id = :id and u.element = 22 and u.source = 3) or (o.contact_id = :id and u.element = 28 and u.source IN (2,3) ) or (note.contact_id = :id and u.element = 31 ) or (pro.u_id = :id and u.element = 30) or (u.element_id = :id and ((u.element = 20 and u.source in (1,2,3)) or (u.element = 25 and u.source = 3)) or (u.user_id = :id and u.element = 21 and u.source = 3) or (u.user_id = :id and u.element in (21,3,7) and u.source = 3)))
                        ORDER BY u.log_date DESC
                        ;';

                        

                    $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
                    $stmt->bindValue('id', $user->accountId);
                    $result = $stmt->executeQuery()->fetchAllAssociative();
                    return new JsonResponse([
                        'success' => 'true',
                        'data' => $result
                    ]);
            // //contact_plans
            // $RAW_QUERY2 = 'SELECT * FROM user_logs u INNER JOIN contact_operations o ON o.id = u.element_id INNER JOIN plans p ON p.id = o.operation_id WHERE o.contact_id = :id and o.operation = 1 and u.element = 28 and u.source = 2 or u.source = 3;';
            // $stmt2 = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
            // $stmt2->bindValue('id', $id);
            // $result2 = $stmt2->executeQuery()->fetchAllAssociative();

            // //contact_forms
            // $RAW_QUERY3 = 'SELECT * FROM user_logs u INNER JOIN contact_operations o ON o.id = u.element_id INNER JOIN contact_forms f ON f.id = o.operation_id WHERE o.contact_id = :id and o.operation = 2 and u.element = 28 and u.source = 2 or u.source = 3;';
            // $stmt3 = $entityManagerInterface->getConnection()->prepare($RAW_QUERY3);
            // $stmt3->bindValue('id', $id);
            // $result3 = $stmt3->executeQuery()->fetchAllAssociative();

            // //contact_clickable_links
            // $RAW_QUERY4 = 'SELECT * FROM user_logs u INNER JOIN contact_operations o ON o.id = u.element_id INNER JOIN clickable_links l ON l.id = o.operation_id WHERE o.contact_id = :id and o.operation = 3 and u.element = 28 and u.source = 2 or u.source = 3;';
            // $stmt4 = $entityManagerInterface->getConnection()->prepare($RAW_QUERY4);
            // $stmt4->bindValue('id', $id);
            // $result4 = $stmt4->executeQuery()->fetchAllAssociative();
            
            // //contact
            // $RAW_QUERY1 = 'SELECT * FROM user_logs u WHERE u.element_id = :id and u.element = 20;';
            // $stmt1 = $entityManagerInterface->getConnection()->prepare($RAW_QUERY1);
            // $stmt1->bindValue('id', $id);
            // $result1 = $stmt1->executeQuery()->fetchAllAssociative();

           
            // array_push($data, $result, $result1, $result2, $result3, $result4);
            
        // }else if($element == 2){
        

        // $RAW_QUERY1 = 'SELECT * FROM user_logs u WHERE u.element_id = :id and u.element = :element and u.source = 2;';
        // $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY1);
        // $stmt->bindValue('id', $id);
        // $stmt->bindValue('element', $element);
        // $result = $stmt->executeQuery()->fetchAllAssociative();
        // //dd($result);
        // return new JsonResponse([
        //     'success' => 'true',
        //     'data' => $result
        // ]);

    
       // return $result;

        // return $stmt->fetchAll();
        // $statement = $em->getConnection()->prepare($RAW_QUERY);
        // $statement->bindValue('id', $id);
        // $statement->execute();

        // $result = $statement->fetchAll();
        // dd($result);
    }
}
