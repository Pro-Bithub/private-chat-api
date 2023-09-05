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

class AddCustomFieldsController extends AbstractController
{
    #[Route('/add_custom_fields', name: 'app_add_custom_fields')]
    public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface, AccountsRepository $accountsRepository): Response
    {
        $customFields = new CustomFields();
        $data = json_decode($request->getContent(), true);
        //dump($data);
        $account = $accountsRepository->find($data['account']);
        // $date = DateTime::createFromFormat('Y-m-d', $data['dateStart']);
        // $datediscount = DateTime::createFromFormat('Y-m-d', $data['discountdateStart']);

        $customFields->account = $account;
        $customFields->field_name = $data['fieldName'];
        $customFields->date_start = new \DateTimeImmutable();
        $customFields->status = "1";
        $customFields->field_type = $data['fieldType'];
       
       
       

        $entityManagerInterface->persist($customFields);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 9;
        $logs->action = 'create';
        $logs->element_id = $customFields->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
   
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();
        return new JsonResponse([
            'success' => true,
            'data' => $customFields,
        ]);
    }
}
