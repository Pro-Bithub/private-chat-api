<?php

namespace App\Controller;

use App\Entity\ContactLogs;
use App\Entity\UserLogs;
use App\Repository\UserLogsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sinergi\BrowserDetector\Os;
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

        $RAW_QUERY2 = 'SELECT u.*, p.name as plan_name,  cli.name as link_name , cu.field_name as field_name, con.friendly_name as form_name, bal.balance 
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

    #[Route('/add_user_log', name: 'app_add_log_controller')]
    public function updateUserInfo(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $data = json_decode($request->getContent(), true);


        $time =  new \DateTimeImmutable();
        $ContactLogs = new ContactLogs();

        if (isset($data['element_value']) && !empty($data['element_value'])) {
            $ContactLogs->element_value = $data['element_value'];
        }
        if (isset($data['agent_id']) && !empty($data['agent_id'])) {
            $ContactLogs->agent_id = $data['agent_id'];
        }
        if (isset($data['message_id']) && !empty($data['message_id'])) {
            $ContactLogs->message_id = $data['message_id'];
        }
        $ContactLogs->profile_id = $data['profile_id'];
        $ContactLogs->action = $data['action'];
        $ContactLogs->element = $data['element'];
        $ContactLogs->log_date = $time;

        if ($ContactLogs->element === 'chat-page' || $ContactLogs->element === 'chat-wlc-page' || str_starts_with($ContactLogs->element, 'radar'))
            if (isset($data['userAgent']) && !empty($data['userAgent'])) {
                $userAgent = $data['userAgent'];
                $browser = new \Sinergi\BrowserDetector\Browser($userAgent);
                $os = new Os(  $userAgent);
                $browserName = $browser->getName();
                $ContactLogs->browsing_data = $browserName . ';' . $os->getName();
                if (isset($data['myipAdress']) && !empty($data['myipAdress'])) {
                    $ContactLogs->browsing_data =   $ContactLogs->browsing_data . ';' .$data['myipAdress'];
                }

            }



        $entityManagerInterface->persist($ContactLogs);
        $entityManagerInterface->flush();



        return new JsonResponse([
            'success' => true,
        ]);
    }

    #[Route('/get_contact_logs/{id}', name: 'app_get_contact_logs_controller')]
    public function get_contact_logs($id, Request $request, EntityManagerInterface $entityManagerInterface): JsonResponse
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
            $filters[] = "(clg.id LIKE :searchTerm OR clg.action LIKE :searchTerm OR clg.log_date LIKE :searchTerm)";
            $filterValues['searchTerm'] = '%' . trim($request->get('search')['value']) . '%';
        }

        if ($request->get('columns')) {
            foreach ($request->get('columns') as $column) {
                if (isset($column['search']['value']) && trim($column['search']['value']) != '') {
                    $filters[] = "(" . $column['name'] . " LIKE :" . str_replace('.', '_', $column['name']) . ")";
                    $filterValues[str_replace('.', '_', $column['name'])]  = '%' . $column['search']['value'] . '%';;
                }
            }
        }

        if (empty($filters)) {
            $filters[] = ' 1=1';
        }
        /*   return new JsonResponse([
         
            'filterValues' => $filterValues,
         
        ]); */

        $sql1 = "SELECT clg.* , agent.nickname , pt.price ,pt.currency ,pl.name as plan_name , cform.friendly_name as f_friendly_name ,
        sp_elements.value as script_element_value
            FROM contact_logs clg
            left join profiles p on p.id =clg.profile_id
            LEFT JOIN user_presentations agent 
             ON (agent.id = clg.agent_id OR (clg.element  IN ('agent-btn-right-menu', 'agent-btn-from-wlc-page', 'agent-btn-left-menu')  AND agent.id  = clg.element_value ))
            
             LEFT JOIN script_elements sp_elements 
             ON  (clg.element  LIKE 'script-btn-choose-%' ) and  sp_elements.id = CAST(REGEXP_SUBSTR(clg.element, '[0-9]+') AS UNSIGNED)
           
               
             LEFT JOIN contact_forms cform 
             ON  (clg.element  LIKE 'prechat-from-%' ) and  cform.id = CAST(REGEXP_SUBSTR(clg.element, '[0-9]+') AS UNSIGNED)
             
             LEFT JOIN plan_tariffs pt 
             ON  clg.element IN ('btn-buy-plan-from-conversation', 'btn-buy-plan-from-modal','btn-plan-phone-call-from-menu')   and pt.id = clg.element_value 
             LEFT JOIN plans pl        ON  pt.plan_id = pl.id

                 " . (!empty($filters) ? 'where  clg.profile_id = :id and p.account_id = :account_id  and ' : '') . implode(' AND', $filters) . "
                GROUP BY clg.id
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "
                LIMIT :limit OFFSET :offset             
                ;";
        /*       return new JsonResponse([
                    'sql1' => $sql1,
                   
             
                ]); */


        // dd($sql1,$filters,$filterValues);
        $sql2 = "SELECT clg.*
                FROM contact_logs clg
                left join profiles p on p.id =clg.profile_id
               
                " . (!empty($filters) ? 'where clg.profile_id = :id  and p.account_id = :account_id  and ' : '') . implode(' AND', $filters) . "
                GROUP BY clg.id
                " . (!empty($sort) ? 'order BY ' : '') . implode(' ,', $sort) . "
                ;";

        $sql3 = "SELECT clg.*
        FROM contact_logs clg
        left join profiles p on p.id =clg.profile_id
       
         where     clg.profile_id = :id  and p.account_id = :account_id 
        GROUP BY clg.id
        ;";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);

        $statement3->bindValue('id', $id);
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

        $statement->bindValue('id', $id);
        $statement->bindValue('account_id', $user->accountId);

        $statement1->bindValue('id', $id);
        $statement1->bindValue('account_id', $user->accountId);
        $results = $statement->executeQuery()->fetchAllAssociative();
        $results1 = $statement1->executeQuery()->rowCount();
        // $data1 = $this->PredefindTextsRepository->findDataBySearch($search);
        // dd($results);
        // Example usage:
/*             'script-btn-plan-phone-call' => 'Click to Button telephone consultation  (in script)',
   'script-btn-plan-chat' => 'Click to Button submit chat consultation (in script)',*/
        $elementActions = [
            'script-btn-callback' => 'Click to Button  callback (in script)',
            'script-btn-save-callback' => 'Click to Button submit callback (in script)',
            'agent-btn-right-menu' => 'Click to Button Chatter in the agent selector',
            'availableAgent-btn-right-menu' => 'Click to Button Chatter in the offers section',
            'availableAgent-btn-from-wlc-page' => 'Click to Button Chatter in the offers section  (welcome page)',
            'modal-chat-plans' => 'Open plans sidebar',
            'login-form' => 'Login form',
            'forget-password-form' => 'Forget password form',
            'sing-in-two-steps-form' => '2FA verification',
            'chat-wlc-page' => 'Open welcome page',
            'chat-page' => 'Open chat page',
            'mise-en-relation-btn' => 'Click to Button MISE EN RELATION',
            'appointment-btn-right-menu' => 'Click to Button Rendez-vous in the offers section',
            'appointment-btn-from-wlc-page' => 'Click to Button Rendez-vous in the offers section  (welcome page)',
            'planifier-btn-in-modal' => 'Click to Button Planifier in the appointment modal',
            'planifier-btn-in-modal-from-wlc-page' => 'Click to Button Planifier in the appointment modal (welcome page)',
            'call-btn-right-menu' => 'Click to Button Appeler in the offers section',
            'call-btn-from-wlc-page' => 'Click to Button Appeler in the offers section  (welcome page)',
            'agent-btn-left-menu' => 'Click to agent profile in the Left side',
            'agent-btn-from-wlc-page' => 'Click to agent profile in the Left side (welcome page)',
            'btn-buy-plan-from-conversation' => 'Click in the Button Buy in the plan sent by agent',
            'btn-buy-plan-from-modal' => 'Click to Button Buy in the plans sidebar',
            'btn-plan-phone-call-from-menu' => 'Click to Button Buy in the plans sidebar',
            'btn-mode-display' => 'Click to Button Mode display',
            'btn-change-language' => 'Click to Button Change language',
            'btn-profil-form' => 'Click to Button Open Profil sidebar',
            'btn-profile-from' => 'Click to Button Submit in the Profil form',
            'btn-authentification-sidebar' => 'Click to Button Open Authentification sidebar',
            'btn-password' => 'Click to Button Submit in the Authentification form',
            'btn-contactez-nous-sidebar' => 'Click to Button Open Contactez-nous sidebar',
            'btn-contact-form' => 'Click to Button Submit in the Contact form',
            'btn-deconnexion' => 'Click to Button Deconnexion',
            'confirm-password' => 'Confirm password change',
            'btn-linked' => 'Click to Predefined Link sent by agent',
            'radar-rule-reject-users' => 'Reject users (payments)',
            'radar-rule-reject-consecutive-payments' => 'Reject consecutive (payments)',
            'radar-rule-reject-different-country' => 'Reject different country  (payments)',
            'radar-rule-reject-used-browsing-data' => 'Confirm password change  (payments)',
            'btn-login-identity' => 'Click to Button login with identity  (welcome page)',
            'script-from-identity-input'=> 'Fill in the inputs of Identity form',
        ];

        $mappedResults = $this->mapResults($results, $elementActions);



        $elementActions['prechat-from-'] =   'Fill in the inputs of Prechat form';
        $elementActions['auth-from-input-'] =  'Fill in the inputs of Authentification form';
        $elementActions['profile-from-input-'] =  'Fill in the inputs of Profil form';
        $elementActions['appointment-from-input-'] =  'Fill in the inputs of Appointment form';
        $elementActions['script-from-callback-input-'] =  'Fill in the inputs of callback form (in script)';
        $elementActions['script-btn-choose-'] =  'Click to Button choose (in script)';
     

        return new JsonResponse([
            'draw' => $draw,
            'recordsTotal' => $results3,
            'recordsFiltered' => $results1,
            'data' => $mappedResults,
            'elementActions' =>  $elementActions

        ]);
    }
    function mapResults($results, $elementActions)
    {

        $mappedResults = [];


        foreach ($results as $result) {

            $action = 'default';

            $result['field'] = "";
            if (strpos($result['element'], 'prechat-from-') === 0) {
                $field = substr($result['element'], strlen('prechat-from-'));
                $field = str_replace(['-', 'wlc','input'], ' ', $field);
                $field = preg_replace('/[0-9]+/', '', $field);
                $result['field'] = $field;
                $action = 'Fill in the inputs of Prechat form';
            }

            if (strpos($result['element'], 'script-btn-choose-') === 0) {
           /*      $field = substr($result['element'], strlen('prechat-from-'));
                $field = str_replace(['-', 'wlc','input'], ' ', $field);
                $field = preg_replace('/[0-9]+/', '', $field);
                $result['field'] = $field; */
                
                $result['element_value'] =  $result['script_element_value'];
                $action = 'Click to Button choose (in script)';
            }


            

            if (strpos($result['element'], 'auth-from-input-') === 0) {
                $field = substr($result['element'], strlen('auth-from-input-'));
                $field = str_replace(['-', 'wlc'], ' ', $field);
                $result['field'] = $field;
                $action = 'Fill in the inputs of Authentification form';
            }
            if (strpos($result['element'], 'profile-from-input-') === 0) {
                $field = substr($result['element'], strlen('profile-from-input-'));
                $field = str_replace(['-', 'wlc', '_'], ' ', $field);
                $result['field'] = $field;
                $action = 'Fill in the inputs of Profil form';
            }
            if (strpos($result['element'], 'appointment-from-input-') === 0) {
                $field = substr($result['element'], strlen('appointment-from-input-'));
                $field = str_replace(['-', 'wlc'], ' ', $field);
                $result['field'] = $field;
                $action = 'Fill in the inputs of Appointment form';
            }

            if ($result['element'] === 'chat-page' || $result['element'] === 'chat-wlc-page') {

                $result['element_value'] = str_replace(['e'], ' from Email',    $result['element_value']);
                $result['element_value'] = str_replace(['fb'], ' from Facebook',    $result['element_value']);
                $result['element_value'] = str_replace(['s'], ' from Sms',    $result['element_value']);
            }

            if (   $result['element'] === 'btn-login-identity'||  $result['element'] === 'btn-plan-phone-call-from-menu'  || $result['element'] === 'btn-buy-plan-from-modal'  || $result['element'] === 'btn-buy-plan-from-conversation ') {
                $result['element_value'] = '';
            }
          

            if (isset($elementActions[$result['element']])) {
                $action = $elementActions[$result['element']];
            }
            $result['action'] = $action;

            $elementsToClear = [
                'agent-btn-right-menu',
                'agent-btn-left-menu',
                'agent-btn-from-wlc-page',

                'btn-linked'
            ];
            if (in_array($result['element'], $elementsToClear)) {
                $result['element_value'] = '';
            }

            $elementsPlan = [
                'btn-buy-plan-from-conversation',
                'btn-buy-plan-from-modal',
            ];
         /*    if (in_array($result['element'], $elementsPlan))
                if (isset($result['price']) &&  isset($result['currency'])) {
                    $result['element_value'] = $result['price'] . ' ' . $result['currency'];
                } */




            $mappedResults[] = $result;
        }


        return $mappedResults;
    }



    #[Route('/get_scripts', name: 'app_get_scripts_controller')]
    public function get_scripts(Request $request, EntityManagerInterface $entityManagerInterface): JsonResponse
    {

        $sql1 = "SELECT s.*, se.element_type ,se.id as id_script_element,ms.source, ms.id as id_s_m, ms.script_id, ms.type as type_message, ms.content, ms.message_order as order_m, se.message_id, se.element, se.value, se.next
        FROM scripts s
        LEFT JOIN script_messages ms ON ms.script_id = s.id
        LEFT JOIN script_elements se ON se.message_id = ms.id;";
    
    $statement = $entityManagerInterface->getConnection()->prepare($sql1);
    $results = $statement->executeQuery()->fetchAllAssociative();
    
    $groupedResults = [
        'welcome' => [],
        'expired_balance' => [],
        'registration' => []
    ];
    
    $tempResults = [];
    
    foreach ($results as $result) {
        $id_s_m = $result['id_s_m'];
        
        if (!isset($tempResults[$id_s_m])) {
            $tempResults[$id_s_m] = [
                'language' => $result['language'],
                'type' => $result['type'],
                'type_message' => $result['type_message'],
                'content' => $result['content'],
                'account_id' => $result['account_id'],
                'name' => $result['name'],
                'id' => $id_s_m,
                'order_m' => $result['order_m'],
                'source' => $result['source'],
                'elements' => []
            ];
        }
    
        if ($result['id_script_element'] !== null) {
            $tempResults[$id_s_m]['elements'][] = [
                'id_script_element' => $result['id_script_element'],
                'message_id' => $result['message_id'],
                'element' => $result['element'],
                'element_type' => $result['element_type'],
                'value' => $result['value'],
                'next' => $result['next']
            ];
        }
    }
    
    foreach ($tempResults as $tempResult) {
        switch ($tempResult['type']) {
            case 1:
                $groupedResults['welcome'][] = $tempResult;
                break;
            case 2:
                $groupedResults['expired_balance'][] = $tempResult;
                break;
            case 3:
                $groupedResults['registration'][] = $tempResult;
                break;
        }
    }
    


    
        $sql2 = "SELECT o.*
            FROM offres o
                ;";

        $statement2 = $entityManagerInterface->getConnection()->prepare($sql2);
        $results2 = $statement2->executeQuery()->fetchAllAssociative();


        return new JsonResponse([
            'script_object' => $groupedResults,
            'offres' => $results2,
        ]);
    }
}
