<?php

namespace App\Controller;

use App\Entity\LandingPageFields;
use App\Entity\LandingPages;
use App\Entity\UserLogs;
use App\Repository\AccountsRepository;
use App\Repository\CustomFieldsRepository;
use App\Repository\LandingPageFieldsRepository;
use App\Repository\LandingPagesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AddPageController extends AbstractController
{
    public function __invoke(Request $request,CustomFieldsRepository $customFieldsRepository , EntityManagerInterface $entityManagerInterface, AccountsRepository $accountsRepository): Response
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
        // $user = $tokenData->getUser();
        // account: new FormControl('api/accounts/' + this.userdata.account_id),
        // name: new FormControl('', Validators.required),
        // language: new FormControl('', Validators.required),
        // category: new FormControl('', Validators.required),
        // text: new FormControl('', Validators.required),
        // status: new FormControl('1', Validators.required),
        // dateStart: new FormControl(new Date(), Validators.required),
        // PreDefinedTextUser: new FormControl(null),
        $LandingPages = new LandingPages();
        $data = json_decode($request->getContent(), true);
        //dump($data);
        $account = $accountsRepository->find($data['account']);
        // $date = DateTime::createFromFormat('Y-m-d', $data['dateStart']);
        // $datediscount = DateTime::createFromFormat('Y-m-d', $data['discountdateStart']);

        $LandingPages->account = $account;
        $LandingPages->name = $data['name'];
        $LandingPages->comment = $data['comment'];
        $LandingPages->url = $data['url'];
        
        $LandingPages->date_start = new \DateTimeImmutable();
        $LandingPages->status = "1";
       
       
       

        $entityManagerInterface->persist($LandingPages);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 10;
        $logs->action = 'create';
        $logs->element_id = $LandingPages->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
   
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        $fieldpageData = $data['landingPagesField'];

        foreach ($fieldpageData  as $i => $value) {
            $PageFields = new LandingPageFields();
            $field = $customFieldsRepository->find($value);
            $PageFields->page =  $LandingPages;
            $PageFields->field = $field;
            $PageFields->status = '1';
            $PageFields->date_start = new \DateTimeImmutable();

            $entityManagerInterface->persist($PageFields);
            $entityManagerInterface->flush();

            $logs = new UserLogs();
            $logs->user_id = $data['user_id'];
            $logs->element = 11;
            $logs->action = 'create';
            $logs->element_id = $PageFields->id;
            $logs->source = 1;
            $logs->log_date = new \DateTimeImmutable();
       
            $entityManagerInterface->persist($logs);
            $entityManagerInterface->flush();
        }

        return new JsonResponse([
            'success' => true,
            'data' => $LandingPages,
        ]);
    }

    #[Route('/update_page/{id}', name: 'app_update_page_controller')]
    public function updatepage(
        $id,
        LandingPagesRepository $landingPagesRepository,
        LandingPageFieldsRepository $landingPageFieldsRepository,
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

        $landingpage = $landingPagesRepository->find($id);
        $data = json_decode($request->getContent(), true);
        $landingpage->name = $data['name'];
        $landingpage->comment = $data['comment'];
        $landingpage->status = $data['status'];
    
        $landingPageField = isset($data['landingPageField']) ? $data['landingPageField'] : [];
        //dd($clickableLinksUser);
        $entityManagerInterface->persist($landingpage);
        $entityManagerInterface->flush();
    
        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 10;
        $logs->action = 'update';
        $logs->element_id = $landingpage->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
    
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();
    
        $sql3 = "SELECT t.field_id FROM `landing_page_fields` as t WHERE t.page_id = :id and t.status = 1";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $landingpage->id);
       
        $predefinedTextUserIds = $statement3->executeQuery()->fetchAllAssociative();
        // $sql3="SELECT t.user_id FROM `predefined_text_users` as t WHERE t.text_id = :id and t.status = 1";
        //     $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        //     $statement3->bindValue('id', $predefindText->id);
        //     $predefinedTextUserIds = $statement3->executeQuery()->fetchAllAssociative();
        $result = array_column($predefinedTextUserIds, 'field_id');
    
        $difference2 = array_diff($result, $landingPageField);
        //dd($difference2, $result, $clickableLinksUser);
        if (!empty($difference2)) {
            foreach ($difference2 as $difference) {
                if (in_array($difference, $result)) {
                    $form_field_data = $landingPageFieldsRepository->loadpageByFieldData1($landingpage->id, $difference);
                    if (!empty($form_field_data)) {
                        $form_field = $form_field_data[0];
                        $form_field->status = '0';
                        $form_field->date_end = new \DateTimeImmutable();
    
                        $entityManagerInterface->persist($form_field);
                        $entityManagerInterface->flush();
                    }
                } else {
                    $LandingPageFields = new LandingPageFields();
                    $field = $customFieldsRepository->find($difference);
                    $LandingPageFields->page = $landingpage;
                    $LandingPageFields->field = $field;
                    $LandingPageFields->status = '1';
                    $LandingPageFields->date_start = new \DateTimeImmutable();
    
                    $entityManagerInterface->persist($LandingPageFields);
                    $entityManagerInterface->flush();
    
                    $logs = new UserLogs();
                    $logs->user_id = $data['user_id'];
                    $logs->element = 11;
                    $logs->action = 'create';
                    $logs->element_id = $LandingPageFields->id;
                    $logs->source = 1;
                    $logs->log_date = new \DateTimeImmutable();
    
                    $entityManagerInterface->persist($logs);
                    $entityManagerInterface->flush();
                }
            }
        } else {
            foreach ($landingPageField as $user_id) {
                $form_field_data = $landingPageFieldsRepository->loadpageByFieldData1($landingpage->id, $user_id);


                if (empty($form_field_data)) {
                    $LandingPageFields = new LandingPageFields();
                    $field = $customFieldsRepository->find($user_id);
                    $LandingPageFields->page = $landingpage;
                    $LandingPageFields->field = $field;
                    $LandingPageFields->status = '1';
                    $LandingPageFields->date_start = new \DateTimeImmutable();
    
                    $entityManagerInterface->persist($LandingPageFields);
                    $entityManagerInterface->flush();
    
                    $logs = new UserLogs();
                    $logs->user_id = $data['user_id'];
                    $logs->element = 11;
                    $logs->action = 'create';
                    $logs->element_id = $LandingPageFields->id;
                    $logs->source = 1;
                    $logs->log_date = new \DateTimeImmutable();
    
                    $entityManagerInterface->persist($logs);
                    $entityManagerInterface->flush();
                }
            }
        }
    
        return new JsonResponse([
            'success' => true,
            'data' => $landingpage,
        ]);
    }

    #[Route('/delete_page/{id}', name: 'app_update_delete_controller')]
    public function deletepage(
        $id,
        LandingPagesRepository $landingPagesRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface
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
        $landingpage = $landingPagesRepository->find($id);
        $data = json_decode($request->getContent(), true);
        //dd($landingpage);
        $landingpage->date_end = new \DateTimeImmutable();
        $landingpage->status = '0';
    
       
        //dd($clickableLinksUser);
        $entityManagerInterface->persist($landingpage);
        $entityManagerInterface->flush();
    
        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 10;
        $logs->action = 'delete';
        $logs->element_id = $landingpage->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
    
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();
    
        
        return new JsonResponse([
            'success' => true,
            'data' => $landingpage,
        ]);
    }

    #[Route('/checkPageName')]
    public function checkPageName(Request $request, EntityManagerInterface $entityManagerInterface): Response
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
        $Name = $request->query->get('name');
        // $account = $request->query->get('account');
        $sql3 = "SELECT * FROM `landing_pages` as c WHERE c.name LIKE :name and c.account_id = :account and c.status = 1";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('name', $Name);
        $statement3->bindValue('account', $user->accountId);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => true,
            'data' => $results3
        ]);
    }
}
