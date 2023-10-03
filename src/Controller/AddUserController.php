<?php

namespace App\Controller;

use App\Entity\UserLogs;
use App\Entity\UserPresentations;
use App\Repository\AccountsRepository;
use App\Repository\UserRepository;
use App\services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[AsController]
class AddUserController extends AbstractController
{
    public function __invoke(Request $request, FileUploader $fileUploader): UserPresentations
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
        
        $userPresentations = new UserPresentations();

        $userPresentations->gender = $request->get('gender');
        $userPresentations->website = $request->get('website');
        if($request->files->get('file') != null){
            $uploadedFile = $request->files->get('file');
            $userPresentations->picture = $fileUploader->upload($uploadedFile);
        }
       
        $userPresentations->role = $request->get('role');
        $userPresentations->nickname = $request->get('nickname');
        $userPresentations->country = $request->get('country');
        $userPresentations->languages = $request->get('languages');
        $userPresentations->expertise = $request->get('expertise');
        $userPresentations->diploma = $request->get('diploma');
        $userPresentations->status = $request->get('status');
        $userPresentations->brand_name = $request->get('brandName');
        $userPresentations->contact_phone = $request->get('contactPhone');
        $userPresentations->contact_mail = $request->get('contactMail');
        $userPresentations->atrological_sign = $request->get('atrologicalSign');
        $userPresentations->user = $request->get('user');
        $userPresentations->date_start = new \DateTime('@'.strtotime('now'));
        $userPresentations->skills = $request->get('skills');
        $userPresentations->presentation = $request->get('presentation');
        $userPresentations->contact_phone_comment =$request->get('contactPhoneComment');
     
      

        return $userPresentations;
  

        //dd($_REQUEST['gender']);
      //  $data = json_decode($request->getContent(), true);
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
        
    //     $userPresentations->gender = $data['gender'];
    //     $userPresentations->website = $data['website'];
    //     $userPresentations->role = $data['role'];
    //     $userPresentations->nickname = $data['nickname'];
    //     $userPresentations->country = $data['country'];
    //     $userPresentations->languages = $data['expertise'];
    //     $userPresentations->expertise = $data['expertise'];
    //     $userPresentations->diploma = $data['diploma'];
    //     $userPresentations->status = $data['status'];
    //     $userPresentations->brand_name = $data['brandName'];
    //     $userPresentations->contact_phone = $data['contactPhone'];
    //     $userPresentations->contact_phone_comment =$data['contactPhoneComment'];
    //     $userPresentations->contact_mail = $data['contactMail'];
    //     $userPresentations->atrological_sign = $data['atrologicalSign'];
    //    // $userPresentations->user = $data['user'];
    //     $userPresentations->date_start = new \DateTime('@'.strtotime('now'));
    //    // $userPresentations->picture = $fileUploader->upload($uploadedFile);
    //     $userPresentations->setFile($data['file']);
        // $userPresentations->picture = $data['picture']; 
        // $userPresentations->setFile($data['picture']->getData());



        



       
    }
    
    #[Route('/delete_user/{id}', name: 'app_delete_user_controller')]
    public function deleteuser(
        $id,
        UserRepository $userRepository,
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
        $user = $userRepository->find($id);
        $data = json_decode($request->getContent(), true);
        $user->date_end = new \DateTimeImmutable();
        $user->status = '0';
    
       
        //dd($clickableLinksUser);
        $entityManagerInterface->persist($user);
        $entityManagerInterface->flush();
    
        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 2;
        $logs->action = 'delete';
        $logs->element_id = $user->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
    
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();
    
       
    
        return new JsonResponse([
            'success' => true,
            'data' => $user,
        ]);
    }


    #[Route('/get_account/{id}', name: 'app_get_account_controller')]
    public function getAccount(
        $id,
        AccountsRepository $accountsRepository, EntityManagerInterface $entityManagerInterface
    ): Response {
        $sql3= "SELECT * FROM accounts a where a.id = :id";
$statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
$statement3->bindValue('id', $id);
$results3 = $statement3->executeQuery()->fetchAssociative();
        //$account = $accountsRepository->find($id);
        //dd($results3);
        // $data = json_decode($request->getContent(), true);
        // $user->date_end = new \DateTimeImmutable();
        // $user->status = '0';
    
       
        // //dd($clickableLinksUser);
        // $entityManagerInterface->persist($user);
        // $entityManagerInterface->flush();
    
        // $logs = new UserLogs();
        // $logs->user_id = $data['user_id'];
        // $logs->element = 2;
        // $logs->action = 'delete';
        // $logs->element_id = $user->id;
        // $logs->source = 1;
        // $logs->log_date = new \DateTimeImmutable();
    
        // $entityManagerInterface->persist($logs);
        // $entityManagerInterface->flush();
    
       
    
        return new JsonResponse([
            'success' => true,
            'data' => $results3
        ]);
    }
}


