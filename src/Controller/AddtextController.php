<?php

namespace App\Controller;

use App\Entity\PredefindTexts;
use App\Entity\PredefinedTextUsers;
use App\Entity\UserLogs;
use App\Repository\AccountsRepository;
use App\Repository\PredefindTextsRepository;
use App\Repository\PredefinedTextUsersRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AddtextController extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface, AccountsRepository $accountsRepository, UserRepository $userRepository): Response
    {
        // account: new FormControl('api/accounts/' + this.userdata.account_id),
        // name: new FormControl('', Validators.required),
        // language: new FormControl('', Validators.required),
        // category: new FormControl('', Validators.required),
        // text: new FormControl('', Validators.required),
        // status: new FormControl('1', Validators.required),
        // dateStart: new FormControl(new Date(), Validators.required),
        // PreDefinedTextUser: new FormControl(null),
        $predefindText = new PredefindTexts();
        $data = json_decode($request->getContent(), true);
        //dump($data);
        $account = $accountsRepository->find($data['account']);
        // $date = DateTime::createFromFormat('Y-m-d', $data['dateStart']);
        // $datediscount = DateTime::createFromFormat('Y-m-d', $data['discountdateStart']);

        $predefindText->account = $account;
        $predefindText->name = $data['name'];
        $predefindText->date_start = new \DateTimeImmutable();
        $predefindText->status = "1";
        $predefindText->language = $data['language'];
        $predefindText->category = $data['category'];
        $predefindText->shortCut = $data['Shortcut'];
        $predefindText->text = $data['text'];

        $textUserData = $data['PreDefinedTextUser'];

        $entityManagerInterface->persist($predefindText);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 12;
        $logs->action = 'create';
        $logs->element_id = $predefindText->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();
if($textUserData != null){
        foreach ($textUserData  as $i => $value) {
            $textUser = new PredefinedTextUsers();
            $user = $userRepository->find($value);
            $textUser->text =  $predefindText;
            $textUser->user = $user;
            $textUser->status = '1';
            $textUser->date_start = new \DateTimeImmutable();

            $entityManagerInterface->persist($textUser);
            $entityManagerInterface->flush();

            $logs = new UserLogs();
            $logs->user_id = $data['user_id'];
            $logs->element = 13;
            $logs->action = 'create';
            $logs->element_id = $textUser->id;
            $logs->source = 1;
            $logs->log_date = new \DateTimeImmutable();

            $entityManagerInterface->persist($logs);
            $entityManagerInterface->flush();
        }
    }

        return new JsonResponse([
            'success' => true,
            'data' => $predefindText,
        ]);
    }

    #[Route('/update_text/{id}', name: 'app_update_text_controller')]
    public function updateText(
        $id,
        PredefinedTextUsersRepository $predefinedTextUsersRepository,
        PredefindTextsRepository $predefindTextsRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        AccountsRepository $accountsRepository,
        UserRepository $userRepository
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
        
        $predefindText = $predefindTextsRepository->find($id);
        $data = json_decode($request->getContent(), true);
        $predefindText->name = $data['name'];
        $predefindText->date_start = new \DateTimeImmutable();
        $predefindText->status = $data['status'];
        $predefindText->language = $data['language'];
        $predefindText->category = $data['category'];
        $predefindText->shortCut = $data['Shortcut'];
        $predefindText->text = $data['text'];

        $textUserData = isset($data['PreDefinedTextUser']) ? $data['PreDefinedTextUser'] : [];

        $entityManagerInterface->persist($predefindText);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 12;
        $logs->action = 'update';
        $logs->element_id = $predefindText->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        $sql3 = "SELECT t.user_id FROM `predefined_text_users` as t WHERE t.text_id = :id and t.status = 1";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $predefindText->id);

        $predefinedTextUserIds = $statement3->executeQuery()->fetchAllAssociative();
        // $sql3="SELECT t.user_id FROM `predefined_text_users` as t WHERE t.text_id = :id and t.status = 1";
        //     $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        //     $statement3->bindValue('id', $predefindText->id);
        //     $predefinedTextUserIds = $statement3->executeQuery()->fetchAllAssociative();
        $result = array_column($predefinedTextUserIds, 'user_id');

        $difference2 = array_diff($result, $textUserData);

        if (!empty($difference2)) {
            foreach ($difference2 as $difference) {
                if (in_array($difference, $result)) {
                    $predefined_text_user_data = $predefinedTextUsersRepository->loadpredefinedtextByUserData1($predefindText->id, $difference);
                    if (!empty($predefined_text_user_data)) {
                        $predefined_text_user = $predefined_text_user_data[0];
                        $predefined_text_user->status = '0';
                        $predefined_text_user->date_end = new \DateTimeImmutable();

                        $entityManagerInterface->persist($predefined_text_user);
                        $entityManagerInterface->flush();
                    }
                } else {
                    $textUser = new PredefinedTextUsers();
                    $user = $userRepository->find($difference);
                    $textUser->text = $predefindText;
                    $textUser->user = $user;
                    $textUser->status = '1';
                    $textUser->date_start = new \DateTimeImmutable();

                    $entityManagerInterface->persist($textUser);
                    $entityManagerInterface->flush();

                    $logs = new UserLogs();
                    $logs->user_id = $data['user_id'];
                    $logs->element = 13;
                    $logs->action = 'create';
                    $logs->element_id = $textUser->id;
                    $logs->source = 1;
                    $logs->log_date = new \DateTimeImmutable();

                    $entityManagerInterface->persist($logs);
                    $entityManagerInterface->flush();
                }
            }
        } else {
            foreach ($textUserData as $user_id) {
                $predefined_text_user_data = $predefinedTextUsersRepository->loadpredefinedtextByUserData1($predefindText->id, $user_id);
                if (empty($predefined_text_user_data)) {
                    $textUser = new PredefinedTextUsers();
                    $user = $userRepository->find($user_id);
                    $textUser->text = $predefindText;
                    $textUser->user = $user;
                    $textUser->status = '1';
                    $textUser->date_start = new \DateTimeImmutable();

                    $entityManagerInterface->persist($textUser);
                    $entityManagerInterface->flush();

                    $logs = new UserLogs();
                    $logs->user_id = $data['user_id'];
                    $logs->element = 13;
                    $logs->action = 'create';
                    $logs->element_id = $textUser->id;
                    $logs->source = 1;
                    $logs->log_date = new \DateTimeImmutable();

                    $entityManagerInterface->persist($logs);
                    $entityManagerInterface->flush();
                }
            }
        }

        return new JsonResponse([
            'success' => true,
            'data' => $predefindText,
        ]);
    }


    #[Route('/delete_text/{id}', name: 'app_delete_text_controller')]
    public function deleteText(
        $id,
        PredefindTextsRepository $predefindTextsRepository,
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
        $predefindText = $predefindTextsRepository->find($id);
        $data = json_decode($request->getContent(), true);

        $predefindText->date_end = new \DateTimeImmutable();
        $predefindText->status = "0";


        $entityManagerInterface->persist($predefindText);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 12;
        $logs->action = 'delete';
        $logs->element_id = $predefindText->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();


        return new JsonResponse([
            'success' => true,
            'data' => $predefindText,
        ]);
    }

    #[Route('/checkShortcut/{Shortcut}')]
    public function checkShortcut($Shortcut,Request $request,EntityManagerInterface $entityManagerInterface): Response
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

        $sql3= "SELECT * FROM `predefind_texts` as p WHERE p.short_cut LIKE :Shortcut and p.account_id = :account";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('Shortcut', $Shortcut);
        $statement3->bindValue('account', $user->accountId);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }
}
