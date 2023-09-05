<?php

namespace App\Controller;

use App\Entity\Notes;
use App\Entity\UserLogs;
use App\Repository\ContactsRepository;
use App\Repository\NotesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class NoteController extends AbstractController
{
    #[Route('/getnotebycontactId/{id}', name: 'getnotebycontactId')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface, $id): Response
    {

        $authorizationHeader = $request->headers->get('Authorization');

        // Check if the token is present and in the expected format (Bearer TOKEN)
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            throw new AccessDeniedException('Invalid or missing authorization token.');
        }

        // Extract the token value (without the "Bearer " prefix)
        $token = substr($authorizationHeader, 7);

        $tokenData = $this->get('security.token_storage')->getToken();

        if ($tokenData === null) {
            throw new AccessDeniedException('Invalid token.');
        }

        // Now you can access the user data from the token (assuming your User class has a `getUsername()` method)
        $user = $tokenData->getUser();
        
        $draw = (int) $request->get('draw', 1);
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 5);

        $filters = [];
        $filterValues = [];
        if ($request->get('search')['value'] && trim($request->get('search')['value']) != '') {
            $filters[] = "(n.content LIKE :searchTerm)";
            $filterValues['searchTerm'] = '%' . trim($request->get('search')['value']) . '%';
        }


        if($request->get('columns')) {
            foreach($request->get('columns') as $column){
                if(isset($column['search']['value']) && trim($column['search']['value']) != '') {
                    if($column['name'] == 'n.date_deleted'){
                        
                        if($column['search']['value'] == 'null'){
                            $filters[] = "(".$column['name']." is null)";
                        }else if ($column['search']['value'] == 'not null'){
                            $filters[] = "(".$column['name']." is not null)";
                        }else{
                            $filters[] = "";
                        }
                    }
                    // $filters[] = "(".$column['name']." LIKE :".$column['name'].")";
                    // $filterValues[$column['name']] = $column['search']['value'];
                }
            }
        }
        $sql1 = "SELECT n.id,n.content , n.date_creation , n.date_deleted, u.firstname, u.lastname, u.id as user_id FROM notes n left join user as u on u.id = n.user_id where n.contact_id = :contact_id
         ". (!empty($filters) ? 'AND ' : ''). implode(' AND',$filters)."
        ORDER BY n.id DESC
        LIMIT :limit OFFSET :offset
        ;";

        //dd($sql1,$filters);
        $sql2 = "SELECT n.id,n.content , n.date_creation , n.date_deleted, u.firstname, u.lastname, u.id as user_id FROM notes n left join user as u on u.id = n.user_id where n.contact_id = :contact_id
        ". (!empty($filters) ? 'AND ' : ''). implode(' AND',$filters)."
       ORDER BY n.id DESC
        ;";

        $sql3= "SELECT n.id,n.content , n.date_creation , n.date_deleted, u.firstname, u.lastname, u.id as user_id FROM notes n left join user as u on u.id = n.user_id where n.contact_id = :contact_id";
$statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
$statement3->bindValue('contact_id', $id);
$results3 = $statement3->executeQuery()->rowCount();

$statement = $entityManagerInterface->getConnection()->prepare($sql1);

$statement1 = $entityManagerInterface->getConnection()->prepare($sql2);
foreach($filterValues AS $key => $value) {
$statement->bindValue($key, $value);
$statement1->bindValue($key, $value);
}
// $statement->bindValue('searchTerm', '%' . $search . '%');
$statement->bindValue('limit', $length, \PDO::PARAM_INT);
$statement->bindValue('offset', $start, \PDO::PARAM_INT);

$statement->bindValue('contact_id', $id);
$statement1->bindValue('contact_id', $id);

$results = $statement->executeQuery()->fetchAllAssociative();
$results1 = $statement1->executeQuery()->rowCount();


        // $sql3 = "SELECT n.id,n.content , n.date_creation , n.date_deleted, u.firstname, u.lastname, u.id as user_id FROM notes n left join user as u on u.id = n.user_id where n.contact_id = :contact_id ";
        // $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        // $statement3->bindValue('contact_id', $id);
        // $results = $statement3->executeQuery()->fetchAllAssociative();

        return new JsonResponse([
            'draw' => $draw,
            'recordsTotal' => $results3,
            'recordsFiltered' => $results1,
            'data' => $results,
        ]);
    }
    #[Route('/addnote', name: 'addnote')]
    public function addnote(Request $request, EntityManagerInterface $entityManagerInterface, ContactsRepository $contactsRepository, UserRepository $userRepository): Response
    {
        // account: new FormControl('api/accounts/' + this.userdata.account_id),
        // name: new FormControl('', Validators.required),
        // language: new FormControl('', Validators.required),
        // category: new FormControl('', Validators.required),
        // text: new FormControl('', Validators.required),
        // status: new FormControl('1', Validators.required),
        // dateStart: new FormControl(new Date(), Validators.required),
        // PreDefinedTextUser: new FormControl(null),

        $data = json_decode($request->getContent(), true);
        //dump($data);
        // $account = $accountsRepository->find($data['account']);
        // $date = DateTime::createFromFormat('Y-m-d', $data['dateStart']);
        // $datediscount = DateTime::createFromFormat('Y-m-d', $data['discountdateStart']);

        $sql3 = "SELECT * FROM profiles as p where p.id = :contact_id and p.u_type = 2";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('contact_id', $data['contact_id']);
        $results = $statement3->executeQuery()->fetchAllAssociative();

        $sql1 = "SELECT * FROM profiles as p where p.id = :user_id and p.u_type = 1";
        $statement1 = $entityManagerInterface->getConnection()->prepare($sql1);
        $statement1->bindValue('user_id', $data['user_id']);
        $results1 = $statement1->executeQuery()->fetchAllAssociative();

        if (count($results) > 0 && count($results1) > 0) {


        $contact = $contactsRepository->find($results[0]['u_id']);
        $user = $userRepository->find($results1[0]['u_id']);


        $notes = new Notes();
        $notes->contact = $contact;
        $notes->user = $user;
        $notes->content = $data['content'];
        $notes->date_creation = new \DateTimeImmutable();


            $entityManagerInterface->persist($notes);
            $entityManagerInterface->flush();

            $logs = new UserLogs();
            $logs->user_id = $data['user_id'];
            $logs->element = 31;
            $logs->action = 'create';
            $logs->element_id = $notes->id;
            $logs->source = $data['source'];
            $logs->log_date = new \DateTimeImmutable();

            $entityManagerInterface->persist($logs);
            $entityManagerInterface->flush();

            return new JsonResponse([
                'success' => true,
                'data' => $logs
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'data' => null
            ]);
        }
    }

    #[Route('/addnotebyAdmin', name: 'addnoteadmin')]
    public function addnotebyAdmin(Request $request, EntityManagerInterface $entityManagerInterface, ContactsRepository $contactsRepository, UserRepository $userRepository): Response
    {
        // account: new FormControl('api/accounts/' + this.userdata.account_id),
        // name: new FormControl('', Validators.required),
        // language: new FormControl('', Validators.required),
        // category: new FormControl('', Validators.required),
        // text: new FormControl('', Validators.required),
        // status: new FormControl('1', Validators.required),
        // dateStart: new FormControl(new Date(), Validators.required),
        // PreDefinedTextUser: new FormControl(null),
        $authorizationHeader = $request->headers->get('Authorization');

        // Check if the token is present and in the expected format (Bearer TOKEN)
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            throw new AccessDeniedException('Invalid or missing authorization token.');
        }

        // Extract the token value (without the "Bearer " prefix)
        $token = substr($authorizationHeader, 7);

        $tokenData = $this->get('security.token_storage')->getToken();

        if ($tokenData === null) {
            throw new AccessDeniedException('Invalid token.');
        }
    
        // Now you can access the user data from the token (assuming your User class has a `getUsername()` method)
        // $user = $tokenData->getUser();
        $data = json_decode($request->getContent(), true);
        //dump($data);
        // $account = $accountsRepository->find($data['account']);
        // $date = DateTime::createFromFormat('Y-m-d', $data['dateStart']);
        // $datediscount = DateTime::createFromFormat('Y-m-d', $data['discountdateStart']);

        $contact = $contactsRepository->find($data['contact_id']);
        $user = $userRepository->find($data['user_id']);


        $notes = new Notes();
        $notes->contact = $contact;
        $notes->user = $user;
        $notes->content = $data['content'];
        $notes->date_creation = new \DateTimeImmutable();


        $entityManagerInterface->persist($notes);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 31;
        $logs->action = 'create';
        $logs->element_id = $notes->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        return new JsonResponse([
            'success' => true,
            'data' => $logs
        ]);
    }


    #[Route('/deletenote/{id}', name: 'deletenote')]
    public function deletenote(Request $request, EntityManagerInterface $entityManagerInterface, NotesRepository $notesRepository, $id): Response
    {
        // account: new FormControl('api/accounts/' + this.userdata.account_id),
        // name: new FormControl('', Validators.required),
        // language: new FormControl('', Validators.required),
        // category: new FormControl('', Validators.required),
        // text: new FormControl('', Validators.required),
        // status: new FormControl('1', Validators.required),
        // dateStart: new FormControl(new Date(), Validators.required),
        // PreDefinedTextUser: new FormControl(null),
        $authorizationHeader = $request->headers->get('Authorization');

        // Check if the token is present and in the expected format (Bearer TOKEN)
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            throw new AccessDeniedException('Invalid or missing authorization token.');
        }

        // Extract the token value (without the "Bearer " prefix)
        $token = substr($authorizationHeader, 7);

        $tokenData = $this->get('security.token_storage')->getToken();

        if ($tokenData === null) {
            throw new AccessDeniedException('Invalid token.');
        }
    
        // Now you can access the user data from the token (assuming your User class has a `getUsername()` method)
        // $user = $tokenData->getUser();


        $data = json_decode($request->getContent(), true);
        //dump($data);
        // $account = $accountsRepository->find($data['account']);
        // $date = DateTime::createFromFormat('Y-m-d', $data['dateStart']);
        // $datediscount = DateTime::createFromFormat('Y-m-d', $data['discountdateStart']);



        $notes = $notesRepository->findOneBy(['id' => $id]);


        $notes->date_deleted = new \DateTimeImmutable();


        $entityManagerInterface->persist($notes);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 31;
        $logs->action = 'delete';
        $logs->element_id = $notes->id;
        $logs->source = 3;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        return new JsonResponse([
            'success' => true,
            'data' => $notes
        ]);
    }


    #[Route('/updatenote/{id}', name: 'updatenote')]
    public function updatenote(Request $request, EntityManagerInterface $entityManagerInterface, NotesRepository $notesRepository, $id): Response
    {
        // account: new FormControl('api/accounts/' + this.userdata.account_id),
        // name: new FormControl('', Validators.required),
        // language: new FormControl('', Validators.required),
        // category: new FormControl('', Validators.required),
        // text: new FormControl('', Validators.required),
        // status: new FormControl('1', Validators.required),
        // dateStart: new FormControl(new Date(), Validators.required),
        // PreDefinedTextUser: new FormControl(null),
        $authorizationHeader = $request->headers->get('Authorization');

        // Check if the token is present and in the expected format (Bearer TOKEN)
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            throw new AccessDeniedException('Invalid or missing authorization token.');
        }

        // Extract the token value (without the "Bearer " prefix)
        $token = substr($authorizationHeader, 7);

        $tokenData = $this->get('security.token_storage')->getToken();

        if ($tokenData === null) {
            throw new AccessDeniedException('Invalid token.');
        }
    
        // Now you can access the user data from the token (assuming your User class has a `getUsername()` method)
        // $user = $tokenData->getUser();

        $data = json_decode($request->getContent(), true);
        //dump($data);
        // $account = $accountsRepository->find($data['account']);
        // $date = DateTime::createFromFormat('Y-m-d', $data['dateStart']);
        // $datediscount = DateTime::createFromFormat('Y-m-d', $data['discountdateStart']);



        $notes = $notesRepository->findOneBy(['id' => $id]);
        $notes->content = $data['content'];



        $entityManagerInterface->persist($notes);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 31;
        $logs->action = 'delete';
        $logs->element_id = $notes->id;
        $logs->source = $data['source'];
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        return new JsonResponse([
            'success' => true,
            'data' => $notes
        ]);
    }


    #[Route('/updatenoteadmin/{id}', name: 'updatenoteadmin')]
    public function updatenoteadmin(Request $request, EntityManagerInterface $entityManagerInterface, NotesRepository $notesRepository, $id): Response
    {
        // account: new FormControl('api/accounts/' + this.userdata.account_id),
        // name: new FormControl('', Validators.required),
        // language: new FormControl('', Validators.required),
        // category: new FormControl('', Validators.required),
        // text: new FormControl('', Validators.required),
        // status: new FormControl('1', Validators.required),
        // dateStart: new FormControl(new Date(), Validators.required),
        // PreDefinedTextUser: new FormControl(null),

        $data = json_decode($request->getContent(), true);
        //dump($data);
        // $account = $accountsRepository->find($data['account']);
        // $date = DateTime::createFromFormat('Y-m-d', $data['dateStart']);
        // $datediscount = DateTime::createFromFormat('Y-m-d', $data['discountdateStart']);



        $notes = $notesRepository->findOneBy(['id' => $id]);
        $notes->content = $data['content'];


        $entityManagerInterface->persist($notes);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 31;
        $logs->action = 'update';
        $logs->element_id = $notes->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        return new JsonResponse([
            'success' => true,
            'data' => $notes
        ]);
    }
}
