<?php

namespace App\Controller;

use App\Entity\ContactOperations;
use App\Entity\UserLogs;
use App\Repository\PlansRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[AsController]
class getPlansController extends AbstractController
{
    public function __construct(private PlansRepository $plansRepository)
    {
    }

    public function __invoke(): array
    {

        return $this->plansRepository->findAll('planDiscounts');
    }

    #[Route('/plan_user')]
    public function getPlanByUser(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        // dd($request->headers->get('account'));
        $data = json_decode($request->getContent(), true);
        // $key = $request->headers->get('key');

        // $RAW_QUERY1 = 'SELECT * FROM accounts WHERE app_key = :key';
        // $stmt1 = $entityManagerInterface->getConnection()->prepare($RAW_QUERY1);
        // $stmt1->bindValue('key', $key);
        // $result = $stmt1->executeQuery()->fetchAllAssociative();

        $querry1 = $querry2 = [];

        if ($request->query->get('agent_id') != null) {
            $querry1[] = ' pr.u_type = 1 AND pr.id = :agent_id';
        }
        if ($request->query->get('status') != null) {
            $querry2[] = ' p.status = :status';
        }

        $RAW_QUERY2 = "SELECT p.*
                FROM `plans` AS p
                LEFT JOIN `plan_users` AS pu ON p.id = pu.plan_id 
                LEFT JOIN `profiles` AS pr ON pr.u_id = pu.user_id
                WHERE (p.account_id = :account and p.date_start <= CURDATE() and (p.date_end >= CURDATE() or p.date_end is null))" . (!empty($querry1) ? 'AND' : '') . implode(' AND', $querry1) . " " . (!empty($querry2) ? 'AND' : '') . implode(' AND', $querry2) . "
                group by p.id
                ;";

        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        $stmt->bindValue('account', $request->attributes->get('account'));
        if (!empty($querry1)) {
            $stmt->bindValue('agent_id', $request->query->get('agent_id'));
        } else if (!empty($querry2)) {
            $stmt->bindValue('status', $request->query->get('status'));
        }
        $result1 = $stmt->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'success' => 'true',
            'data' => $result1
        ]);
        // if ((isset($data['agent_id']) && $data['agent_id'] !== '') && (isset($data['status']) && $data['status'] !== '')) {
        //     $RAW_QUERY2 = "SELECT p.*, GROUP_CONCAT(pu.user_id SEPARATOR ',') as user_id
        //         FROM `plans` AS p
        //         LEFT JOIN `plan_users` AS pu ON p.id = pu.plan_id
        //         LEFT JOIN `profiles` AS pr ON pr.u_id = pu.user_id
        //         WHERE p.account_id = :account and p.status = :status
        //             AND p.date_start <= CURDATE()
        //             AND (p.date_end >= CURDATE() OR p.date_end IS NULL)
        //             AND (pr.u_type = 1 AND pr.id = :agent_id)
        //             group by p.id
        //             ";

        //     $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        //     $stmt->bindValue('account', $request->attributes->get('account'));
        //     $stmt->bindValue('agent_id', $data['agent_id']);
        //     $stmt->bindValue('status', $data['status']);
        //     $result1 = $stmt->executeQuery()->fetchAllAssociative();

        //     return new JsonResponse([
        //         'success' => 'true',
        //         'data' => $result1
        //     ]);
        // } else if ((isset($data['agent_id']) && $data['agent_id'] !== '')){
        //     $RAW_QUERY2 = "SELECT p.*, GROUP_CONCAT(pu.user_id SEPARATOR ',') as user_id
        //     FROM `plans` AS p
        //     LEFT JOIN `plan_users` AS pu ON p.id = pu.plan_id
        //     LEFT JOIN `profiles` AS pr ON pr.u_id = pu.user_id
        //     WHERE p.account_id = :account
        //         AND p.date_start <= CURDATE()
        //         AND (p.date_end >= CURDATE() OR p.date_end IS NULL)
        //         AND (pr.u_type = 1 AND pr.id = :agent_id)
        //         group by p.id
        //         ";

        // $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        // $stmt->bindValue('account', $request->attributes->get('account'));
        // $stmt->bindValue('agent_id', $data['agent_id']);
        // $result1 = $stmt->executeQuery()->fetchAllAssociative();

        // return new JsonResponse([
        //     'success' => 'true',
        //     'data' => $result1
        // ]);
        // }else if ((isset($data['status']) && $data['status']!== '')){
        //     $RAW_QUERY2 = "SELECT p.*, GROUP_CONCAT(pu.user_id SEPARATOR ',') as user_id
        //     FROM `plans` AS p
        //     LEFT JOIN `plan_users` AS pu ON p.id = pu.plan_id
        //     WHERE p.account_id = :account and p.status = :status
        //         AND p.date_start <= CURDATE()
        //         AND (p.date_end >= CURDATE() OR p.date_end IS NULL)
        //         group by p.id
        //         ";

        // $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        // $stmt->bindValue('account', $request->attributes->get('account'));
        // $stmt->bindValue('status', $data['status']);
        // $result1 = $stmt->executeQuery()->fetchAllAssociative();

        // return new JsonResponse([
        //     'success' => 'true',
        //     'data' => $result1
        // ]);
        // }
        // else {
        //     $RAW_QUERY2 = "SELECT p.*, GROUP_CONCAT(pu.user_id SEPARATOR ',') as user_id
        //         FROM `plans` AS p
        //         LEFT JOIN `plan_users` AS pu
        //         ON p.id = pu.plan_id 
        //         WHERE (p.account_id = :account and p.date_start <= CURDATE() and (p.date_end >= CURDATE() or p.date_end is null))
        //         group by p.id
        //         ;";

        //     $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        //     $stmt->bindValue('account', $request->attributes->get('account'));
        //     $result1 = $stmt->executeQuery()->fetchAllAssociative();
        //     return new JsonResponse([
        //         'success' => 'true',
        //         'data' => $result1
        //     ]);
        // }


        // $RAW_QUERY2 ='SELECT p.*
        // FROM `plans` AS p
        // LEFT JOIN `plan_users` AS pu
        //     ON p.id = pu.plan_id 
        // WHERE (p.status = 1 and p.date_start <= CURDATE() and (p.date_end >= CURDATE() or p.date_end is null )) AND ((pu.user_id = :id OR pu.plan_id IS NULL);';

        // $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        // $stmt->bindValue('id', $id);
        // $result = $stmt->executeQuery()->fetchAllAssociative();
        // return new JsonResponse([
        //     'success' => 'true',
        //     'data' => $result
        // ]);

    }

    #[Route('/link_user')]
    public function getlinkByUser(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {



        // $RAW_QUERY2 ='SELECT l.* FROM `clickable_links` AS l
        // WHERE( EXISTS
        //     (
        //         SELECT pu.* from `clickable_links_users` AS lu
        //         WHERE
        //             lu.user_id  = :id AND l.id = lu.link_id
        //     ))
        //     or 
        //     (l.ID not in (select lus.link_id from `clickable_links_users` AS lus)) and l.status = 1;';



        // $RAW_QUERY2 = 'SELECT l.* FROM `clickable_links` AS l
        //             LEFT JOIN `clickable_links_users` AS lu
        //             LEFT JOIN `profiles` AS pr ON pr.u_id = lu.user_id
        //                 ON l.id = lu.link_id
        //             WHERE (l.status = 1 and l.date_start <= CURDATE() and (l.date_end >= CURDATE() or l.date_end is null )) AND l.account_id = :account AND (pr.u_type = 1 AND pr.id = :agent_id);';

        // $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        // $stmt->bindValue('account', $request->attributes->get('account'));
        // $stmt->bindValue('agent_id', $data['agent_id']);
        // $result = $stmt->executeQuery()->fetchAllAssociative();
        // return new JsonResponse([
        //     'success' => 'true',
        //     'data' => $result
        // ]);
        $data = json_decode($request->getContent(), true);

        $querry1 = $querry2 = [];

        if ($request->query->get('agent_id') != null) {
            $querry1[] = ' pr.u_type = 1 AND pr.id = :agent_id';
        }
        if ($request->query->get('status') != null) {
            $querry2[] = ' l.status = :status';
        }

        $RAW_QUERY2 = "SELECT l.* FROM `clickable_links` AS l
        LEFT JOIN `clickable_links_users` AS lu ON l.id = lu.link_id and lu.status = 1
        LEFT JOIN `profiles` AS pr ON pr.u_id = lu.user_id
        WHERE  l.account_id = :account " . (!empty($querry1) ? 'AND' : '') . implode(' AND', $querry1) . " " . (!empty($querry2) ? 'AND' : '') . implode(' AND', $querry2) . "
        group by l.id
        ";
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        $stmt->bindValue('account', $request->attributes->get('account'));
        if (!empty($querry1)) {
            $stmt->bindValue('agent_id', $request->query->get('agent_id'));
        }
        if (!empty($querry2)) {
            $stmt->bindValue('status', $request->query->get('status'));
        }
        $result1 = $stmt->executeQuery()->fetchAllAssociative();

        return new JsonResponse([
            'success' => 'true',
            'data' => $result1
        ]);

        // if ((isset($data['agent_id']) && $data['agent_id'] !== '') && (isset($data['status']) && $data['status'] !== '')) {
        //     $RAW_QUERY2 = "SELECT l.*, GROUP_CONCAT(lu.user_id SEPARATOR ',') as user_id FROM `clickable_links` AS l 
        //     LEFT JOIN `clickable_links_users` AS lu ON l.id = lu.link_id
        //     LEFT JOIN `profiles` AS pr ON pr.u_id = lu.user_id
        //     WHERE l.status = :status AND l.account_id = :account AND (pr.u_type = 1 AND pr.id = :agent_id) and lu.status = 1
        //     group by l.id
        //     ";

        //     $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        //     $stmt->bindValue('account', $request->attributes->get('account'));
        //     $stmt->bindValue('agent_id', $data['agent_id']);
        //     $stmt->bindValue('status', $data['status']);
        //     $result1 = $stmt->executeQuery()->fetchAllAssociative();

        //     return new JsonResponse([
        //         'success' => 'true',
        //         'data' => $result1
        //     ]);
        // }else if(isset($data['agent_id']) && $data['agent_id'] !== ''){
        //     $RAW_QUERY2 = "SELECT l.*, GROUP_CONCAT(lu.user_id SEPARATOR ',') as user_id FROM `clickable_links` AS l 
        //     LEFT JOIN `clickable_links_users` AS lu ON l.id = lu.link_id
        //     LEFT JOIN `profiles` AS pr ON pr.u_id = lu.user_id

        //     WHERE  l.account_id = :account AND (pr.u_type = 1 AND pr.id = :agent_id) and lu.status = 1
        //     group by l.id
        //     ";

        //     $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        //     $stmt->bindValue('account', $request->attributes->get('account'));
        //     $stmt->bindValue('agent_id', $data['agent_id']);
        //     $result1 = $stmt->executeQuery()->fetchAllAssociative();

        //     return new JsonResponse([
        //         'success' => 'true',
        //         'data' => $result1
        //     ]);
        // }else if (isset($data['status']) && $data['status'] !== ''){
        //     $RAW_QUERY2 = "SELECT l.*, GROUP_CONCAT(lu.user_id SEPARATOR ',') as user_id FROM `clickable_links` AS l 
        //     LEFT JOIN `clickable_links_users` AS lu ON l.id = lu.link_id
        //     WHERE l.status = :status AND l.account_id = :account and lu.status = 1
        //     group by l.id
        //     ";

        //     $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        //     $stmt->bindValue('account', $request->attributes->get('account'));
        //     $stmt->bindValue('status', $data['status']);
        //     $result1 = $stmt->executeQuery()->fetchAllAssociative();

        //     return new JsonResponse([
        //         'success' => 'true',
        //         'data' => $result1
        //     ]);
        // }else {
        //     $RAW_QUERY2 = "SELECT l.*, GROUP_CONCAT(lu.user_id SEPARATOR ',') as user_id FROM `clickable_links` AS l
        //     LEFT JOIN `clickable_links_users` AS lu ON l.id = lu.link_id
        //     WHERE  l.account_id = :account and lu.status = 1
        //     group by l.id
        //     ";

        //     $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        //     $stmt->bindValue('account', $request->attributes->get('account'));
        //     if(!empty($querry1)){
        //         $stmt->bindValue('agent_id', $data['agent_id']);
        //     } else if(!empty($querry2)){
        //         $stmt->bindValue('status', $data['status']);
        //     }
        //     $result1 = $stmt->executeQuery()->fetchAllAssociative();
        //     return new JsonResponse([
        //         'success' => 'true',
        //         'data' => $result1
        //     ]);
        // }
    }

    #[Route('/text_user')]
    public function getTextByUser(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {



        // $RAW_QUERY2 ='SELECT t.* FROM `predefind_texts` AS t
        // WHERE( EXISTS
        //     (
        //         SELECT tu.* from `predefined_text_users` AS tu
        //         WHERE
        //             tu.user_id  = :id AND t.id = tu.text_id
        //     ))
        //     or 
        //     (t.ID not in (select tus.text_id from `clickable_links_users` AS tus)) and t.status = 1;';



        // $RAW_QUERY2 = 'SELECT t.* , tu.user_id FROM `predefind_texts` AS t
        //             LEFT JOIN `predefined_text_users` AS tu
        //                 ON t.id = tu.text_id 
        //                 LEFT JOIN `profiles` AS pr ON pr.u_id = tu.user_id
        //             WHERE (t.status = 1 and t.date_start <= CURDATE() and (t.date_end >= CURDATE() or t.date_end is null )) AND t.account_id = :accountid AND (pr.u_type = 1 AND pr.id = :agent_id);';

        // $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        // $stmt->bindValue('id', $id);
        // $stmt->bindValue('accountid', $accountid);
        // $result = $stmt->executeQuery()->fetchAllAssociative();
        // return new JsonResponse([
        //     'success' => 'true',
        //     'data' => $result
        // ]);


        $data = json_decode($request->getContent(), true);
        $query1 = $query2 = [];
        if ($request->query->get('agent_id') != null) {
            $query1[] = ' (pr.u_type = 1 AND pr.id = :agent_id AND up.pre_defined_messages = 1)';
        }
        if ($request->query->get('status') != null) {
            $query2[] = ' (t.status = :status)';
        }

        $rawQuery = "SELECT DISTINCT t.*, up.pre_defined_messages
            FROM `predefind_texts` AS t
            LEFT JOIN `predefined_text_users` AS tu ON t.id = tu.text_id
            LEFT JOIN `profiles` AS pr ON pr.u_id = tu.user_id
            LEFT JOIN `user_permissions` AS up ON up.user_id = tu.user_id
            WHERE t.account_id = :account 
            " . (!empty($query1) ? 'AND' : '') . implode(' AND ', $query1) . "
            " . (!empty($query2) ? 'AND' : '') . implode(' AND ', $query2) . " 
            group by t.id
            ";

        $stmt = $entityManagerInterface->getConnection()->prepare($rawQuery);
        //dd($stmt);
        $stmt->bindValue('account', $request->attributes->get('account'));
        if (!empty($query1)) {
            $stmt->bindValue('agent_id', $request->query->get('agent_id'));
        }
        if (!empty($query2)) {
            $stmt->bindValue('status', $request->query->get('status'));
        }
        $result = $stmt->executeQuery()->fetchAllAssociative();

        return new JsonResponse([
            'success' => 'true',
            'data' => $result
        ]);
        // if ((isset($data['agent_id']) && $data['agent_id'] !== '') && (isset($data['status']) && $data['status'] !== '') ) {
        //     $RAW_QUERY2 = 'SELECT t.* , tu.user_id FROM `predefind_texts` AS t
        //             LEFT JOIN `predefined_text_users` AS tu
        //                 ON t.id = tu.text_id 
        //                 LEFT JOIN `profiles` AS pr ON pr.u_id = tu.user_id
        //             WHERE (t.status = 1 and t.date_start <= CURDATE() and (t.date_end >= CURDATE() or t.date_end is null )) AND t.account_id = :account AND (pr.u_type = 1 AND pr.id = :agent_id);';

        //     $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        //     $stmt->bindValue('account', $request->attributes->get('account'));
        //     $stmt->bindValue('agent_id', $data['agent_id']);
        //     $stmt->bindValue('status', $data['status']);
        //     $result1 = $stmt->executeQuery()->fetchAllAssociative();

        //     return new JsonResponse([
        //         'success' => 'true',
        //         'data' => $result1
        //     ]);
        // } else {
        //     $RAW_QUERY2 = "SELECT t.* , GROUP_CONCAT(tu.user_id SEPARATOR ',') as user_id FROM `predefind_texts` AS t
        //     LEFT JOIN `predefined_text_users` AS tu
        //         ON t.id = tu.text_id 
        //         WHERE (t.status = 1 and t.date_start <= CURDATE() and (t.date_end >= CURDATE() or t.date_end is null )) AND t.account_id = :account
        //         group by t.id
        //         ";

        //     $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        //     $stmt->bindValue('account', $request->attributes->get('account'));
        //     $result1 = $stmt->executeQuery()->fetchAllAssociative();
        //     return new JsonResponse([
        //         'success' => 'true',
        //         'data' => $result1
        //     ]);
        // }
    }

    #[Route('/presentationUser')]
    public function getPresentationByUser(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $data = json_decode($request->getContent(), true);
        $id = intval($data['id']);

        $stmt = $entityManagerInterface->getConnection()->prepare("SELECT p.id, p.firstname, p.lastname, pr.*
        FROM `user` AS p
        INNER JOIN `user_presentations` AS pr ON p.id = pr.user_id 
        WHERE p.id = :id");
        $stmt->bindValue('id', $id);
        $result1 = $stmt->executeQuery()->fetchAllAssociative();

        return new JsonResponse([
            'success' => 'true',
            'data' => $result1
        ]);
    }

    #[Route('/presentationUsers')]
    public function getPresentationByUsers(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $data = json_decode($request->getContent(), true);
        $id = intval($data['account_id']);

        $stmt = $entityManagerInterface->getConnection()->prepare("SELECT p.id AS UserID, p.firstname, p.lastname, pr.*
        FROM `user` AS p
        INNER JOIN `user_presentations` AS pr ON p.id = pr.user_id 
        WHERE p.account_id = :id");
        $stmt->bindValue('id', $id);
        $result1 = $stmt->executeQuery()->fetchAllAssociative();

        return new JsonResponse([
            'success' => 'true',
            'total' => sizeof($result1),
            'data' => $result1
        ]);
    }

    #[Route('/form_user')]
    public function getformByUser(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {



        // $RAW_QUERY2 ='SELECT t.* FROM `predefind_texts` AS t
        // WHERE( EXISTS
        //     (
        //         SELECT tu.* from `predefined_text_users` AS tu
        //         WHERE
        //             tu.user_id  = :id AND t.id = tu.text_id
        //     ))
        //     or 
        //     (t.ID not in (select tus.text_id from `clickable_links_users` AS tus)) and t.status = 1;';



        // $RAW_QUERY2 = 'SELECT t.* , tu.user_id FROM `predefind_texts` AS t
        //             LEFT JOIN `predefined_text_users` AS tu
        //                 ON t.id = tu.text_id 
        //                 LEFT JOIN `profiles` AS pr ON pr.u_id = tu.user_id
        //             WHERE (t.status = 1 and t.date_start <= CURDATE() and (t.date_end >= CURDATE() or t.date_end is null )) AND t.account_id = :accountid AND (pr.u_type = 1 AND pr.id = :agent_id);';

        // $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        // $stmt->bindValue('id', $id);
        // $stmt->bindValue('accountid', $accountid);
        // $result = $stmt->executeQuery()->fetchAllAssociative();
        // return new JsonResponse([
        //     'success' => 'true',
        //     'data' => $result
        // ]);

        $data = json_decode($request->getContent(), true);

        $query1 = $query2 = [];

        if ($request->query->get('agent_id') != null) {
            $query1[] = ' (pr.u_type = 1 AND pr.id = :agent_id)';
        }

        if ($request->query->get('status') != null) {
            $query2[] = ' (t.status = :status)';
        }

        $rawQuery = "SELECT DISTINCT t.* , f.field_name , f.field_type , f.id as field_id
                     FROM contact_forms AS t 
                     LEFT JOIN contact_form_fields AS c ON c.form_id = t.id AND c.status = 1 
                     LEFT JOIN custom_fields AS f ON f.id = c.field_id 
                     LEFT JOIN profiles AS pr ON FIND_IN_SET(pr.u_id, t.sendable_agents) 
                     WHERE t.account_id = :account AND t.form_type = 4 " . (!empty($query1) ? 'AND' : '') . implode(' AND ', $query1) . " " . (!empty($query2) ? 'AND' : '') . implode(' AND ', $query2) . " ";

        $stmt = $entityManagerInterface->getConnection()->prepare($rawQuery);
        $stmt->bindValue('account', $request->attributes->get('account'));
        if (!empty($query1)) {
            $stmt->bindValue('agent_id', $request->query->get('agent_id'));
        }
        if (!empty($query2)) {
            $stmt->bindValue('status', $request->query->get('status'));
        }

        $result = $stmt->executeQuery()->fetchAllAssociative();

        $combinedData = [];

        foreach ($result as $row) {
            $formId = $row['id'];

            if (!isset($combinedData[$formId])) {
                $combinedData[$formId] = [
                    'id' => $row['id'],
                    'account_id' => $row['account_id'],
                    'form_type' => $row['form_type'],
                    'text_capture' => $row['text_capture'],
                    'sendable_agents' => $row['sendable_agents'],
                    'status' => $row['status'],
                    'friendly_name' => $row['friendly_name'],
                    'fields' => [],
                ];
            }

            $combinedData[$formId]['fields'][] = [
                'id' => $row['field_id'],
                'field_name' => $row['field_name'],
                'field_type' => $row['field_type'],
            ];
        }

        $responseData = array_values($combinedData);

        return new JsonResponse([
            'success' => 'true',
            'data' => $responseData,
        ]);


        // if (isset($data['agent_id']) && $data['agent_id'] !== '') {
        //     $RAW_QUERY2 = 'SELECT t.*
        //     FROM contact_forms AS t
        //     LEFT JOIN profiles AS pr ON FIND_IN_SET(pr.u_id, t.sendable_agents)
        //     WHERE t.status = 1 AND t.account_id = :account AND (pr.u_type = 1 AND pr.id = :agent_id);';

        //     $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        //     $stmt->bindValue('account', $request->attributes->get('account'));
        //     $stmt->bindValue('agent_id', $data['agent_id']);
        //     $result1 = $stmt->executeQuery()->fetchAllAssociative();

        //     return new JsonResponse([
        //         'success' => 'true',
        //         'data' => $result1
        //     ]);
        // } else {
        //     $RAW_QUERY2 = 'SELECT t.* FROM `contact_forms` AS t
        //         WHERE t.status = 1 AND t.account_id = :account;';

        //     $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        //     $stmt->bindValue('account', $request->attributes->get('account'));
        //     $result1 = $stmt->executeQuery()->fetchAllAssociative();
        //     return new JsonResponse([
        //         'success' => 'true',
        //         'data' => $result1
        //     ]);
        // }
    }

    #[Route('/getAll')]
    public function getAll(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {

        // $authorizationHeader = $request->headers->get('Authorization');

        // // Check if the token is present and in the expected format (Bearer TOKEN)
        // if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
        //     throw new AccessDeniedException('Invalid or missing authorization token.');
        // }

        // // Extract the token value (without the "Bearer " prefix)
        // $token = substr($authorizationHeader, 7);

        // $tokenData = $this->get('security.token_storage')->getToken();
        // // dd($tokenData);
        // if ($tokenData === null) {
        //     throw new AccessDeniedException('Invalid token.');
        // }

        // // Now you can access the user data from the token (assuming your User class has a `getUsername()` method)
        // $user = $tokenData->getUser();

        //plans
        $data = json_decode($request->getContent(), true);

        // $plansJson = $request->query->get('items', '[]');
        $plans = $request->query->all('items') ?? [];
        // dd($plans);
        $data = [];
        // dd($plans);
        // Use regular expressions to extract clickable links from the parameter value

        // dd($request->query->get('status'));

   
        if (empty($plans) || in_array("plans", $plans)) {
            $querry1 = $querry2 = [];

            if ($request->attributes->get('agent_id') && !$request->attributes->get('admin')) {
                $querry1[] = ' pr.u_type = 1 AND pr.id = :agent_id';
            }
            if ($request->query->get('status') != null) {
                $querry2[] = ' p.status = :status';
            }

            $RAW_QUERY1 = "SELECT p.*
          FROM `plans` AS p
          LEFT JOIN `plan_users` AS pu ON p.id = pu.plan_id 
          LEFT JOIN `profiles` AS pr ON pr.u_id = pu.user_id
          WHERE (p.account_id = :account and p.date_start <= CURDATE() and (p.date_end >= CURDATE() or p.date_end is null))" . (!empty($querry1) ? 'AND' : '') . implode(' AND', $querry1) . " " . (!empty($querry2) ? 'AND' : '') . implode(' AND', $querry2) . "
          group by p.id
          ;";

            $stmt1 = $entityManagerInterface->getConnection()->prepare($RAW_QUERY1);
            $stmt1->bindValue('account', $request->attributes->get('account'));
            if (!empty($querry1)) {
                $stmt1->bindValue('agent_id', $request->attributes->get('agent_id'));
            }
            if (!empty($querry2)) {
                $stmt1->bindValue('status', $request->query->get('status'));
            }
            $result1 = $stmt1->executeQuery()->fetchAllAssociative();
            $data['plans'] = $result1;
        }
        if (empty($plans) || in_array("predefined_texts", $plans)) {
            $query5 = $query6 = [];
            if ($request->attributes->get('agent_id') && !$request->attributes->get('admin')) {

                $query5[] = ' (pr.u_type = 1 AND pr.id = :agent_id AND up.pre_defined_messages = 1)';
            }
            if ($request->query->get('status') != null) {
                $query6[] = ' (t.status = :status)';
            }

            $rawQuery3 = "SELECT DISTINCT t.*, up.pre_defined_messages
                  FROM `predefind_texts` AS t
                  LEFT JOIN `predefined_text_users` AS tu ON t.id = tu.text_id
                  LEFT JOIN `profiles` AS pr ON pr.u_id = tu.user_id
                  LEFT JOIN `user_permissions` AS up ON up.user_id = tu.user_id
                  WHERE t.account_id = :account 
                  " . (!empty($query5) ? 'AND' : '') . implode(' AND ', $query5) . "
                  " . (!empty($query6) ? 'AND' : '') . implode(' AND ', $query6) . " 
                  group by t.id
                  ";

            $stmt3 = $entityManagerInterface->getConnection()->prepare($rawQuery3);
            //dd($stmt3);
            $stmt3->bindValue('account', $request->attributes->get('account'));
            if (!empty($query5)) {
                $stmt3->bindValue('agent_id', $request->attributes->get('agent_id'));
            }
            if (!empty($query6)) {
                $stmt3->bindValue('status', $request->query->get('status'));
            }
            $result3 = $stmt3->executeQuery()->fetchAllAssociative();
            $data['predefined_texts'] = $result3;
        }

        if (empty($plans) || in_array("clickable_links", $plans)) {
            $querry3 = $querry4 = [];

            if ($request->attributes->get('agent_id') && !$request->attributes->get('admin')) {

                $querry3[] = ' pr.u_type = 1 AND pr.id = :agent_id';
            }
            if ($request->query->get('status') != null) {
                $querry4[] = ' l.status = :status';
            }

            $RAW_QUERY2 = "SELECT l.* FROM `clickable_links` AS l
              LEFT JOIN `clickable_links_users` AS lu ON l.id = lu.link_id and lu.status = 1
              LEFT JOIN `profiles` AS pr ON pr.u_id = lu.user_id
              WHERE  l.account_id = :account " . (!empty($querry3) ? 'AND' : '') . implode(' AND', $querry3) . " " . (!empty($querry4) ? 'AND' : '') . implode(' AND', $querry4) . "
              group by l.id
              ";
            $stmt2 = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
            $stmt2->bindValue('account', $request->attributes->get('account'));
            if (!empty($querry3)) {
                $stmt2->bindValue('agent_id', $request->attributes->get('agent_id'));
            }
            if (!empty($querry4)) {
                $stmt2->bindValue('status', $request->query->get('status'));
            }
            $result2 = $stmt2->executeQuery()->fetchAllAssociative();
            $data['clickable_links'] = $result2;
        }
        if (empty($plans) || in_array("contact_forms", $plans)) {
            $query7 = $query8 = [];
       
            if ($request->attributes->get('agent_id') && !$request->attributes->get('admin')) {

                $query7[] = ' (pr.u_type = 1 AND pr.id = :agent_id)';
            }

            if ($request->query->get('status') != null) {
                $query8[] = ' (t.status = :status)';
            }

            $rawQuery4 = "SELECT   CASE
            WHEN f.field_type = 12 OR f.field_type = 13 THEN  GROUP_CONCAT(DISTINCT  cflv.value SEPARATOR '##') 
            ELSE NULL END AS list_values_select  , t.* , f.field_name , f.field_type , c.id as field_id 
                           FROM contact_forms AS t 
                           LEFT JOIN contact_form_fields AS c ON c.form_id = t.id AND c.status = 1 
                           LEFT JOIN custom_fields AS f ON f.id = c.field_id 
                           LEFT JOIN `custom_field_list_values` AS cflv ON cflv.custom_field_id =  f.id  
                           LEFT JOIN profiles AS pr ON FIND_IN_SET(pr.u_id, t.sendable_agents) 
                           WHERE t.account_id = :account AND t.form_type = 4 " . (!empty($query7) ? 'AND' : '') . implode(' AND ', $query7) . " " . (!empty($query8) ? 'AND' : '') . implode(' AND ', $query8) . "  GROUP BY t.id , c.id , f.id";

            $stmt4 = $entityManagerInterface->getConnection()->prepare($rawQuery4);
            $stmt4->bindValue('account', $request->attributes->get('account'));
            if (!empty($query7)) {
                $stmt4->bindValue('agent_id', $request->attributes->get('agent_id'));
            }
            if (!empty($query8)) {
                $stmt4->bindValue('status', $request->query->get('status'));
            }

            $result = $stmt4->executeQuery()->fetchAllAssociative();

      

            $combinedData = [];
          
            foreach ($result as $row) {
                $formId = $row['id'];

                if (!isset($combinedData[$formId])) {
                    $combinedData[$formId] = [
                        'form_id' => $row['id'],
                        'account_id' => $row['account_id'],
                        'form_type' => $row['form_type'],
                        'text_capture' => $row['text_capture'],
                        'sendable_agents' => $row['sendable_agents'],
                        'status' => $row['status'],
                        'button' => $row['button'],
                        'friendly_name' => $row['friendly_name'],
                        'introduction' => $row['introduction'],
                        'message_capture' => $row['message_capture'],
                        'source' => $row['source'],
                        'agent_status' => $row['agent_status'],
                        'fields' => [],
                    ];
                }

                $listArray = [];
                if ($row['field_type'] == 12 || $row['field_type'] == 13) {
                    if ($row['list_values_select'] !== null) {
                        $listArray = explode('##', $row['list_values_select']);
                    }
                }

                $combinedData[$formId]['fields'][] = [
                    'field_id' => $row['field_id'],
                    'field_name' => $row['field_name'],
                    'field_type' => $row['field_type'],
                    'field_default_value' => $listArray,
                ];
            }

            $responseData = array_values($combinedData);
            $data['contact_forms'] = $responseData;
        }

        if (empty($plans) || in_array("users", $plans)) {
            //  $query9 = [];

            // // if ($request->query->get('agent_id') != null) {
            // //     $query7[] = ' (pr.u_type = 1 AND pr.id = :agent_id)';
            // // }

            // if ($request->query->get('status') != null) {
            //     $query9[] = ' (t.status = :status)';
            // }

            $rawQuery5 = "SELECT DISTINCT u.lastname , u.firstname, pr.id as profile_id
                           FROM user AS u 
                           LEFT JOIN profiles AS pr ON pr.u_id = u.id
                           WHERE u.account_id = :account AND pr.u_type = 1 and u.status = 1 ";

            $stmt5 = $entityManagerInterface->getConnection()->prepare($rawQuery5);
            $stmt5->bindValue('account', $request->attributes->get('account'));

            // if (!empty($query9)) {
            //     $stmt5->bindValue('status', $request->query->get('status'));
            // }

            $result5 = $stmt5->executeQuery()->fetchAllAssociative();


            $data['users'] = $result5;
        }

        return new JsonResponse([
            'success' => 'true',
            'data' => $data
        ]);
        // if ($plans != null) {
        //     if (in_array("plans", $plans)) {
        //     }
        //     return $this->getformByUser($request, $entityManagerInterface);
        // } else {









        //     //clickable_links



        //     //predefind_texts





        //     //contact_forms



        //     return new JsonResponse([
        //         'success' => 'true',
        //         'plans' => $result1,
        //         'clickable_links' => $result2,
        //         'predefind_texts' => $result3,
        //         'contact_forms' => $responseData,
        //     ]);
        // }
    }

    #[Route('/getDataByProfileId/{id}')]
    public function getDataByProfileId(Request $request, $id, EntityManagerInterface $entityManagerInterface): Response
    {

        $sql2 = "SELECT c.firstname , c.lastname , c.email ,c.phone , c.country  FROM `profiles` as p  left join `contacts` as c on c.id = p.u_id  where p.id=:id limit 1";
        $statement2 = $entityManagerInterface->getConnection()->prepare($sql2);
        $statement2->bindValue('id', $id);
        $results = $statement2->executeQuery()->fetchAllAssociative();

        return new JsonResponse([
            'success' => 'true',
            'data' => $results
        ]);
      
    }


    #[Route('/getTotalBalancebyaccount/{id}')]
    public function getTotalBalancebyaccount(Request $request, $id, EntityManagerInterface $entityManagerInterface)
    {

        $sql2 = "SELECT * FROM `profiles` as p WHERE p.id = :id";
        $statement2 = $entityManagerInterface->getConnection()->prepare($sql2);
        $statement2->bindValue('id', $id);
        $results = $statement2->executeQuery()->fetchAllAssociative();

        // dd($results[0]['u_id']);

        if (count($results) > 0) {
            $sql3 = "SELECT SUM(b.balance) as balance, b.balance_type FROM `contact_balances` as b left join `contacts` as c on c.id = :id WHERE b.contact_id = :id and c.account_id = :account GROUP BY b.balance_type having SUM(b.balance) > 0";
            $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
            $statement3->bindValue('id', $results[0]['u_id']);
            $statement3->bindValue('account', $request->attributes->get('account'));
            $results1 = $statement3->executeQuery()->fetchAllAssociative();
            // $contact_balances = $this->ContactBalancesRepository->loadbalancesByContact($id);
            //dd($user);


            return new JsonResponse([
                'success' => true,
                'data' => $results1
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'data' => null
            ]);
        }
    }




    #[Route('/createoperation')]
    public function createoperation(Request $request, EntityManagerInterface $entityManagerInterface): response
    {


        $contact_op = new ContactOperations();
        $data = json_decode($request->getContent(), true);

        // dd($data['contact_id']);
        if (is_array($data['operation_id'])) {
            $operation_id = $data['operation_id'];
        } else {
            $operation_id = [$data['operation_id']];
        }
        foreach ($operation_id  as $i => $value) {

            $contact_op->operation =  $data['operation'];
            $contact_op->operation_id =  $value;
            $contact_op->status = '1';
            $contact_op->contact_id = $data['contact_id'];
            $entityManagerInterface->persist($contact_op);
            $entityManagerInterface->flush();

            $sql3 = "SELECT p.* FROM `profiles` as p 
        LEFT JOIN `user` as u ON p.u_id = u.id
        WHERE p.id = :id and u.status = 1 and p.u_type in (1,3)";
            $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
            $statement3->bindValue('id', $data['user_id']);
            $results = $statement3->executeQuery()->fetchAllAssociative();
            //dd($results[0]['u_id']);
            $logs = new UserLogs();

            $logs->user_id = $results[0]['u_id'];
            $logs->element = 28;
            $logs->action = $data['action'];
            $logs->element_id = $contact_op->id;
            $logs->source = 2;
            $logs->log_date = new \DateTimeImmutable();

            $entityManagerInterface->persist($logs);
            $entityManagerInterface->flush();
        }
        return new JsonResponse([
            'success' => 'true',
            'data' => $logs
        ]);
    }


    #[Route('/contact_details/{id}', methods: ['GET'])]
    public function getContactDetails($id, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        // dd($request->headers->get('account'));
        // $data = json_decode($request->getContent(), true);
        // $key = $request->headers->get('key');

        // $RAW_QUERY1 = 'SELECT * FROM accounts WHERE app_key = :key';
        // $stmt1 = $entityManagerInterface->getConnection()->prepare($RAW_QUERY1);
        // $stmt1->bindValue('key', $key);
        // $result = $stmt1->executeQuery()->fetchAllAssociative();



        $RAW_QUERY2 = "SELECT SUM(b.balance) as balance, b.balance_type, c.gender , c.country , c.name, c.email , c.phone , c.lastname , c.firstname , c.ip_address , c.address , c.date_birth , c.company , p.browser_data
        FROM `profiles` AS p
        LEFT JOIN `contacts` AS c ON p.u_id = c.id
        LEFT JOIN `contact_balances` AS b ON c.id = b.contact_id
        WHERE (c.account_id = :account and p.id = :profile_id and c.status = 1)
        GROUP BY c.id, b.balance_type;";

        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        $stmt->bindValue('account', $request->attributes->get('account'));
        $stmt->bindValue('profile_id', $id);

        $result1 = $stmt->executeQuery()->fetchAllAssociative();
        if ($result1) {

            $combinedData = [];

            foreach ($result1 as $row) {


                if (empty($combinedData)) {
                    $combinedData = [
                        'gender' => $row['gender'],
                        'country' => $row['country'],
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'phone' => $row['phone'],
                        'lastname' => $row['lastname'],
                        'firstname' => $row['firstname'],
                        'ip_address' => $row['ip_address'],
                        'address' => $row['address'],
                        'date_birth' => $row['date_birth'],
                        'company' => $row['company'],
                        'browser_data' => $row['browser_data'],
                        'balance' => [],
                    ];
                }

                $combinedData['balance'][] = [
                    'balance' => $row['balance'],
                    'balance_type' => $row['balance_type']

                ];
            }


            return new JsonResponse([
                'success' => 'true',
                'data' => $combinedData
            ]);
        } else {
            return new JsonResponse([
                'success' => 'false'

            ]);
        }
    }
}
