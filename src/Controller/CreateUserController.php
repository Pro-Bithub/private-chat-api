<?php

namespace App\Controller;

use App\Entity\Profiles;
use App\Entity\User;
use App\Entity\UserLogs;
use App\Entity\UserNotifications;
use App\Entity\UserPermissions;
use App\Entity\UserPresentations;
use App\Entity\UserRights;
use App\Repository\AccountsRepository;
use App\Repository\ProfilesRepository;
use App\Repository\UserNotificationsRepository;
use App\Repository\UserPermissionsRepository;
use App\Repository\UserPresentationsRepository;
use App\Repository\UserRepository;
use App\Repository\UserRightsRepository;
use App\services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sinergi\BrowserDetector\Os;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Util\SecureRandom;

#[AsController]
class CreateUserController extends AbstractController
{

    public function __invoke(FileUploader $fileUploader, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManagerInterface, AccountsRepository $accountsRepository, UserRepository $userRepository): Response
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
        $user2 = $tokenData->getUser();
        // dd($user2->accountId);
        $request1 = Request::createFromGlobals();

        $data = json_decode($request->getContent(), true);
        $user = new User();
        // $account = $accountsRepository->find($request->get('accountId'));
        $user->accountId = $user2->accountId;
        $user->gender = $request->get('gender');
        $user->lastname = $request->get('lastname');

        $user->firstname = $request->get('firstname');
        $user->email = $request->get('email');
        $user->notification_mail = $request->get('notificationsMail');
        $user->notification_audio = $request->get('notificationAudio');
        $user->notification_browser = $request->get('notificationBrowser');

        $user->status = $request->get('status');
        $user->password = $userPasswordHasher->hashPassword($user, $request->get('password'));


        $user->date_start = new \DateTime('@' . strtotime('now'));

        $entityManagerInterface->persist($user);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $request->get('user_id');
        $logs->element = 2;
        $logs->action = 'create';
        $logs->element_id = $user->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();
        $userid = $userRepository->find($user->id);

        $userPresentations = new UserPresentations();

        $userPresentations->gender = $request->get('gender');
        $userPresentations->website = $request->get('website');
        if ($request->files->get('file') != null) {
            $uploadedFile = $request->files->get('file');
        
            // Upload the original file and get its filename
            $originalFileName = $fileUploader->upload($uploadedFile);
        
            // Get the user's ID
            $userId = $user->id;
        
            // Extract the original file extension
            $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
        
            // Create a new filename by appending the user's ID to the original filename
            $newFileName = $userId . '.' . $extension;
        
            // Get the destination directory
            $destinationDirectory = $fileUploader->getUploadPath();
        
            // Copy the original file and rename it with the new filename
            $sourcePath = $destinationDirectory . '/' . $originalFileName;
            $destinationPath = $destinationDirectory . '/' . $newFileName;
        
            if (file_exists($sourcePath)) {
                copy($sourcePath, $destinationPath);
            } else {
                // Handle error if the source file doesn't exist
                throw new \Exception('Source file not found.');
            }

            $userPresentations->picture = $originalFileName;

        }
        
        


        $userPresentations->role = $request->get('role');
        $userPresentations->gender = $request->get('gender_presentation');
        $userPresentations->nickname = $request->get('nickname');
        $userPresentations->country = $request->get('country');
        $userPresentations->languages = $request->get('languages');
        $userPresentations->expertise = $request->get('expertise');
        $userPresentations->diploma = $request->get('diploma');
        $userPresentations->status = $request->get('status_presentation');
        $userPresentations->brand_name = $request->get('brandName');
        $userPresentations->contact_phone = $request->get('contactPhone');
        $userPresentations->contact_mail = $request->get('contactMail');
        $userPresentations->atrological_sign = $request->get('atrologicalSign');
        $userPresentations->date_start = new \DateTime('@' . strtotime('now'));
        $userPresentations->skills = $request->get('skills');
        $userPresentations->presentation = $request->get('presentation');
        $userPresentations->contact_phone_comment = $request->get('contactPhoneComment');
        $userPresentations->user = $userid;

        $entityManagerInterface->persist($userPresentations);

        $entityManagerInterface->flush();
        // if ($request->files->get('file') != null) {
        //     $uploadedFile = $request->files->get('file');

        //     // dd(file_exists($uploadedFile->getPathName()));

        //     // $userPresentations->picture = $fileUploader->upload($uploadedFile);
        //     $fileUploader->uploadProfile($uploadedFile,$user->id);

        // }
        $logs = new UserLogs();
        $logs->user_id = $request->get('user_id');
        $logs->element = 18;
        $logs->action = 'create';
        $logs->element_id = $userPresentations->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();
        $userAgent = $request1->headers->get('User-Agent');

        // Use a library like BrowserDetect to parse the user agent string
        $browser = new \Sinergi\BrowserDetector\Browser($userAgent);
        $os = new Os();

        // Generate a random token (assuming $profiles is an entity object)
        $randomToken = bin2hex(random_bytes(32)); // Change 32 to the desired token length

        //dd($os->getName());

        $profiles = new Profiles();
        $profiles->accountId = $user->accountId;
        $profiles->username = $userPresentations->nickname;
        $profiles->password = $user->password;
        $profiles->login = $user->email;
        $profiles->u_id = $user->id;
        $profiles->u_type = '1';
        $browserName = $browser->getName();
        $profiles->browser_data = $browserName . ';' . $os->getName();


        // Set the generated token to the $profiles->user_key property
        $profiles->user_key = $randomToken;
        // $profiles->user_key = 
        $profiles->ip_address =  $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
        $entityManagerInterface->persist($profiles);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $request->get('user_id');
        $logs->element = 30;
        $logs->action = 'create';
        $logs->element_id = $profiles->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        $userRights = new UserRights();
        $userRights->user = $userid;
        $userRights->contact_gender = $request->get('contactGender');
        $userRights->contact_name = $request->get('contactName');
        $userRights->contact_firstname = $request->get('contactFirstname');
        $userRights->contact_lastname = $request->get('contactLastname');
        $userRights->contact_phone = $request->get('contactPhone');
        $userRights->contact_country = $request->get('contactCountry');
        $userRights->contact_address = $request->get('contactAddress');
        $userRights->contact_ipaddress = $request->get('contactIpaddress');
        $userRights->contact_request_category = $request->get('contactRequestCategory');
        $userRights->contact_request = $request->get('contactRequest');
        $userRights->contact_origin = $request->get('contactOrigin');
        $userRights->contact_date_of_birth = $request->get('contactDateOfBirth');
        $userRights->contact_company_name = $request->get('contactCompanyName');
        $userRights->contact_custom_fields = $request->get('contactCustomFields');
        $userRights->status = '1';
        $userRights->date_start = new \DateTime('@' . strtotime('now'));
        $entityManagerInterface->persist($userRights);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $request->get('user_id');
        $logs->element = 19;
        $logs->action = 'create';
        $logs->element_id = $profiles->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        $userNotification = new UserNotifications();
        $userNotification->user = $userid;
        $userNotification->email_notifications = $request->get('emailNotifications');
        $userNotification->visitor_register = $request->get('visitorRegister');
        $userNotification->visitor_login = $request->get('visitorLogin');
        $userNotification->plan_actions = $request->get('planActions');
        $userNotification->contact_form_actions = $request->get('contactFormActions');
        $userNotification->predefined_text_actions = $request->get('predefinedTextActions');
        $userNotification->links_actions = $request->get('linksActions');
        $userNotification->user_actions = $request->get('userActions');
        $userNotification->landing_page_actions = $request->get('landingPageActions');
        $userNotification->contact_actions = $request->get('contactActions');
        $userNotification->sales = $request->get('sales');

        $entityManagerInterface->persist($userNotification);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $request->get('user_id');
        $logs->element = 15;
        $logs->action = 'create';
        $logs->element_id = $profiles->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        $userPermission = new UserPermissions();
        $userPermission->user = $userid;
        $userPermission->visitors_rating = $request->get('visitorsRating');
        $userPermission->package_visibility = $request->get('packageVisibility');
        $userPermission->package_creation = $request->get('packageCreation');
        $userPermission->planning_management = $request->get('planningManagement');
        $userPermission->pre_defined_messages = $request->get('preDefinedMessages');
        $userPermission->business_tools = $request->get('businessTools');
        $userPermission->communications = $request->get('communication');
        $userPermission->status = '1';
        $userPermission->date_start = new \DateTime('@' . strtotime('now'));
        $entityManagerInterface->persist($userPermission);
        $entityManagerInterface->flush();
        // dd('eqqfs');

        return new JsonResponse([
            'success' => true,
            'data' => $user,
            'nickname' => $userPresentations->nickname
        ]);


        //dd($_REQUEST['gender'));
        //$data = json_decode($request->getContent(), true);
        // $userPresentations->setGender($request->request->get('gender'));
        // $userPresentations->setWebsite($request->request->get('website'));
        // $userPresentations->setRole($request->request->get('role'));
        // $userPresentations->setNickname($request->request->get('nickname'));
        // $userPresentations->setCountry($request->request->get('country'));
        // $userPresentations->setLanguages($request->request->get('languages'));
        // $userPresentations->setExpertise($request->request->get('expertise'));
        // $userPresentations->setDiploma($request->request->get('diploma'));
        // $userPresentations->setStatus($request->request->get('status'));
        // $userPresentations->setBrandName($request->request->get('brandName'));
        // $userPresentations->setContactPhone($request->request->get('contactPhone'));
        // $userPresentations->setContactPhoneComment($request->request->get('contactPhoneComment'));
        // $userPresentations->setContactMail($request->request->get('contactMail'));
        // $userPresentations->setAtrologicalSign($request->request->get('atrologicalSign'));
        // $userPresentations->setFile($request->files->get('file'));
        // $userPresentations->setDateStart(new \DateTime('@'.strtotime('now')));









    }

    #[Route('/update_user_rights/{id}', name: 'app_update_user_rights_controller')]
    public function updateUserRights($id, Request $request, EntityManagerInterface $entityManagerInterface, UserRightsRepository $userRightsRepository): Response
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
        $data = json_decode($request->getContent(), true);
        $userRight = $userRightsRepository->find($id);
        // dd($userRight->contact_gender);
        if ($data['contactGender'] != null) {
            $userRight->contact_address = $data['contactGender'];
        } else if ($data['contactName'] != null) {
            $userRight->contact_name = $data['contactName'];
        } else if ($data['contactLastname'] != null) {
            $userRight->contact_lastname = $data['contactLastname'];
        } else if ($data['contactPhone'] != null) {
            $userRight->contact_phone = $data['contactPhone'];
        } else if ($data['contactFirstname'] != null) {
            $userRight->contact_firstname = $data['contactFirstname'];
        } else if ($data['contactOrigin'] != null) {
            $userRight->contact_origin = $data['contactOrigin'];
        } else if ($data['contactAddress']) {
            $userRight->contact_address = $data['contactAddress'];
        } else if ($data['contactCountry'] != null) {
            $userRight->contact_country = $data['contactCountry'];
        } else if ($data['contactIpaddress'] != null) {
            $userRight->contact_ipaddress = $data['contactIpaddress'];
        } else if ($data['contactCustomFields'] != null) {
            $userRight->contact_custom_fields = $data['contactCustomFields'];
        } else if ($data['contactDateOfBirth'] != null) {
            $userRight->contact_date_of_birth = $data['contactDateOfBirth'];
        } else if ($data['contactRequest'] != null) {
            $userRight->contact_request = $data['contactRequest'];
        } else if ($data['contactRequestCategory'] != null) {
            $userRight->contact_request_category = $data['contactRequestCategory'];
        } else if ($data['contactCompanyName'] != null) {
            $userRight->contact_company_name = $data['contactCompanyName'];
        }
        $entityManagerInterface->persist($userRight);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 19;
        $logs->action = 'update';
        $logs->element_id = $userRight->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();


        return new JsonResponse([
            'success' => true,
            'data' => $userRight,
        ]);
    }

    #[Route('/update_user_notifications/{id}', name: 'app_update_user_notifications_controller')]
    public function updateUserNotifications($id, Request $request, EntityManagerInterface $entityManagerInterface, UserNotificationsRepository $userNotificationsRepository): Response
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
        $data = json_decode($request->getContent(), true);
        $usernotifications = $userNotificationsRepository->find($id);
        $usernotifications->visitor_register = $data['visitorRegister'];
        $usernotifications->visitor_login = $data['visitorLogin'];
        $usernotifications->plan_actions = $data['planActions'];
        $usernotifications->contact_form_actions = $data['contactFormActions'];
        $usernotifications->predefined_text_actions = $data['predefinedTextActions'];
        $usernotifications->links_actions = $data['linksActions'];
        $usernotifications->user_actions = $data['userActions'];
        $usernotifications->landing_page_actions = $data['landingPageActions'];
        $usernotifications->contact_actions = $data['contactActions'];
        $usernotifications->sales = $data['sales'];



        $entityManagerInterface->persist($usernotifications);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 15;
        $logs->action = 'update';
        $logs->element_id = $usernotifications->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();


        return new JsonResponse([
            'success' => true,
            'data' => $usernotifications,
        ]);
    }

    #[Route('/add_user_notifications', name: 'app_add_user_notifications_controller')]
    public function addUserNotifications( Request $request, EntityManagerInterface $entityManagerInterface,  UserRepository $userRepository): Response
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
        $data = json_decode($request->getContent(), true);
        $usernotifications =new UserNotifications() ;
        $usernotifications->visitor_register = $data['visitorRegister'];
        $usernotifications->visitor_login = $data['visitorLogin'];
        $usernotifications->plan_actions = $data['planActions'];
        $usernotifications->contact_form_actions = $data['contactFormActions'];
        $usernotifications->predefined_text_actions = $data['predefinedTextActions'];
        $usernotifications->links_actions = $data['linksActions'];
        $usernotifications->user_actions = $data['userActions'];
        $usernotifications->landing_page_actions = $data['landingPageActions'];
        $usernotifications->contact_actions = $data['contactActions'];
        $usernotifications->sales = $data['sales'];

   
        
        $old_user = $userRepository->find($data['u_id']);
        $usernotifications->user = $old_user;



        $entityManagerInterface->persist($usernotifications);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 15;
        $logs->action = 'create';
        $logs->element_id = $usernotifications->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();


        return new JsonResponse([
            'success' => true,
            'data' => $usernotifications,
      
       
        ]);
    }

    #[Route('/add_user_presentation/{id}', name: 'app_add_user_presentation_controller')]
    public function addUserPresentation( $id,Request $request, EntityManagerInterface $entityManagerInterface, UserPresentationsRepository $userPresentationsRepository, UserRepository $userRepository): Response
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
        $data = json_decode($request->getContent(), true);
        // dd($data);
        $userid = $userRepository->find($id);
        $userPresentation = new UserPresentations();
        $userPresentation->user =$userid;
        $userPresentation->gender = $data['gender'];
        $userPresentation->website = $data['website'];
        $userPresentation->role = $data['role'];
        $userPresentation->nickname = $data['nickname'];
        $userPresentation->country = $data['country'];
        $userPresentation->languages = $data['languages'];
        $userPresentation->expertise = $data['expertise'];
        $userPresentation->diploma = $data['diploma'];
        $userPresentation->brand_name = $data['brand_name'];
        $userPresentation->contact_phone = $data['contact_phone'];
        $userPresentation->contact_mail = $data['contact_mail'];
        $userPresentation->atrological_sign = $data['atrological_sign'];
        $userPresentation->skills = $data['skills'];
        $userPresentation->status = '1';
        $userPresentation->date_start = new \DateTime('@' . strtotime('now'));
        $userPresentation->presentation = $data['presentation'];
        $userPresentation->contact_phone_comment = $data['contact_phone_comment'];



        $entityManagerInterface->persist($userPresentation);
        $entityManagerInterface->flush();



        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 18;
        $logs->action = 'add';
        $logs->element_id = $userPresentation->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        // $time =  new \DateTimeImmutable();
        // $profile = $profilesRepository->findProfileById($userPresentation->user);
        // $profile->username = $userPresentation->nickname;
        // $entityManagerInterface->persist($profile);
        // $entityManagerInterface->flush();

        // $UserLogs = new UserLogs();
        // $UserLogs->user_id = $data['user_id'];
        // $UserLogs->action = 'Update Profile';
        // $UserLogs->element = '30';
        // $UserLogs->element_id = $profile->id;
        // $UserLogs->log_date = $time;
        // $UserLogs->source = '1';
        // $entityManagerInterface->persist($UserLogs);
        // $entityManagerInterface->flush();

        return new JsonResponse([
            'success' => true,
            'data' => $userPresentation,
        ]);
    }



    #[Route('/update_user_presentation/{id}', name: 'app_update_user_presentation_controller')]
    public function updateUserPresentation($id, Request $request, EntityManagerInterface $entityManagerInterface, UserPresentationsRepository $userPresentationsRepository, ProfilesRepository $profilesRepository): Response
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
        $data = json_decode($request->getContent(), true);
        // dd($data);
        $userPresentation = $userPresentationsRepository->find($id);
        $userPresentation->gender = $data['gender'];
        $userPresentation->website = $data['website'];
        $userPresentation->role = $data['role'];
        $userPresentation->nickname = $data['nickname'];
        $userPresentation->country = $data['country'];
        $userPresentation->languages = $data['languages'];
        $userPresentation->expertise = $data['expertise'];
        $userPresentation->diploma = $data['diploma'];
        $userPresentation->brand_name = $data['brand_name'];
        $userPresentation->contact_phone = $data['contact_phone'];
        $userPresentation->contact_mail = $data['contact_mail'];
        $userPresentation->atrological_sign = $data['atrological_sign'];
        $userPresentation->skills = $data['skills'];
        $userPresentation->presentation = $data['presentation'];
        $userPresentation->contact_phone_comment = $data['contact_phone_comment'];



        $entityManagerInterface->persist($userPresentation);
        $entityManagerInterface->flush();



        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 18;
        $logs->action = 'update';
        $logs->element_id = $userPresentation->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        // $time =  new \DateTimeImmutable();
        // $profile = $profilesRepository->findProfileById($userPresentation->user);
        // $profile->username = $userPresentation->nickname;
        // $entityManagerInterface->persist($profile);
        // $entityManagerInterface->flush();

        // $UserLogs = new UserLogs();
        // $UserLogs->user_id = $data['user_id'];
        // $UserLogs->action = 'Update Profile';
        // $UserLogs->element = '30';
        // $UserLogs->element_id = $profile->id;
        // $UserLogs->log_date = $time;
        // $UserLogs->source = '1';
        // $entityManagerInterface->persist($UserLogs);
        // $entityManagerInterface->flush();

        return new JsonResponse([
            'success' => true,
            'data' => $userPresentation,
        ]);
    }



    #[Route('/delete_user_presentation/{id}', name: 'app_delete_user_presentation_controller')]
    public function deleteUserPresentation($id, Request $request, EntityManagerInterface $entityManagerInterface, UserPresentationsRepository $userPresentationsRepository, ProfilesRepository $profilesRepository): Response
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

        $deleted = false;
        // Now you can access the user data from the token (assuming your User class has a `getUsername()` method)
        // $user = $tokenData->getUser();
        $data = json_decode($request->getContent(), true);
        // dd($data);
        $userPresentation = $userPresentationsRepository->find($id);
  
        if (!$userPresentation) {
          
        } else {
            $logs = new UserLogs();
            $logs->user_id = $data['user_id'];
            $logs->element = 18;
            $logs->action = 'delete';
            $logs->element_id = $userPresentation->id;
            $logs->source = 1;
            $logs->log_date = new \DateTimeImmutable();
            $entityManagerInterface->persist($logs);
            $entityManagerInterface->flush();
            
            $entityManagerInterface->remove($userPresentation); // Assuming $entityManager is your Entity Manager
            $entityManagerInterface->flush();
            $deleted=true;
            // You can return a success response or perform any other required action here.
        }
        



      

 

        return new JsonResponse([
            'success' => true,
            'data' =>  $deleted ,
        ]);
    }

    #[Route('/api/update_user_permission/{id}', name: 'app_update_user_permission_controller')]
    public function updateUserPermission($id, Request $request, EntityManagerInterface $entityManagerInterface, UserPermissionsRepository $userPermissionsRepository): Response
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
        $data = json_decode($request->getContent(), true);
        $userpermission = $userPermissionsRepository->find($id);
        //dd($userpermission);

        if ($data['visitorsRating'] != null) {
            $userpermission->visitors_rating = $data['visitorsRating'];
        } else if ($data['packageVisibility'] != null) {
            $userpermission->package_visibility = $data['packageVisibility'];
        } else if ($data['packageCreation'] != null) {
            $userpermission->package_creation = $data['packageCreation'];
        } else if ($data['planningManagement'] != null) {
            $userpermission->planning_management = $data['planningManagement'];
        } else if ($data['preDefinedMessages'] != null) {
            $userpermission->pre_defined_messages = $data['preDefinedMessages'];
        }




        $entityManagerInterface->persist($userpermission);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 16;
        $logs->action = 'update';
        $logs->element_id = $userpermission->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();


        return new JsonResponse([
            'success' => true,
            'data' => $userpermission,
        ]);
    }

    public function deleteUser(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManagerInterface, AccountsRepository $accountsRepository, UserRepository $userRepository): Response
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
        $data = json_decode($request->getContent(), true);
        $user = new User();
        $account = $accountsRepository->find($request->get('accountId'));
        $user->accountId = $request->get('accountId');
        $user->gender = $request->get('gender');
        $user->lastname = $request->get('lastname');

        $user->firstname = $request->get('firstname');
        $user->email = $request->get('email');
        $user->notification_mail = $request->get('notificationsMail');
        $user->notification_audio = $request->get('notificationAudio');
        $user->notification_browser = $request->get('notificationBrowser');

        $user->status = $request->get('status');
        $user->password = $userPasswordHasher->hashPassword($user, $request->get('password'));


        $user->date_start = new \DateTime('@' . strtotime('now'));

        $entityManagerInterface->persist($user);
        $entityManagerInterface->flush();

        // $logs = new UserLogs();
        // $logs->user_id = $request->get('user_id');
        // $logs->element = 2;
        // $logs->action = 'create';
        // $logs->element_id = $user->id;
        // $logs->source = 1;
        // $logs->log_date = new \DateTimeImmutable();

        // $entityManagerInterface->persist($logs);
        // $entityManagerInterface->flush();
        $userid = $userRepository->find($user->id);






        // $logs = new UserLogs();
        // $logs->user_id = $request->get('user_id');
        // $logs->element = 30;
        // $logs->action = 'create';
        // $logs->element_id = $profiles->id;
        // $logs->source = 1;
        // $logs->log_date = new \DateTimeImmutable();
        // $entityManagerInterface->persist($logs);
        // $entityManagerInterface->flush();








        // $userPermission = new UserPermissions();
        // $userPermission->user = $userid;
        // $userPermission->visitors_rating = $request->get('visitorsRating');
        // $userPermission->package_visibility = $request->get('packageVisibility');
        // $userPermission->package_creation = $request->get('packageCreation');
        // $userPermission->planning_management = $request->get('planningManagement');
        // $userPermission->pre_defined_messages = $request->get('preDefinedMessages');
        // $userPermission->business_tools = $request->get('businessTools');
        // $userPermission->communications = $request->get('communication');
        // $userPermission->status = '1';
        // $userPermission->date_start = new \DateTime('@'.strtotime('now'));
        // $entityManagerInterface->persist($userPermission);
        // $entityManagerInterface->flush();

        return new JsonResponse([
            'success' => true,
            'data' => $user,
        ]);

        //dd($_REQUEST['gender'));
        //$data = json_decode($request->getContent(), true);
        // $userPresentations->setGender($request->request->get('gender'));
        // $userPresentations->setWebsite($request->request->get('website'));
        // $userPresentations->setRole($request->request->get('role'));
        // $userPresentations->setNickname($request->request->get('nickname'));
        // $userPresentations->setCountry($request->request->get('country'));
        // $userPresentations->setLanguages($request->request->get('languages'));
        // $userPresentations->setExpertise($request->request->get('expertise'));
        // $userPresentations->setDiploma($request->request->get('diploma'));
        // $userPresentations->setStatus($request->request->get('status'));
        // $userPresentations->setBrandName($request->request->get('brandName'));
        // $userPresentations->setContactPhone($request->request->get('contactPhone'));
        // $userPresentations->setContactPhoneComment($request->request->get('contactPhoneComment'));
        // $userPresentations->setContactMail($request->request->get('contactMail'));
        // $userPresentations->setAtrologicalSign($request->request->get('atrologicalSign'));
        // $userPresentations->setFile($request->files->get('file'));
        // $userPresentations->setDateStart(new \DateTime('@'.strtotime('now')));









    }
}
