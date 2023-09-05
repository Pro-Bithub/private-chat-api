<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetContactDetailsController extends AbstractController
{
    #[Route('/getProfileByContactId/{id}', name: 'app_get_Profile_By_ContactId_details')]
    public function getProfileByContactId(EntityManagerInterface $entityManagerInterface,$id): Response
    {
        $sql = "SELECT p.*
    FROM `contacts` AS c
    LEFT JOIN `profiles` AS p ON p.u_id = c.id
    WHERE c.id = :id and c.status = 1";
    
    $statement = $entityManagerInterface->getConnection()->prepare($sql);
    $statement->bindValue('id', $id);
    $profiles = $statement->executeQuery()->fetchAssociative();
        
    
    return new JsonResponse([
        'success' => true,
        'data' => $profiles,
    ]);
    }

    #[Route('/getContactByProfileId/{id}', name: 'app_get_Contact_By_Profile_Id_details')]
    public function getContactByProfileId(EntityManagerInterface $entityManagerInterface,$id): Response
    {
        $sql = "SELECT c.* , l.source
    FROM `contacts` AS c
    LEFT JOIN `profiles` AS p ON p.u_id = c.id
    LEFT JOIN `user_logs` AS l on l.element_id = c.id and l.element = 27
    WHERE c.id = :id and c.status = 1";
    
    $statement = $entityManagerInterface->getConnection()->prepare($sql);
    $statement->bindValue('id', $id);
    $profiles = $statement->executeQuery()->fetchAssociative();
       
   
    
    return new JsonResponse([
        'success' => true,
        'data' => $profiles,
       
    ]);
    }

    #[Route('/getProfileAvatarByContactId', name: 'app_get_Profile_avatar_By_ContactId_details')]
    public function getProfileAvatarByContactId(Request $request  ,EntityManagerInterface $entityManagerInterface): Response
    {
        $data = $request->query->all('items') ?? [];
        $sql = "SELECT up.picture , p.id as profile_id 
        FROM `user_presentations` AS up
        LEFT JOIN `user` AS u ON u.id = up.user_id
        LEFT JOIN `profiles` AS p ON p.u_id = u.id
        WHERE p.id regexp :id and u.status = 1";
    
    $statement = $entityManagerInterface->getConnection()->prepare($sql);

    $statement->bindValue('id', implode('|' ,$data));
    $profiles = $statement->executeQuery()->fetchAllAssociative();
        
    
    return new JsonResponse([
        'success' => true,
        'data' => $profiles,
    ]);
    }

    #[Route('/getProfileByAgentId/{id}', name: 'app_get_Profile_By_AgentId_details')]
    public function getProfileByAgentId(EntityManagerInterface $entityManagerInterface,$id): Response
    {
        $sql = "SELECT up.picture, u.firstname, u.lastname , u.id
    FROM `user_presentations` AS up
    LEFT JOIN `user` AS u ON u.id = up.user_id
    LEFT JOIN `profiles` AS p ON p.u_id = u.id
    WHERE p.id = :id and u.status = 1";
    
    $statement = $entityManagerInterface->getConnection()->prepare($sql);
    $statement->bindValue('id', $id);
    $profiles = $statement->executeQuery()->fetchAssociative();
        
    
    return new JsonResponse([
        'success' => true,
        'data' => $profiles,
    ]);
    }
}
