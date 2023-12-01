<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetUserAccountController extends AbstractController
{

#[Route('/GetUserAccount/{id}', name: 'app_get_user_account')]
public function GetUserAccount(EntityManagerInterface $entityManagerInterface,$id): Response
{ 
    $sql = "SELECT a.id, a.firstname, a.lastname, b.* FROM `user` a LEFT JOIN user_presentations b ON a.id = b.user_id  and  b.status =1 WHERE a.account_id = :account_id ";
    $statement = $entityManagerInterface->getConnection()->prepare($sql);
    $statement->bindValue('account_id', $id);
    $contactForms = $statement->executeQuery()->fetchAllAssociative();           

    return new JsonResponse([
        'success' => true,
        'total' => sizeof($contactForms),
        'data' => $contactForms,
    ]);
}

}
