<?php

namespace App\Controller;

use App\Entity\TwoFactorAuthAccount;
use App\Entity\TwoFactorAuthCode;
use App\Entity\TwoFactorAuthRequests;
use App\Repository\TwoFactorAuthAccountRepository;
use App\Repository\TwoFactorAuthRequestsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VerificationController extends AbstractController
{
    #[Route('/tools/2fa/generate', name: 'app_tools_2fa_generate')]
    public function generate(Request $request, EntityManagerInterface $entityManagerInterface, TwoFactorAuthAccountRepository $TwoFactorAuthAccountRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        $receiver = $data['receiver'];

        $account_id = isset($data['account_id']) ? $data['account_id'] :  $request->attributes->get('account');

   

        $method = 1;


        $id_2fa_accounts = null;

        $sql = "SELECT  fa.id , fr.status , fc.id as id_code , fc.status as status_code   FROM `2fa_accounts` AS fa
         LEFT JOIN `2fa_codes` AS fc ON fc.account_id = fa.id
        LEFT JOIN `2fa_requests` AS fr ON fr.code_id = fc.id
        WHERE fr.id  is not null  and  fa.customer_account_id = :account_id and  fa.method = :method and fa.receiver= :receiver  and DATE(fa.date_start) <= CURDATE() and fa.status = 1   AND (fa.date_end IS NULL OR CURDATE() < DATE(fa.date_end)) 
        ";

        $statement = $entityManagerInterface->getConnection()->prepare($sql);
        $statement->bindValue('receiver', $receiver);
        $statement->bindValue('account_id', $account_id);
        $statement->bindValue('method', $method);
        $results = $statement->executeQuery()->fetchAllAssociative();
        $list_code_id_used = []; //for add all old code used
        if (count($results) > 0) {
            $isVerify = false;
            foreach ($results as $row) {
                if (isset($row['status'])) {
                    if ($row['status'] === 3) {
                        $isVerify = true;
                    }
                }
                if (isset($row['id'])) {
                    if (!isset($id_2fa_accounts))
                        $id_2fa_accounts = $row['id'];
                }

                if (isset($row['status_code'])) {
                    if ($row['status_code'] != 3) {
                        $list_code_id_used[] = $row['id_code'];
                    }
                }
            }
            if ($isVerify)
                return new JsonResponse([
                    'success' => false,
                    'error_type' => "READY_VRIFYIED",
                    'generated_code' =>  null,
                ]);
        }
        if (isset($id_2fa_accounts)) {
            $TwoFactorAuthAccount = $TwoFactorAuthAccountRepository->find($id_2fa_accounts);
            if (count($list_code_id_used) > 0) {
                $codes = implode(', ', array_map('intval', $list_code_id_used));
                $sql = "UPDATE 2fa_codes SET status = 3 WHERE id IN ($codes)";
                $statementused = $entityManagerInterface->getConnection()->prepare($sql);
                $statementused->execute();
            }
        } else {
            $TwoFactorAuthAccount = new TwoFactorAuthAccount();
            $TwoFactorAuthAccount->receiver = $receiver;
            $TwoFactorAuthAccount->method = $method;
            $TwoFactorAuthAccount->status = 1;
            $TwoFactorAuthAccount->date_start = new \DateTimeImmutable();
            $TwoFactorAuthAccount->customer_account_id =$account_id;
            $entityManagerInterface->persist($TwoFactorAuthAccount);
            $entityManagerInterface->flush();
        }


        $TwoFactorAuthCode = new TwoFactorAuthCode();
        $TwoFactorAuthCode->account_id  = $TwoFactorAuthAccount->id;
        $TwoFactorAuthCode->code  = mt_rand(100000, 999999); // Generates a random 6-digit number
        $TwoFactorAuthCode->status = 1;
        $TwoFactorAuthCode->date_creation = new \DateTimeImmutable();
        $entityManagerInterface->persist($TwoFactorAuthCode);
        $entityManagerInterface->flush();




        $TwoFactorAuthRequests = new TwoFactorAuthRequests();
        $TwoFactorAuthRequests->account_id = $TwoFactorAuthAccount->id;
        $TwoFactorAuthRequests->code_id = $TwoFactorAuthCode->id;
        $TwoFactorAuthRequests->date_sent  = new \DateTimeImmutable();
        $TwoFactorAuthRequests->status = 1;
        $entityManagerInterface->persist($TwoFactorAuthRequests);
        $entityManagerInterface->flush();



        return new JsonResponse([
            'success' => true,
            'generated_code' =>  $TwoFactorAuthCode->code
        ]);
    }



    #[Route('/tools/2fa/verify', name: 'app_tools_2fa_verify')]
    public function verify(Request $request, EntityManagerInterface $entityManagerInterface, TwoFactorAuthRequestsRepository $TwoFactorAuthRequestsRepository): Response
    {


        $data = json_decode($request->getContent(), true);

        $account_id = isset($data['account_id']) ? $data['account_id'] :  $request->attributes->get('account');

        $method = 1;
        $receiver = $data['receiver'];
        $code = $data['code'];

        $id_2fa_requests = null;

        $sql = "SELECT  fr.id , fr.status,fr.date_sent   , fc.status as status_code   FROM `2fa_accounts` AS fa
        LEFT JOIN `2fa_requests` AS fr ON fr.account_id = fa.id
        LEFT JOIN `2fa_codes` AS fc ON fc.id = fr.code_id
        WHERE fa.method = :method  and  fa.customer_account_id = :account_id and fa.receiver= :receiver and fc.code= :code  and fa.status = 1 
        ";

        $statement = $entityManagerInterface->getConnection()->prepare($sql);
        $statement->bindValue('receiver', $receiver);
        $statement->bindValue('method', $method);
        $statement->bindValue('account_id', $account_id);
        $statement->bindValue('code', $code);
        $results = $statement->executeQuery()->fetchAllAssociative();

        if (count($results) == 0) {
            return new JsonResponse([
                'success' => false,
                'error_type' => "INCORRECET_CODE",
            ]);
        }
        $isREADYVerify = false;
        $isvaliddate = false;
        $isreject = false;
        $isUsed = false;

        foreach ($results as $row) {
            if (isset($row['status_code'])) {
                if ($row['status_code'] === 3) {
                    $isUsed = true;
                    break;
                }
            }

            if (isset($row['status'])) {
                if ($row['status'] === 3) {
                    $isREADYVerify = true;
                    break;
                }
                if ($row['status'] === 2) {
                    $isreject = true;
                    break;
                }
            }
            if (isset($row['date_sent'])) {

                $id_2fa_requests = $row['id'];
                $currentDateTime = new \DateTime();

                $dateSent = new \DateTime($row['date_sent']);
                $dateSent->modify('+1 hour');

                if ($currentDateTime <= $dateSent) {

                    $isvaliddate = true;
                    break;
                }
            }
        }
        if ($isreject)
            return new JsonResponse([
                'success' => false,
                'error_type' => "EXPIRED_DATE",
            ]);

        if ($isUsed) {
            return new JsonResponse([
                'success' => false,
                'error_type' => "INCORRECET_CODE",
            ]);
        }
        if ($isREADYVerify)
            return new JsonResponse([
                'success' => false,
                'error_type' => "READY_VRIFYIED",
            ]);

        if ($isvaliddate == false) {

            $TwoFactorAuthRequests = $TwoFactorAuthRequestsRepository->find($id_2fa_requests);
            $TwoFactorAuthRequests->date_reject = new \DateTimeImmutable();
            $TwoFactorAuthRequests->status = 2;
            $entityManagerInterface->persist($TwoFactorAuthRequests);
            $entityManagerInterface->flush();
            return new JsonResponse([
                'success' => false,
                'error_type' => "EXPIRED_DATE",
            ]);
        }


        $TwoFactorAuthRequests = $TwoFactorAuthRequestsRepository->find($id_2fa_requests);
        $TwoFactorAuthRequests->date_verification = new \DateTimeImmutable();
        $TwoFactorAuthRequests->status = 3;
        $entityManagerInterface->persist($TwoFactorAuthRequests);
        $entityManagerInterface->flush();

        return new JsonResponse([
            'success' => true,
            'verify' => true,

        ]);
    }
}
