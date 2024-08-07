<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GetAccountController extends AbstractController
{
    #[Route('/account/info', name: 'app_account_info')]
    public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface): JsonResponse
    {

        $authorizationHeader = $request->headers->get('Authorization');

        // Check if the token is present and in the expected format (Bearer TOKEN)
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            throw new AccessDeniedException('Invalid or missing authorization token.');
        }


        $token = substr($authorizationHeader, 7);

        $tokenData = $this->get('security.token_storage')->getToken();

        if ($tokenData === null) {
            throw new AccessDeniedException('Invalid token.');
        }

        // Now you can access the user data from the token (assuming your User class has a `getUsername()` method)
        $user = $tokenData->getUser();
        $RAW_QUERY5 =
            'SELECT a.name,a.url,a.date_start
       FROM accounts AS a
       WHERE a.id = :id
        ;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY5);
        $stmt->bindValue('id', $user->accountId);
   
        $results = $stmt->executeQuery()->fetchAllAssociative();

        return new JsonResponse([
            'data' => $results,
            'default_text_additional_comment' =>   "Une application de messagerie privée, payante et sécurisée spécifiquement conçue pour des consultations confidentielles avec des experts qualifiés. En mettant l'accent sur la confidentialité et la sécurité, {APPLICATION} les utilisateurs peuvent engager des consultations vidéo, vocales ou écrites en toute confiance, sachant que leurs données sensibles sont protégées. ",
     
        ]);
    }
}
