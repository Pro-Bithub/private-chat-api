<?php

namespace App\Controller;

use App\Entity\UserLogs;
use App\Repository\ContactsRepository;
use App\Repository\ProfilesRepository;
use App\Repository\UserPresentationsRepository;
use App\services\FileUploader;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UpdateProfileController extends AbstractController
{
    #[Route('/update/{id}/profile', name: 'app_update_profile')]
    public function index(ProfilesRepository $profilesRepository,Request $request, EntityManagerInterface $entityManagerInterface): Response
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
        $time =  new \DateTimeImmutable();
        $data = json_decode($request->getContent(), true);

        // dd($data['username']);
        $profile = $profilesRepository->findProfileById($data['iduser']);
        //dd($profile);
        $profile->username = $data['username'];
        $profile->login = $data['email'];
        $entityManagerInterface->persist($profile);
        $entityManagerInterface->flush();

        $UserLogs = new UserLogs();
        $UserLogs->user_id = $data['iduser'];
        $UserLogs->action = 'Update Profile';
        $UserLogs->element = '30';
        $UserLogs->element_id = $profile->id;
        $UserLogs->log_date = $time;
        $UserLogs->source = '1';
        $entityManagerInterface->persist($UserLogs);
        $entityManagerInterface->flush();

        return new JsonResponse([
            'success' => 'true',
            'data' => $profile
        ]);

    }
 

    #[Route('new/update/{id}/profile', name: 'app_update_profile_with_avatar')]
    public function newupdateprofile( UserPresentationsRepository $userPresentationsRepository,ProfilesRepository $profilesRepository,Request $request, EntityManagerInterface $entityManagerInterface, FileUploader $fileUploader): Response
    {

        $authorizationHeader = $request->headers->get('Authorization');

        // Check if the token is present and in the expected format (Bearer TOKEN)
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            throw new AccessDeniedException('Invalid or missing authorization token.');
        }


        $tokenData = $this->get('security.token_storage')->getToken();

        if ($tokenData === null) {
            throw new AccessDeniedException('Invalid token.');
        }

         $user = $tokenData->getUser();
        $time =  new \DateTimeImmutable();
       // $data = json_decode($request->getContent(), true);
     
        // dd($data['username']);
        $profile = $profilesRepository->findProfileById(   $request->get('iduser'));
        //dd($profile);
        $profile->username = $request->get('username') ;
        $profile->login =$request->get('email')  ;
        $entityManagerInterface->persist($profile);
        $entityManagerInterface->flush();

        $uploadedFile = $request->files->get('file');
       if (null !== $uploadedFile) {
         
              $user_p=  $userPresentationsRepository->loadActiveUserPresentationByuser($profile->u_id);
              if($user_p!=null){
                try {
                  
              

                    $file_name = preg_replace('/[\s.]+/', '_', $user_p->nickname);
                    $user_p->picture = $fileUploader->upload($uploadedFile, $file_name,$user->accountId);

                    $entityManagerInterface->persist($user_p);
                    $entityManagerInterface->flush();
                } catch (FileException $e) {
                }
              }
        } 

        $UserLogs = new UserLogs();
        $UserLogs->user_id =$request->get('iduser') ;
        $UserLogs->action = 'Update Profile';
        $UserLogs->element = '30';
        $UserLogs->element_id = $profile->id;
        $UserLogs->log_date = $time;
        $UserLogs->source = '1';
        $entityManagerInterface->persist($UserLogs);
        $entityManagerInterface->flush();
      
      

        return new JsonResponse([
            'success' => 'true',
            'data' => $profile,
            
        ]);

    }

    #[Route('/updateProfilEmail/{id}/profile', name: 'app_update_email_profile')]
    public function updateProfilEmail(ProfilesRepository $profilesRepository,Request $request, EntityManagerInterface $entityManagerInterface): Response
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
        
        $time =  new \DateTimeImmutable();
        $data = json_decode($request->getContent(), true);

        
        $profile = $profilesRepository->findProfileById($data['iduser']);
        $profile->login = $data['login'];
        $entityManagerInterface->persist($profile);
        $entityManagerInterface->flush();
        
        $UserLogs = new UserLogs();
        $UserLogs->user_id = $data['iduser'];
        $UserLogs->action = 'Update Profile';
        $UserLogs->element = '30';
        $UserLogs->element_id = $profile->id;
        $UserLogs->log_date = $time;
        $UserLogs->source = '1';
        $entityManagerInterface->persist($UserLogs);
        $entityManagerInterface->flush();

        return new JsonResponse([
            'success' => 'true',
            'data' => $profile
        ]);

    }

    #[Route('/update_contact/{id}', name: 'app_update_contact_controller')]
    public function updateContact($id,Request $request,EntityManagerInterface $entityManagerInterface,ContactsRepository $contactsRepository,ProfilesRepository $profilesRepository): Response {
      
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
        $contacts = $contactsRepository->find($id);
        $contacts->gender = $data['gender'];
        $contacts->firstname = $data['firstname'];
        $contacts->lastname = $data['lastname'];
        $contacts->country = $data['country'];
        $contacts->name = $data['name'];
        $contacts->email = $data['email'];
        $contacts->phone = $data['phone'];
        $contacts->date_birth = new DateTime($data['dateBirth']);
        $contacts->address = $data['address'];
        $contacts->origin = $data['origin'];
        $contacts->company = $data['company'];
        $contacts->status = $data['status'];
       
       


        $entityManagerInterface->persist($contacts);
        $entityManagerInterface->flush();

        

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 20;
        $logs->action = 'update';
        $logs->element_id = $contacts->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        $time =  new \DateTimeImmutable();
        $profile = $profilesRepository->findContactProfileById($contacts->id);
        $profile->username = $contacts->name;
        $entityManagerInterface->persist($profile);
        $entityManagerInterface->flush();

        $UserLogs = new UserLogs();
        $UserLogs->user_id = $data['user_id'];
        $UserLogs->action = 'Update Profile';
        $UserLogs->element = '30';
        $UserLogs->element_id = $profile->id;
        $UserLogs->log_date = $time;
        $UserLogs->source = '1';
        $entityManagerInterface->persist($UserLogs);
        $entityManagerInterface->flush();
    
        return new JsonResponse([
            'success' => true,
            'data' => $contacts,
        ]);


    }
}
