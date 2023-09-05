<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Entity\UserLogs;
use App\Repository\ProfilesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    #[Route('/message', name: 'app_message')]
    public function index(): Response
    {
        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
        ]);
    }
    #[Route('/addmessage', name: 'addmessage')]
    public function addmessage(Request $request, EntityManagerInterface $entityManagerInterface, ProfilesRepository $ProfilesRepository): Response
    {
       
       
        $data = json_decode($request->getContent(), true);
       
        $messages = new Messages();
        $messages->sender_id = $data['sender_id'];
        $messages->receiver_id = $data['receiver_id'];
        $messages->date_sent = new \DateTimeImmutable();
        // $messages->date_seen = $data['receiver_id'];
        $messages->status = "1";
        $messages->message = $data['message'];

        $entityManagerInterface->persist($messages);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['sender_id'];
        $logs->element = 32;
        $logs->action = 'create';
        $logs->element_id = $messages->id;
        $logs->source = $data['source'];
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();
        
        return new JsonResponse([
            'success' => true,
            'data' => $messages,
        ]);
    }
}
