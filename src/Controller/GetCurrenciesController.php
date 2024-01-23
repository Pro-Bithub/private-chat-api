<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class GetCurrenciesController extends AbstractController
{
  
    #[Route('/get_currencies', name: 'app_get_currencies_controller')]
    public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface): JsonResponse
    {


        $sql = "SELECT 
       c.* FROM currencies c
      where  c.status =1
      ";

      $statement = $entityManagerInterface->getConnection()->prepare($sql);
      //$statement->bindValue('account_id', $id);
      $results = $statement->executeQuery()->fetchAllAssociative();

         return new JsonResponse([
           'data' => $results,
         ]);
    }
}
