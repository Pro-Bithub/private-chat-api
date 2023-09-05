<?php

namespace App\Controller;

use App\Entity\UserLogs;
use App\Repository\ContactBalancesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UpdateBalanceController extends AbstractController
{
    /**
     * @var ContactBalancesRepository
     */
    private $ContactBalancesRepository;
    public function __construct(ContactBalancesRepository $ContactBalancesRepository)
    {
        $this->ContactBalancesRepository = $ContactBalancesRepository;
    }


    #[Route('/updateBalance')]
    public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $data = json_decode($request->getContent(), true);
        $sql2 = "SELECT * FROM `profiles` as p WHERE p.id = :id";
        $statement2 = $entityManagerInterface->getConnection()->prepare($sql2);
        $statement2->bindValue('id', $data['contact_id']);
        $results = $statement2->executeQuery()->fetchAllAssociative();

        //dd($results[0]['u_id']);

        if (count($results) > 0) {
            $sql3 = "SELECT b.* FROM `contact_balances` as b WHERE b.contact_id = :id and b.balance_type = :type and b.balance > 0 order by b.id desc limit 1";
            $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
            $statement3->bindValue('id', $results[0]['u_id']);
            $statement3->bindValue('type', $data['type']);
            $result = $statement3->executeQuery()->fetchAssociative();
            //  dd($result);
            // $contact_balances = $this->ContactBalancesRepository->findOneById($id);
            //   $user = new User();
            // dd($contact_balances->contact->id);
            
            $contact_balances = $this->ContactBalancesRepository->findOneById($result['id']);
            $contact_balances->balance = $data['total'];

            //$user->password = $userPasswordHasher->hashPassword($user,$request->get('password'));
            // $plainPassword = $request->get('password');
            // $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
            // $user->setPassword($hashedPassword);


            $entityManagerInterface->persist($contact_balances);
            $entityManagerInterface->flush();
// dd($contact_balances->contact);
            $logs = new UserLogs();
            $logs->user_id = $contact_balances->contact->id;
            $logs->element = 24;
            $logs->action = 'update';
            $logs->element_id = $contact_balances->id;
            $logs->source = 3;
            $logs->log_date = new \DateTimeImmutable();

            $entityManagerInterface->persist($logs);
            $entityManagerInterface->flush();
            

            $sql4 = "SELECT SUM(b.balance) as balance, b.balance_type FROM `contact_balances` as b WHERE b.contact_id = :id GROUP BY b.balance_type having count(*) > 0";
            $statement4 = $entityManagerInterface->getConnection()->prepare($sql4);
            $statement4->bindValue('id', $results[0]['u_id']);
            $result4 = $statement4->executeQuery()->fetchAssociative();

            return new JsonResponse([
                'success' => true,
                'data' => $result4,
                'log' => $logs
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'data' => null,
            ]);
        }
    }
}
