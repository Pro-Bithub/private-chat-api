<?php

namespace App\Controller;

use App\Entity\ClickableLinks;
use App\Entity\ClickableLinksUsers;
use App\Entity\UserLogs;
use App\Repository\AccountsRepository;
use App\Repository\ClickableLinksRepository;
use App\Repository\ClickableLinksUsersRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AddlinkController extends AbstractController
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

        $clickablelinks = new ClickableLinks();
        $data = json_decode($request->getContent(), true);
        //dump($data);
        $account = $accountsRepository->find($data['account']);
        // $date = DateTime::createFromFormat('Y-m-d', $data['dateStart']);
        // $datediscount = DateTime::createFromFormat('Y-m-d', $data['discountdateStart']);

        $clickablelinks->account = $account;
        $clickablelinks->name = $data['name'];
        $clickablelinks->date_start = new \DateTimeImmutable();
        $clickablelinks->status = "1";
        $clickablelinks->url = $data['url'];


        $linkUserData = $data['clickableLinksUser'];

        $entityManagerInterface->persist($clickablelinks);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 7;
        $logs->action = 'create';
        $logs->element_id = $clickablelinks->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();
        if ($linkUserData != null) {
            foreach ($linkUserData  as $i => $value) {
                $linkUser = new ClickableLinksUsers();
                $user = $userRepository->find($value);
                $linkUser->link =  $clickablelinks;
                $linkUser->user = $user;
                $linkUser->status = '1';
                $linkUser->date_start = new \DateTimeImmutable();

                $entityManagerInterface->persist($linkUser);
                $entityManagerInterface->flush();

                $logs = new UserLogs();
                $logs->user_id = $data['user_id'];
                $logs->element = 8;
                $logs->action = 'create';
                $logs->element_id = $linkUser->id;
                $logs->source = 1;
                $logs->log_date = new \DateTimeImmutable();

                $entityManagerInterface->persist($logs);
                $entityManagerInterface->flush();
            }
        }
        return new JsonResponse([
            'success' => true,
            'data' => $clickablelinks,
        ]);
    }

    #[Route('/update_link/{id}', name: 'app_update_link_controller')]
    public function updateLink(
        $id,
        ClickableLinksRepository $clickableLinksRepository,
        ClickableLinksUsersRepository $clickableLinksUsersRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
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

        $clickablelinks = $clickableLinksRepository->find($id);
        $data = json_decode($request->getContent(), true);
        $clickablelinks->name = $data['name'];
        $clickablelinks->status = $data['status'];
        $clickablelinks->url = $data['url'];

        $clickableLinksUser = isset($data['clickableLinksUser']) ? $data['clickableLinksUser'] : [];
        //dd($clickableLinksUser);
        $entityManagerInterface->persist($clickablelinks);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 7;
        $logs->action = 'update';
        $logs->element_id = $clickablelinks->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        $sql3 = "SELECT t.user_id FROM `clickable_links_users` as t WHERE t.link_id = :id and t.status = 1";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $clickablelinks->id);

        $predefinedTextUserIds = $statement3->executeQuery()->fetchAllAssociative();
        // $sql3="SELECT t.user_id FROM `predefined_text_users` as t WHERE t.text_id = :id and t.status = 1";
        //     $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        //     $statement3->bindValue('id', $predefindText->id);
        //     $predefinedTextUserIds = $statement3->executeQuery()->fetchAllAssociative();
        $result = array_column($predefinedTextUserIds, 'user_id');

        $difference2 = array_diff($result, $clickableLinksUser);
        //dd($difference2, $result, $clickableLinksUser);
        if (!empty($difference2)) {
            foreach ($difference2 as $difference) {
                if (in_array($difference, $result)) {
                    $link_user_data = $clickableLinksUsersRepository->loadlinkByUserData1($clickablelinks->id, $difference);
                    if (!empty($link_user_data)) {
                        $link_user = $link_user_data[0];
                        $link_user->status = '0';
                        $link_user->date_end = new \DateTimeImmutable();

                        $entityManagerInterface->persist($link_user);
                        $entityManagerInterface->flush();
                    }
                } else {
                    $ClickableLinksUsers = new ClickableLinksUsers();
                    $user = $userRepository->find($difference);
                    $ClickableLinksUsers->link = $clickablelinks;
                    $ClickableLinksUsers->user = $user;
                    $ClickableLinksUsers->status = '1';
                    $ClickableLinksUsers->date_start = new \DateTimeImmutable();

                    $entityManagerInterface->persist($ClickableLinksUsers);
                    $entityManagerInterface->flush();

                    $logs = new UserLogs();
                    $logs->user_id = $data['user_id'];
                    $logs->element = 8;
                    $logs->action = 'create';
                    $logs->element_id = $ClickableLinksUsers->id;
                    $logs->source = 1;
                    $logs->log_date = new \DateTimeImmutable();

                    $entityManagerInterface->persist($logs);
                    $entityManagerInterface->flush();
                }
            }
        } else {
            foreach ($clickableLinksUser as $user_id) {
                $link_user_data = $clickableLinksUsersRepository->loadlinkByUserData1($clickablelinks->id, $user_id);

                if (empty($link_user_data)) {
                    $ClickableLinksUsers = new ClickableLinksUsers();
                    $user = $userRepository->find($user_id);
                    $ClickableLinksUsers->link = $clickablelinks;
                    $ClickableLinksUsers->user = $user;
                    $ClickableLinksUsers->status = '1';
                    $ClickableLinksUsers->date_start = new \DateTimeImmutable();

                    $entityManagerInterface->persist($ClickableLinksUsers);
                    $entityManagerInterface->flush();

                    $logs = new UserLogs();
                    $logs->user_id = $data['user_id'];
                    $logs->element = 8;
                    $logs->action = 'create';
                    $logs->element_id = $ClickableLinksUsers->id;
                    $logs->source = 1;
                    $logs->log_date = new \DateTimeImmutable();

                    $entityManagerInterface->persist($logs);
                    $entityManagerInterface->flush();
                }
            }
        }

        return new JsonResponse([
            'success' => true,
            'data' => $clickablelinks,
        ]);
    }

    #[Route('/delete_link/{id}', name: 'app_delete_link_controller')]
    public function deleteLink(
        $id,
        ClickableLinksRepository $clickableLinksRepository,
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
        
        $clickablelinks = $clickableLinksRepository->find($id);
        $data = json_decode($request->getContent(), true);
        $clickablelinks->status = '0';
        $clickablelinks->date_end = new \DateTimeImmutable();


        //dd($clickableLinksUser);
        $entityManagerInterface->persist($clickablelinks);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 7;
        $logs->action = 'delete';
        $logs->element_id = $clickablelinks->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();



        return new JsonResponse([
            'success' => true,
            'data' => $clickablelinks,
        ]);
    }

    #[Route('/checkLinkName')]
    public function checkLinkName(Request $request, EntityManagerInterface $entityManagerInterface): Response
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
        $sql3 = "SELECT * FROM `clickable_links` as c WHERE c.name LIKE :name and c.account_id = :account and c.status = 1";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('name', $Name);
        $statement3->bindValue('account', $user->accountId);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }

    #[Route('/checkLinkUrl')]
    public function checkLinkUrl(Request $request, EntityManagerInterface $entityManagerInterface): Response
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

        $slug = $request->query->get('url');
        // $account = $request->query->get('account');

        $sql3 = "SELECT * FROM `clickable_links` as c WHERE c.url LIKE :url and c.account_id = :account and c.status = 1";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('url', $slug);
        $statement3->bindValue('account', $user->accountId);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();

        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }
}
