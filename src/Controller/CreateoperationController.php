<?php

namespace App\Controller;

use App\Entity\ContactOperations;
use App\Entity\UserLogs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateoperationController extends AbstractController
{
    #[Route('/createoperation5')]
    public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface): response
    {

        
        $contact_op = new ContactOperations();
        $data = json_decode($request->getContent(), true);
       // dd($data['contact_id']);
        $contact_op->operation =  $data['operation'];
        $contact_op->operation_id =  $data['operation_id'];
        $contact_op->status = '1';
        $contact_op->contact_id = $data['contact_id'];
        $entityManagerInterface->persist($contact_op);
        $entityManagerInterface->flush();
        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 28;
        $logs->action = $data['action'];
        $logs->element_id = $contact_op->id;
        $logs->source = 2;
        $logs->log_date = new \DateTimeImmutable();
       
        $entityManagerInterface->persist($logs);
            $entityManagerInterface->flush();
     
      

        return new JsonResponse([
            'success' => 'true',
            'data' => $logs
        ]);
  
    }
}
