<?php

namespace App\Controller;

use App\Entity\CustomFields;
use App\Entity\UserLogs;
use App\Repository\AccountsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddlogsController extends AbstractController
{
    #[Route('/add_logs', name: 'app_add_logs')]
    public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $data = json_decode($request->getContent(), true);
        //dump($data);

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = $data['element'];
        $logs->action = $data['action'];
        $logs->element_id = $data['element_id'];
        $logs->source = $data['source'];
        $logs->log_date = new \DateTimeImmutable();
   
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();
        return new JsonResponse([
            'success' => true,
            'data' => $logs,
        ]);
    }
}
