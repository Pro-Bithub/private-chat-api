<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetPlansDicountUserController extends AbstractController
{
    #[Route('/plansDicountUserId/{id}')]
    public function index($id,Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $sql3= "SELECT p.user_id, p.status FROM `plan_discount_users` as p WHERE p.discount_id = :id";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $id);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }

    #[Route('/linkUserId/{id}')]
    public function getuserlink($id,Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $sql3= "SELECT p.user_id , p.status FROM `clickable_links_users` as p WHERE p.link_id = :id";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $id);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }

    #[Route('/textUserId/{id}')]
    public function getusertext($id,Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $sql3= "SELECT p.user_id , p.status FROM `predefined_text_users` as p WHERE p.text_id =:id";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $id);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }



   


}
