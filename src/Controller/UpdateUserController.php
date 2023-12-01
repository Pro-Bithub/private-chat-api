<?php

namespace App\Controller;

use App\Entity\Profiles;
use App\Entity\User;
use App\Entity\UserLogs;
use App\Entity\UserPresentations;
use App\Repository\ProfilesRepository;
use App\Repository\UserPresentationsRepository;
use App\Repository\UserRepository;
use App\services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
#[AsController]
class UpdateUserController extends AbstractController
{
    /**
    * @var UserPresentationsRepository
    */
    private $UserPresentationsRepository;
    public function __construct(UserPresentationsRepository $UserPresentationsRepository)
    {
        $this->UserPresentationsRepository = $UserPresentationsRepository;
    }
    public function __invoke(Request $request, FileUploader $fileUploader, EntityManagerInterface $entityManagerInterface, SluggerInterface $slugger): UserPresentations
    {
        
        
        //$userPresentations = new UserPresentations();
        $data = json_decode($request->getContent(), true);
        //dd($request->get('idUser'));
        $userPresentations = $this->UserPresentationsRepository->findOneById($request->get('id_p'));
        //dd($userPresentations);
        
        $uploadedFile = $request->files->get('file');
       // $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
       // $safeFilename = $slugger->slug($originalFilename);
        $fileName = $userPresentations->id.'.'.$uploadedFile->guessExtension();

        // dd($fileName);

        try {
                // $uploadedFile->move($this->getuploadPath(), $fileName);
                    $userPresentations->picture = $fileUploader->upload($uploadedFile);
                    //$data = array("data" => "File is valid, and was successfully uploaded.");
                    } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    $data = array("data" => "File is not valid, and was successfully uploaded.");
            }

       
        
        // $entityManagerInterface->persist($userPresentations);
        // $entityManagerInterface->flush();

        return $userPresentations;
    }
    #[Route('/update/user/info/{id}', name: 'app_update_user_info_controller')]
    public function updateUserInfo($id, ProfilesRepository $profilesRepository,Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManagerInterface,  UserRepository $userRepository): Response
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


        $data = json_decode($request->getContent(), true);
        $updated_user = $userRepository->find($id);
        $updated_user->accountId = $user2->accountId;
        $updated_user->gender = $data['gender'];
        $updated_user->lastname = $data['lastname'];
        $updated_user->firstname = $data['firstname'];
        $updated_user->email = $data['email'];

        $updated_user->notification_audio = $data['notification_audio'];
        $updated_user->notification_browser = $data['notification_browser'];
        $updated_user->notification_mail = $data['notification_mail'];

    

        if( $data['newPassword']!="")
        $updated_user->password = $userPasswordHasher->hashPassword($updated_user, $data['newPassword']);
        $entityManagerInterface->persist($updated_user);
        $entityManagerInterface->flush();
        

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 2;
        $logs->action = 'update';
        $logs->element_id = $updated_user->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();
  

        $profile = $profilesRepository->findProfileByIduser($updated_user->id);
        $profile->accountId = $updated_user->accountId;
        $profile->password = $updated_user->password;
        $profile->login = $updated_user->email;
     

        $entityManagerInterface->persist($profile);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 30;
        $logs->action = 'update';
        $logs->element_id = $profile->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

       
        return new JsonResponse([
            'success' => true,
            'data' => $updated_user
        ]);


    }


}
