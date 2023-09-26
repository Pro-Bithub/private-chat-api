<?php

namespace App\Controller;

use App\Entity\ContactFormFields;
use App\Entity\ContactForms;
use App\Entity\UserLogs;
use App\Repository\AccountsRepository;
use App\Repository\ContactFormFieldsRepository;
use App\Repository\ContactFormsRepository;
use App\Repository\CustomFieldsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AddFormController extends AbstractController
{
    public function __invoke(Request $request, ContactFormsRepository $contactFormsRepository, CustomFieldsRepository $customFieldsRepository, EntityManagerInterface $entityManagerInterface, AccountsRepository $accountsRepository, UserRepository $userRepository): Response
    {
        // account: new FormControl('api/accounts/' + this.userdata.account_id),
        // name: new FormControl('', Validators.required),
        // language: new FormControl('', Validators.required),
        // category: new FormControl('', Validators.required),
        // text: new FormControl('', Validators.required),
        // status: new FormControl('1', Validators.required),
        // dateStart: new FormControl(new Date(), Validators.required),
        // PreDefinedTextUser: new FormControl(null),


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
        // $user = $tokenData->getUser();


        $data = json_decode($request->getContent(), true);

        if ($data['formType'] != 4 && $data['formType'] != "4") {
            $sql = "SELECT c.*
            FROM `contact_forms` AS c
            WHERE c.form_type = :form_type AND c.status = 1 and c.account_id = :account_id
            ORDER BY c.form_type, c.id  DESC";
            $statement = $entityManagerInterface->getConnection()->prepare($sql);
            $statement->bindValue('form_type', $data['formType']);
            $statement->bindValue('account_id', $data['account']);
            $contactForms1 = $statement->executeQuery()->fetchAllAssociative();
            //  dd($contactForms1['id']);
            if (is_array($contactForms1)) {
                if (count($contactForms1) > 0) {
                    foreach ($contactForms1 as $contactForm) {
                        $contactForms2 = $contactFormsRepository->find($contactForm['id']);
                        $contactForms2->status = 0;
                        $contactForms2->date_end = new \DateTimeImmutable();
                        $entityManagerInterface->persist($contactForms2);
                        $entityManagerInterface->flush();
                    }
                }
            }
        }
        
     


        $ContactForms = new ContactForms();
        //dump($data);
        $account = $accountsRepository->find($data['account']);




        // $date = DateTime::createFromFormat('Y-m-d', $data['dateStart']);
        // $datediscount = DateTime::createFromFormat('Y-m-d', $data['discountdateStart']);
     
        $ContactForms->source = $data['source'];
        $ContactForms->agent_status = $data['agentStatus'];
        $ContactForms->button = $data['button'];
        $ContactForms->message_capture = $data['messageCapture'];

        $ContactForms->account = $account;
        $ContactForms->form_type = $data['formType'];
        $ContactForms->friendly_name = $data['FriendlyName'];
        $ContactForms->sendable_agents = $data['sendableAgents'];
        $ContactForms->waiting_time = $data['waitingTime'];
        $ContactForms->text_capture = $data['textCapture'];
      //  $ContactForms->text_capture_before = $data['textCaptureBefore'];
        $ContactForms->introduction = $data['introduction'];
        
        $ContactForms->date_start = new \DateTimeImmutable();
        $ContactForms->status = "1";




        $entityManagerInterface->persist($ContactForms);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 21;
        $logs->action = 'create';
        $logs->element_id = $ContactForms->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        $fieldformData = $data['contactFormField'];

        foreach ($fieldformData  as $i => $value) {
            $FormFields = new ContactFormFields();
            $field = $customFieldsRepository->find($value);
            $FormFields->form =  $ContactForms;
            $FormFields->field = $field;
            $FormFields->status = '1';
            $FormFields->date_start = new \DateTimeImmutable();

            $entityManagerInterface->persist($FormFields);
            $entityManagerInterface->flush();

            $logs = new UserLogs();
            $logs->user_id = $data['user_id'];
            $logs->element = 22;
            $logs->action = 'create';
            $logs->element_id = $FormFields->id;
            $logs->source = 1;
            $logs->log_date = new \DateTimeImmutable();

            $entityManagerInterface->persist($logs);
            $entityManagerInterface->flush();
        }

        $rawQuery4 = "SELECT DISTINCT t.* , f.field_name , f.field_type , c.id as field_id 
        FROM contact_forms AS t 
        LEFT JOIN contact_form_fields AS c ON c.form_id = t.id AND c.status = 1 
        LEFT JOIN custom_fields AS f ON f.id = c.field_id 
        LEFT JOIN profiles AS pr ON FIND_IN_SET(pr.u_id, t.sendable_agents) 
        WHERE  t.id = :form_id and t.status = 1";

        $stmt4 = $entityManagerInterface->getConnection()->prepare($rawQuery4);
        // $stmt4->bindValue('account', $data['account']);

        $stmt4->bindValue('form_id', $ContactForms->id);


        $result = $stmt4->executeQuery()->fetchAllAssociative();
        // dd($result);
        // $combinedData = [];




        // if (empty($combinedData)) {
        //     $combinedData = [
        //         'form_id' => $result['id'],
        //         'account_id' => $result['account_id'],
        //         'form_type' => $result['form_type'],
        //         'text_capture' => $result['text_capture'],
        //         'sendable_agents' => $result['sendable_agents'],
        //         'status' => $result['status'],
        //         'friendly_name' => $result['friendly_name'],
        //         'fields' => [],
        //     ];
        // }
        // // foreach ($filesData as $file) {
        // //     $combinedData['fields'][] = [
        // //         'field_id' => $file['field_id'],
        // //         'field_name' => $file['field_name'],
        // //         'field_type' => $file['field_type'],
        // //     ];
        // // }
        
        // $combinedData['fields'][] = [
        //     'field_id' => $result['field_id'],
        //     'field_name' => $result['field_name'],
        //     'field_type' => $result['field_type'],
        // ];


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
                    'source' => $row['source'],
                    'agent_status' => $row['agent_status'],
                    'message_capture' => $row['message_capture'],
                    'button' => $row['button'],
                    'introduction' => $row['introduction'],
                    'waiting_time' => $row['waiting_time'],
                    'fields' => [],
                ];
            }
      
   
        
     
            $combinedData[$formId]['fields'][] = [
                'field_id' => $row['field_id'],
                'field_name' => $row['field_name'],
                'field_type' => $row['field_type'],
            ];
        }



        // $responseData = array_values($combinedData);
        // $data['contact_forms'] = $responseData;

        return new JsonResponse([
            'success' => true,
            'data' => $combinedData,
        ]);
    }

    #[Route('/update_form/{id}', name: 'app_update_form_controller')]
    public function updateForm(
        $id,
        ContactFormsRepository $contactFormsRepository,
        ContactFormFieldsRepository $contactFormFieldsRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        CustomFieldsRepository $customFieldsRepository
    ): Response {


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
        // $user = $tokenData->getUser();
        $data = json_decode($request->getContent(), true);
        
        if ($data['formType'] != 4 && $data['formType'] != "4" && ($data['status'] === "1" ||$data['status'] === 1  )  ) {
            $sql = "SELECT c.*
            FROM `contact_forms` AS c
            WHERE c.form_type = :form_type AND c.status = 1 and c.account_id = :account_id and c.id !=:id
            ORDER BY c.form_type, c.id  DESC";
            $statement = $entityManagerInterface->getConnection()->prepare($sql);
            $statement->bindValue('form_type', $data['formType']);
            $statement->bindValue('account_id', $data['account']);
            $statement->bindValue('id', $id);
            $contactForms1 = $statement->executeQuery()->fetchAllAssociative();
            //  dd($contactForms1['id']);
            if (is_array($contactForms1)) {
                if (count($contactForms1) > 0) {
                    foreach ($contactForms1 as $contactForm) {
                        $contactForms2 = $contactFormsRepository->find($contactForm['id']);
                        $contactForms2->status = 0;
                        $contactForms2->date_end = new \DateTimeImmutable();
                        $entityManagerInterface->persist($contactForms2);
                        $entityManagerInterface->flush();
                    }
                }
            }
        }

        $ContactForms = $contactFormsRepository->find($id);
   
        $ContactForms->source = $data['source'];
        $ContactForms->agent_status = $data['agentStatus'];
        $ContactForms->button = $data['button'];
        $ContactForms->message_capture = $data['messageCapture'];

        $ContactForms->form_type = $data['formType'];
        $ContactForms->friendly_name = $data['FriendlyName'];
        $ContactForms->sendable_agents = $data['sendableAgents'];
        $ContactForms->waiting_time = $data['waitingTime'];
        $ContactForms->text_capture = $data['textCapture'];
      //  $ContactForms->text_capture_before = $data['textCaptureBefore'];
        $ContactForms->introduction = $data['introduction'];
        $ContactForms->status = $data['status'];

        $contactFormField = isset($data['contactFormField']) ? $data['contactFormField'] : [];
        //dd($clickableLinksUser);
        $entityManagerInterface->persist($ContactForms);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 21;
        $logs->action = 'update';
        $logs->element_id = $ContactForms->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        $sql3 = "SELECT t.field_id FROM `contact_form_fields` as t WHERE t.form_id = :id and t.status = 1";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $ContactForms->id);

        $predefinedTextUserIds = $statement3->executeQuery()->fetchAllAssociative();
        // $sql3="SELECT t.user_id FROM `predefined_text_users` as t WHERE t.text_id = :id and t.status = 1";
        //     $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        //     $statement3->bindValue('id', $predefindText->id);
        //     $predefinedTextUserIds = $statement3->executeQuery()->fetchAllAssociative();
        $result = array_column($predefinedTextUserIds, 'field_id');

        $difference2 = array_diff($result, $contactFormField);
        //dd($difference2, $result, $clickableLinksUser);
        if (!empty($difference2)) {
            foreach ($difference2 as $difference) {
                if (in_array($difference, $result)) {
                    $form_field_data = $contactFormFieldsRepository->loadfromByFieldData1($ContactForms->id, $difference);
                    if (!empty($form_field_data)) {
                        $form_field = $form_field_data[0];
                        $form_field->status = '0';
                        $form_field->date_end = new \DateTimeImmutable();

                        $entityManagerInterface->persist($form_field);
                        $entityManagerInterface->flush();
                    }
                } else {
                    $ContactFormFields = new ContactFormFields();
                    $field = $customFieldsRepository->find($difference);
                    $ContactFormFields->form = $ContactForms;
                    $ContactFormFields->field = $field;
                    $ContactFormFields->status = '1';
                    $ContactFormFields->date_start = new \DateTimeImmutable();

                    $entityManagerInterface->persist($ContactFormFields);
                    $entityManagerInterface->flush();

                    $logs = new UserLogs();
                    $logs->user_id = $data['user_id'];
                    $logs->element = 22;
                    $logs->action = 'create';
                    $logs->element_id = $ContactFormFields->id;
                    $logs->source = 1;
                    $logs->log_date = new \DateTimeImmutable();

                    $entityManagerInterface->persist($logs);
                    $entityManagerInterface->flush();
                }
            }
        } else {
            foreach ($contactFormField as $user_id) {
                $form_field_data = $contactFormFieldsRepository->loadfromByFieldData1($ContactForms->id, $user_id);


                if (empty($form_field_data)) {
                    $ContactFormFields = new ContactFormFields();
                    $field = $customFieldsRepository->find($user_id);
                    $ContactFormFields->form = $ContactForms;
                    $ContactFormFields->field = $field;
                    $ContactFormFields->status = '1';
                    $ContactFormFields->date_start = new \DateTimeImmutable();

                    $entityManagerInterface->persist($ContactFormFields);
                    $entityManagerInterface->flush();

                    $logs = new UserLogs();
                    $logs->user_id = $data['user_id'];
                    $logs->element = 22;
                    $logs->action = 'create';
                    $logs->element_id = $ContactFormFields->id;
                    $logs->source = 1;
                    $logs->log_date = new \DateTimeImmutable();

                    $entityManagerInterface->persist($logs);
                    $entityManagerInterface->flush();
                }
            }
        }

        $rawQuery4 = "SELECT DISTINCT t.* , f.field_name , f.field_type , c.id as field_id 
        FROM contact_forms AS t 
        LEFT JOIN contact_form_fields AS c ON c.form_id = t.id AND c.status = 1 
        LEFT JOIN custom_fields AS f ON f.id = c.field_id 
        LEFT JOIN profiles AS pr ON FIND_IN_SET(pr.u_id, t.sendable_agents) 
        WHERE  t.id = :form_id and t.status ";

        $stmt4 = $entityManagerInterface->getConnection()->prepare($rawQuery4);
        // $stmt4->bindValue('account', $data['account']);

        $stmt4->bindValue('form_id', $ContactForms->id);


        $result = $stmt4->executeQuery()->fetchAllAssociative();
        // dd($result);
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
                    'source' => $row['source'],
                    'agent_status' => $row['agent_status'],
                    'message_capture' => $row['message_capture'],
                    'button' => $row['button'],
                    'friendly_name' => $row['friendly_name'],
                    'fields' => [],
                ];
            }

            $combinedData[$formId]['fields'][] = [
                'field_id' => $row['field_id'],
                'field_name' => $row['field_name'],
                'field_type' => $row['field_type'],
            ];
        }

        return new JsonResponse([
            'success' => true,
            'data' => $combinedData,
        ]);
    }

    #[Route('/delete_form/{id}', name: 'app_delete_form_controller')]
    public function deleteForm(
        $id,
        ContactFormsRepository $contactFormsRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
    ): Response {


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
        // $user = $tokenData->getUser();
        $ContactForms = $contactFormsRepository->find($id);
        $data = json_decode($request->getContent(), true);
        $ContactForms->date_end = new \DateTimeImmutable();
        $ContactForms->status = '0';


        //dd($clickableLinksUser);
        $entityManagerInterface->persist($ContactForms);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 21;
        $logs->action = 'delete';
        $logs->element_id = $ContactForms->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();



        return new JsonResponse([
            'success' => true,
            'data' => $ContactForms,
        ]);
    }
}
