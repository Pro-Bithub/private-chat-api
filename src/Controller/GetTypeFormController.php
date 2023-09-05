<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetTypeFormController extends AbstractController
{
#[Route('/GetFormType', name: 'app_get_type_form')]
public function index(EntityManagerInterface $entityManagerInterface): Response
{ 
    
    $sql = "SELECT c.account_id , c.id as form_id, c.form_type, c.text_capture, c.friendly_name, cu.field_name, cu.field_type, cu.id
    FROM `contact_forms` AS c
    LEFT JOIN `contact_form_fields` AS cf ON cf.form_id = c.id
    LEFT JOIN `custom_fields` AS cu ON cu.id = cf.field_id
    WHERE c.form_type IN (1, 2, 3) AND c.status = 1
    ORDER BY c.form_type, c.id  DESC";
    
    $statement = $entityManagerInterface->getConnection()->prepare($sql);
    $contactForms = $statement->executeQuery()->fetchAllAssociative();
    // dd($contactForms);
    $data = [
      
    ];

    foreach ($contactForms as $row) {
        if(empty($data[$row['form_id']])){
                $data[$row['form_id']] = [
                    'form_type' => $row['form_type'],
                    'friendly_name' => $row['friendly_name'],
                    'text_capture' => $row['text_capture'],
                    'form_id' => $row['form_id'],
                    'account_id' => $row['account_id'],
                    'fields' => [],
                ];
                
        }
        
            $data[$row['form_id']]['fields'][] = [
                'field_name' => $row['field_name'],
                'field_type' => $row['field_type'],
                'field_id' => $row['id'],
            ];
    }
              


    return new JsonResponse([
        'success' => true,
        'data' => array_values($data),
    ]);
}



#[Route('/GetFormTypeByAccountId/{id}', name: 'app_Get_formType_ByAccountId_form')]
public function GetFormTypeByAccountId(EntityManagerInterface $entityManagerInterface,$id): Response
{ 
    $sql = "SELECT c.account_id , c.id as form_id, c.form_type, c.text_capture, c.friendly_name, cu.field_name, cu.field_type, cu.id
    FROM `contact_forms` AS c
    LEFT JOIN `contact_form_fields` AS cf ON cf.form_id = c.id
    LEFT JOIN `custom_fields` AS cu ON cu.id = cf.field_id
    WHERE c.form_type IN (1, 2, 3) and c.status = 1 and c.account_id = :account_id
    ORDER BY c.form_type, c.id  DESC";
    
    $statement = $entityManagerInterface->getConnection()->prepare($sql);
    $statement->bindValue('account_id', $id);
    $contactForms = $statement->executeQuery()->fetchAllAssociative();
    // dd($contactForms);
    $data = [
      
    ];

    foreach ($contactForms as $row) {
        if(empty($data[$row['form_id']])){
                $data[$row['form_id']] = [
                    'form_type' => $row['form_type'],
                    'friendly_name' => $row['friendly_name'],
                    'text_capture' => $row['text_capture'],
                    'form_id' => $row['form_id'],
                    'account_id' => $row['account_id'],
                    'fields' => [],
                ];
                
        }
        
            $data[$row['form_id']]['fields'][] = [
                'field_name' => $row['field_name'],
                'field_type' => $row['field_type'],
                'field_id' => $row['id'],
            ];
    }
              


    return new JsonResponse([
        'success' => true,
        'data' => array_values($data),
    ]);
}


#[Route('/getAllAgentsByAccount', methods: ['GET'])]
public function getAllAgentsByAccount(Request $request, EntityManagerInterface $entityManagerInterface): Response
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
    $query2 = [];

    if ($request->query->get('status') != null) {
        $query2[] = ' (u.status = :status)';
    }

    $rawQuery = "SELECT p.id as profile_id , u.id , u.email, u.lastname , u.firstname , u.status, p.user_key
        FROM `user` AS u
        left join `profiles` as p on p.u_id = u.id
         WHERE u.account_id = :account 
        " . (!empty($query2) ? 'AND' : '') . implode(' AND ', $query2) . " 
        ";

    $stmt = $entityManagerInterface->getConnection()->prepare($rawQuery);
    //dd($stmt);
    $stmt->bindValue('account', $request->attributes->get('account'));
    if (!empty($query2)) {
        $stmt->bindValue('status', $request->query->get('status'));
    }
    $result = $stmt->executeQuery()->fetchAllAssociative();

    return new JsonResponse([
        'success' => 'true',
        'data' => $result
    ]);
}
}
