<?php

namespace App\Controller;

use App\Entity\UserLogs;
use App\Entity\UserPermissions;
use App\Repository\UserPermissionsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UpdateUserPermissionController extends AbstractController
{
    // #[Route('/update/user/permission', name: 'app_update_user_permission')]
    public function __invoke($id,Request $request,EntityManagerInterface $entityManagerInterface,UserPermissionsRepository $userPermissionsRepository): Response
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

        if($data['visitorsRating'] != null){
            $userpermission->visitors_rating = $data['visitorsRating'];
        }
         if ($data['packageVisibility'] != null){
            $userpermission->package_visibility = $data['packageVisibility'];
        }
        
         if ($data['packageCreation'] != null){
            $userpermission->package_creation = $data['packageCreation'];
        }
        
         if ($data['planningManagement'] != null){
            $userpermission->planning_management = $data['planningManagement'];
        }
        
         if ($data['preDefinedMessages'] != null){
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


     #[Route('/add/user/permissions', name: 'app_add_user_permission')]
     public function add_user_permission(Request $request,  UserRepository $userRepository,EntityManagerInterface $entityManagerInterface,UserPermissionsRepository $userPermissionsRepository): Response
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
         $userpermission = new UserPermissions();
         //dd($userpermission);
 
       
            $userpermission->visitors_rating = '0';
            $userpermission->package_visibility = '0';
            $userpermission->package_creation ='0';
            $userpermission->planning_management = '0';
            $userpermission->pre_defined_messages ='0';
            $userpermission->business_tools ='0';
            $userpermission->communications ='0';
            $userpermission->status = '1';
            $userpermission->date_start = new \DateTime('@' . strtotime('now'));

            
            
            if($data['visitorsRating'] != null){
                $userpermission->visitors_rating = $data['visitorsRating'];
            }
             if ($data['packageVisibility'] != null){
                $userpermission->package_visibility = $data['packageVisibility'];
            }
            
            if ($data['packageCreation'] != null){
                $userpermission->package_creation = $data['packageCreation'];
            }
            
            if ($data['planningManagement'] != null){
                $userpermission->planning_management = $data['planningManagement'];
            }
            
            if ($data['preDefinedMessages'] != null){
                $userpermission->pre_defined_messages = $data['preDefinedMessages'];
            }
         $old_user = $userRepository->find($data['u_id']);
         $userpermission->user = $old_user;
 
         $entityManagerInterface->persist($userpermission);
         $entityManagerInterface->flush();
 
         $logs = new UserLogs();
         $logs->user_id = $data['user_id'];
         $logs->element = 16;
         $logs->action = 'add';
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

}
