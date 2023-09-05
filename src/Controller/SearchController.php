<?php

namespace App\Controller;

use App\Repository\ContactFormsRepository;
use App\Repository\ContactsRepository;
use App\Repository\LandingPagesRepository;
use App\Repository\PlansRepository;
use App\Repository\PredefindTextsRepository;
use App\Repository\SalesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
    * @var PlansRepository
    * @var UserRepository
    * @var PredefindTextsRepository
    * @var ContactsRepository
    * @var ContactFormsRepository
    * @var LandingPagesRepository
    * @var SalesRepository
    */
    private $PlansRepository;
    private $UserRepository;
    private $PredefindTextsRepository;
    private $contactsRepository;
    private $ContactFormsRepository;
    private $LandingPagesRepository;
    private $SalesRepository;

    public function __construct(SalesRepository $SalesRepository,LandingPagesRepository $LandingPagesRepository,PlansRepository $PlansRepository, UserRepository $UserRepository, PredefindTextsRepository $PredefindTextsRepository, ContactsRepository $contactsRepository, ContactFormsRepository $ContactFormsRepository)
    {
        $this->PlansRepository = $PlansRepository;
        $this->UserRepository = $UserRepository;
        $this->PredefindTextsRepository = $PredefindTextsRepository;
        $this->contactsRepository = $contactsRepository;
        $this->ContactFormsRepository = $ContactFormsRepository;
        $this->LandingPagesRepository = $LandingPagesRepository;
        $this->SalesRepository = $SalesRepository;
    }
    #[Route('/search/{id}')]
        /**
     * Undocumented function
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request, $id, EntityManagerInterface $entityManagerInterface)
    {
        $data = [];
        $sql1= "SELECT p.id , p.name FROM `plans` as p WHERE p.id = :id  OR p.name LIKE :searchTerm";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql1);
        $statement3->bindValue('id', $id);
        $statement3->bindValue('searchTerm', '%'.$id.'%');
        $plans = $statement3->executeQuery()->fetchAllAssociative();

        $sql2= "SELECT u.id , u.firstname , u.lastname, u.email FROM `user` as u WHERE u.id = :id  OR u.firstname LIKE :searchTerm OR u.lastname LIKE :searchTerm OR u.email LIKE :searchTerm";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql2);
        $statement3->bindValue('id', $id);
        $statement3->bindValue('searchTerm', '%'.$id.'%');
        $users = $statement3->executeQuery()->fetchAllAssociative();


        $sql3= "SELECT p.id , p.name FROM `predefind_texts` as p WHERE p.id = :id  OR p.name LIKE :searchTerm";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $id);
        $statement3->bindValue('searchTerm', '%'.$id.'%');
        $PredefindTexts = $statement3->executeQuery()->fetchAllAssociative();


        $sql4= "SELECT c.id , c.firstname , c.lastname, c.email FROM `contacts` as c WHERE c.id = :id  OR c.firstname LIKE :searchTerm OR c.lastname LIKE :searchTerm OR c.email LIKE :searchTerm";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql4);
        $statement3->bindValue('id', $id);
        $statement3->bindValue('searchTerm', '%'.$id.'%');
        $Contacts = $statement3->executeQuery()->fetchAllAssociative();

        $sql5= "SELECT c.id , c.friendly_name FROM `contact_forms` as c WHERE c.id = :id OR c.friendly_name LIKE :searchTerm";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql5);
        $statement3->bindValue('id', $id);
        $statement3->bindValue('searchTerm', '%'.$id.'%');
        $ContactForms = $statement3->executeQuery()->fetchAllAssociative();
        // $plans = $this->PlansRepository->searchPlan($id);
        // $users = $this->UserRepository->searchUser($id);
        // $PredefindTexts = $this->PredefindTextsRepository->searchPredefindTexts($id);

        $sql6="SELECT l.id , l.name FROM `landing_pages` as l WHERE l.id = :id  OR l.name LIKE :searchTerm";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql6);
        $statement3->bindValue('id', $id);
        $statement3->bindValue('searchTerm', '%'.$id.'%');
        $LandingPages = $statement3->executeQuery()->fetchAllAssociative();

        $sql7= "SELECT p.id FROM `sales` as p WHERE p.id = :id";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql7);
        $statement3->bindValue('id', $id);
        $Sales = $statement3->executeQuery()->fetchAllAssociative();

        // $Contacts = $this->contactsRepository->searchContacts($id);
        // $ContactForms = $this->ContactFormsRepository->searchContactForms($id);
        // $LandingPages = $this->LandingPagesRepository->searchLandingPages($id);
        // $Sales = $this->SalesRepository->searchSales($id);
        array_push($data, $plans, $users, $PredefindTexts, $Contacts, $ContactForms, $LandingPages, $Sales);
        return $this->json($data);
      //  dd($plans_account);
        //dd($);
       
        
    }
}
