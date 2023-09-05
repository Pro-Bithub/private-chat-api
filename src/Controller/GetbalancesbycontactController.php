<?php

namespace App\Controller;

use App\Repository\ContactBalancesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetbalancesbycontactController extends AbstractController
{
    /**
    * @var ContactBalancesRepository
    */
    private $ContactBalancesRepository;
    public function __construct(ContactBalancesRepository $ContactBalancesRepository)
    {
        $this->ContactBalancesRepository = $ContactBalancesRepository;
    }

    public function __invoke(Request $request, $id)
    {

        $contact_balances = $this->ContactBalancesRepository->loadbalancesByContact($id);
        //dd($user);
        return $contact_balances;
    }

    #[Route('/api/getTotalBalance/{id}')]
    public function gettotalbalance(Request $request, $id, EntityManagerInterface $entityManagerInterface)
    {
        $sql2= "SELECT * FROM `profiles` as p WHERE p.id = :id";
        $statement2 = $entityManagerInterface->getConnection()->prepare($sql2);
        $statement2->bindValue('id', $id);
        $results = $statement2->executeQuery()->fetchAllAssociative();

       // dd($results[0]['u_id']);

if(count($results) > 0){

        $sql3= "SELECT SUM(b.balance) as balance, b.balance_type FROM `contact_balances` as b WHERE b.contact_id = :id GROUP BY b.balance_type having count(*) > 0";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $results[0]['u_id']);
        $results = $statement3->executeQuery()->fetchAllAssociative();
        // $contact_balances = $this->ContactBalancesRepository->loadbalancesByContact($id);
        //dd($user);
        return new JsonResponse([
            'success' => true,
            'data' => $results
        ]);
       
    }
    }

}
