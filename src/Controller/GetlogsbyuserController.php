<?php

namespace App\Controller;

use App\Repository\UserLogsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetlogsbyuserController extends AbstractController
{
    /**
    * @var UserLogsRepository
    */
    private $UserLogsRepository;
    public function __construct(UserLogsRepository $UserLogsRepository)
    {
        $this->UserLogsRepository = $UserLogsRepository;
    }

    public function __invoke(Request $request,  EntityManagerInterface $entityManagerInterface,$id): Response
    {
        //$logs_user = $this->UserLogsRepository->loadlogsByUser($id);

        $RAW_QUERY4 = 'SELECT l.id , l.log_date, l.user_id, l.action , l.element, l.element_id, l.source , u.firstname, u.lastname, u.email FROM user_logs l INNER JOIN user u ON u.id = l.user_id  WHERE l.user_id = :id;';
        $stmt4 = $entityManagerInterface->getConnection()->prepare($RAW_QUERY4);
        $stmt4->bindValue('id', $id);
        $result4 = $stmt4->executeQuery()->fetchAllAssociative();
        //dd($);
         return new JsonResponse([
            'success' => 'true',
            'data' => $result4
        ]);
    }
}
