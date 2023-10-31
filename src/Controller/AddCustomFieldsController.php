<?php

namespace App\Controller;

use App\Entity\CustomFieldListValues;
use App\Entity\CustomFields;
use App\Entity\UserLogs;
use App\Repository\AccountsRepository;
use App\Repository\ContactFormsRepository;
use App\Repository\CustomFieldsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddCustomFieldsController extends AbstractController
{

    #[Route('/api/custom_fields/getDataCustomfields', name: 'app_get_data_custom_fields')]
    public function getDataCustomfields(Request $request, EntityManagerInterface $entityManagerInterface, CustomFieldsRepository $customFieldsRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $ids = $data;
        $idsinsql="";
        foreach ($ids as $key => $id) {
            if ($key > 0) {
                $idsinsql .= ", "; 
            }
            $idsinsql .= $id;
        }

        $RAW_QUERY2 = "SELECT  GROUP_CONCAT(cflv.value SEPARATOR '##') AS list_values_select ,cf.*
        FROM `custom_fields` AS cf
        LEFT JOIN `custom_field_list_values` AS cflv ON cflv.custom_field_id = cf.id  
        WHERE cf.status = 1  AND cf.id IN ( ".$idsinsql." ) 
        GROUP BY cf.id";
        ///and cf.account_id =:account
        

        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
      //  $stmt->bindValue('account', $request->attributes->get('account'));
     

        $result1 = $stmt->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'success' => true,
            'data' => $result1,
            
        ]);
    }


    #[Route('/api/getContactFormsById/{id}', name: 'app_get_contact_forms_by_id')]
    public function getContactFormsById(  $id,Request $request, EntityManagerInterface $entityManagerInterface, ContactFormsRepository $contactFormsRepository): Response
    {

        $onecontactform = $contactFormsRepository->find($id);
        $RAW_QUERY2 = "SELECT  GROUP_CONCAT(cflv.value SEPARATOR '##') AS list_values_select ,cf.*
        FROM `contact_form_fields` AS cff
        LEFT JOIN `custom_fields` AS cf ON cf.id = cff.field_id  
        LEFT JOIN `custom_field_list_values` AS cflv ON cflv.custom_field_id = cf.id  
        WHERE    cff.form_id=:id  and  cff.status=1 and cf.status = 1  AND cf.id 
        GROUP BY cf.id";
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        $stmt->bindValue('id', $id);
      $result1 = $stmt->executeQuery()->fetchAllAssociative();
     

    
        return new JsonResponse([
            'success' => true,
            'data' => $onecontactform,
            'form_fields' => $result1,
        ]);
    }



    


    #[Route('/add_custom_fields', name: 'app_add_custom_fields')]
    public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface, AccountsRepository $accountsRepository): Response
    {
        $customFields = new CustomFields();
        $data = json_decode($request->getContent(), true);
        //dump($data);
        $account = $accountsRepository->find($data['account']);
        // $date = DateTime::createFromFormat('Y-m-d', $data['dateStart']);
        // $datediscount = DateTime::createFromFormat('Y-m-d', $data['discountdateStart']);

        $customFields->account = $account;
        $customFields->field_name = $data['fieldName'];
        $customFields->date_start = new \DateTimeImmutable();
        $customFields->status = "1";
        $customFields->field_type = $data['fieldType'];




        $entityManagerInterface->persist($customFields);
        $entityManagerInterface->flush();
        if ($data['fieldType'] == '12') {
            if (is_array($data['fieldvalue'])) {
                $limit = 50;

                foreach ($data['fieldvalue'] as $value) {
                    if ($limit <= 0) {
                        break;
                    }
                    $CustomFieldListValues = new CustomFieldListValues();
                    $CustomFieldListValues->value = $value;
                    $CustomFieldListValues->custom_field_id = $customFields->id;
                    $entityManagerInterface->persist($CustomFieldListValues);
                    $entityManagerInterface->flush();
                    $limit--;
                }
            }
        }

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 9;
        $logs->action = 'create';
        $logs->element_id = $customFields->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();
        return new JsonResponse([
            'success' => true,
            'data' => $customFields,

        ]);
    }
}
