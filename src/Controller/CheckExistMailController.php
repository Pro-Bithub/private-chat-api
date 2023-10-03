<?php

namespace App\Controller;

use App\Entity\User;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[AsController]
class CheckExistMailController extends AbstractController
{
    #[Route('/check/exist/mail', name: 'app_check_exit_mail_controller')]
    public function checkexistmail(
        UserRepository $userRepository,Request $request, EntityManagerInterface $entityManagerInterface
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

        $data = json_decode($request->getContent(), true);

        if( $data['from']=='User'){
            if($data['type']=='old'){
                $sql = "SELECT COUNT(*) as count FROM user u WHERE u.id != :id AND u.email = :email";
                $statement = $entityManagerInterface->getConnection()->prepare($sql);
                $statement->bindValue('id', $data['id']);
                $statement->bindValue('email', $data['mail']);
                $statement->execute();
                
                $result = $statement->executeQuery()->fetchAssociative();
                
                return new JsonResponse([
                    'success' => true,
                    'data' =>  $result['count']===0
                ]);
                
            }
        }
    
       
    
        return new JsonResponse([
            'success' => false,
            'data' => null
        ]);
    }

  
}
