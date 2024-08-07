<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
class GetRegistrationController extends AbstractController
{
    #[Route('/getallregistration')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $authorizationHeader = $request->headers->get('Authorization');

        // Check if the token is present and in the expected format (Bearer TOKEN)
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            throw new AccessDeniedException('Invalid or missing authorization token.');
        }
    
        $sql3= "SELECT r.slug_url FROM `registrations` as r";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();
       // dd($results3);
        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }

    #[Route('/getregistration')]
    
    public function getpresentRegistartion(Request $request, EntityManagerInterface $entityManagerInterface): Response
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

        
        $sql3= "SELECT * FROM `registrations` as r WHERE r.account_id = :id and r.status = 1 order by r.id desc limit 1";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $user->accountId);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }

    #[Route('/checkregistration/slug/{slug}/registrations/{registrations_id}')]
    public function checkregistration($slug,Request $request,$registrations_id,EntityManagerInterface $entityManagerInterface): Response
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

    
        $sql3= "SELECT * FROM `registrations` as r WHERE r.slug_url LIKE :slug  and r.account_id  = :account_id and r.id != :registrations_id";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('slug', $slug);
        $statement3->bindValue('registrations_id', $registrations_id);
        $statement3->bindValue('account_id', $user->accountId);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }

    #[Route('/checkregistration1/{slug}')]
    public function checkregistration1($slug,Request $request,EntityManagerInterface $entityManagerInterface): Response
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


        $sql3= "SELECT * FROM `registrations` as r WHERE r.slug_url LIKE :slug and r.account_id  = :account_id";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('slug', $slug);
        $statement3->bindValue('account_id', $user->accountId);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }
}
