<?php

namespace App\Controller;

use App\Entity\ContactBalances;
use App\Entity\Sales;
use App\Entity\UserLogs;
use App\Repository\ContactsRepository;
use App\Repository\PlansRepository;
use App\Repository\PlanTariffsRepository;
use App\Repository\SalesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SalesController extends AbstractController
{
    #[Route('/add_sales', name: 'app_sales')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface,PlanTariffsRepository $planTariffsRepository, ContactsRepository $contactsRepository, UserRepository $userRepository, PlansRepository $plansRepository): Response
    {
        $sales = new Sales();
        $data = json_decode($request->getContent(), true);
        //dump($data);
        $sql2 = "SELECT * FROM `profiles` as p WHERE p.id = :id";
        $statement2 = $entityManagerInterface->getConnection()->prepare($sql2);
        $statement2->bindValue('id', $data['contact']);
        $results = $statement2->executeQuery()->fetchAllAssociative();




        // dd($results[0]['u_id']);

        if ($data['agent_id'] != null) {

            $sql2 = "SELECT * FROM `user` as p WHERE p.id = :id";
            $statement2 = $entityManagerInterface->getConnection()->prepare($sql2);
            $statement2->bindValue('id', $data['agent_id']);
            $user1 = $statement2->executeQuery()->fetchAssociative();
            $user = $userRepository->find($user1['id']);




            $contact = $contactsRepository->find($results[0]['u_id']);
            $plans = $plansRepository->find($data['plan']);
            // $date = DateTime::createFromFormat('Y-m-d', $data['dateStart']);
            // $datediscount = DateTime::createFromFormat('Y-m-d', $data['discountdateStart']);

            $sales->contact = $contact;
            $sales->user = $user;
            $sales->date_creation = new \DateTimeImmutable();
            $sales->status = $data['sale_status'];
            $sales->plan = $plans;
            $sales->payment_method = $data['payment_method'];
            $sales->provider_id = $data['provider_id'];

       
            $sales->p_id = $data['p_id'] != null?$data['p_id']:null;

            $sales->tariff_id = $data['tariff'] != null?$data['tariff']:null;

          

            $entityManagerInterface->persist($sales);
            $entityManagerInterface->flush();
        } else {
            $contact = $contactsRepository->find($results[0]['u_id']);
            $plans = $plansRepository->find($data['plan']);
            // $date = DateTime::createFromFormat('Y-m-d', $data['dateStart']);
            // $datediscount = DateTime::createFromFormat('Y-m-d', $data['discountdateStart']);

            $sales->contact = $contact;
            $sales->user = null;
            $sales->date_creation = new \DateTimeImmutable();
            $sales->status = $data['sale_status'];
            $sales->plan = $plans;
            $sales->payment_method = $data['payment_method'];
            $sales->provider_id = $data['provider_id'];


            $sales->tariff_id = $data['tariff'] != null?$data['tariff']:null;

            $entityManagerInterface->persist($sales);
            $entityManagerInterface->flush();
        }
        $logs = new UserLogs();
        $logs->user_id = $data['contact'];
        $logs->element = 14;
        $logs->action = 'create';
        $logs->element_id = $sales->id;
        $logs->source = 3;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();



        

        $tariff = $planTariffsRepository->find($data['tariff']);

        


        


        return new JsonResponse([
            'success' => true,
            'sale_id' => $sales->id,
            'payment_method' => $sales->payment_method,
            'agent_id' => $sales->user == null ? null : $sales->user->id,
            'contact_name' => $sales->contact->name,
            'lastname' => $sales->contact->lastname,
            'firstname' => $sales->contact->firstname,
            'sale_status' => $sales->status,
            'plan_name' => $plans->name,
            'plan_tariff' => $tariff->price,
            'plan_currency' => $tariff->currency,


        ]);
    }


    #[Route('/update_sales', name: 'app_update_sales')]
    public function updateSales(Request $request, EntityManagerInterface $entityManagerInterface, SalesRepository $salesRepository): Response
    {

        $data = json_decode($request->getContent(), true);

        $sales = $salesRepository->find($data['sale_id']);
        $sales->status = $data['status'];
        $sales->date_end = new \DateTimeImmutable();


        $entityManagerInterface->persist($sales);
        $entityManagerInterface->flush();





        $logs = new UserLogs();
        $logs->user_id =  $sales->contact->id;
        $logs->element = 14;
        $logs->action = 'update';
        $logs->element_id = $sales->id;
        $logs->source = 3;
        $logs->log_date = new \DateTimeImmutable();
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        if ($data['status'] === 2) {
            return new JsonResponse([
                'success' => true,
                'sale_id' => $sales->id,
                'payment_method' => $sales->payment_method,
                'agent_id' => $sales->user == null ? null : $sales->user->id,
                'contact_name' => $sales->contact->name,
                'lastname' => $sales->contact->lastname,
                'firstname' => $sales->contact->firstname,
                'sale_status' => $sales->status,
                'plan_name' => $sales->plan->name,
                'plan_id' => $sales->plan->id,
                'plan_tariff' => $sales->plan->tariff,
                'plan_currency' => $sales->plan->currency,
            ]);
        } else if ($data['status'] === 1) {

            $balance = new ContactBalances();
            $balance->contact = $sales->contact;
            $balance->balance_type = $sales->plan->billing_type;
            $balance->balance = $sales->plan->billing_volume;
            $balance->request = "1";
            $balance->request_id = $sales->id;

            $entityManagerInterface->persist($balance);
            $entityManagerInterface->flush();

            $logs = new UserLogs();
            $logs->user_id = $sales->contact->id;
            $logs->element = 24;
            $logs->action = 'create';
            $logs->element_id = $balance->id;
            $logs->source = 3;
            $logs->log_date = new \DateTimeImmutable();
            $entityManagerInterface->persist($logs);
            $entityManagerInterface->flush();

            $sql3 = "SELECT SUM(b.balance) as balance, b.balance_type  FROM `contact_balances` as b WHERE b.contact_id = :id GROUP BY b.balance_type having count(b.balance) > 0";
            $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
            $statement3->bindValue('id', $sales->contact->id);
            $results6 = $statement3->executeQuery()->fetchAllAssociative();

            return new JsonResponse([
                'success' => true,
                'sale_id' => $sales->id,
                'payment_method' => $sales->payment_method,
                'agent_id' => $sales->user == null ? null : $sales->user->id,
                'contact_name' => $sales->contact->name,
                'lastname' => $sales->contact->lastname,
                'firstname' => $sales->contact->firstname,
                'sale_status' => $sales->status,
                'plan_name' => $sales->plan->name,
                'plan_tariff' => $sales->plan->tariff,
                'plan_currency' => $sales->plan->currency,
                'plan_id' => $sales->plan->id,
                'id' => $balance->id,
                'balance_type' => $balance->balance_type,
                'balance' => $balance->balance,
                'Total_balance' => $results6,
            ]);
        } else {
            return new JsonResponse([
                'success' => false,

            ]);
        }


        /*      $sql2 = "SELECT * FROM `profiles` as p WHERE p.id = :id";
        $statement2 = $entityManagerInterface->getConnection()->prepare($sql2);
        $statement2->bindValue('id', $sales->contact->id);
        $results = $statement2->executeQuery()->fetchAllAssociative(); */
    }


    // #[Route('/get_sales/{id}', name: 'app_get_sales_controller')]
    // public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface,$id): JsonResponse
    // {




    //     $draw = (int) $request->get('draw', 1);
    //     $start = (int) $request->get('start', 0);
    //     $length = (int) $request->get('length', 5);
    //     $search = $request->get('columns')[1]['search']['value'] ?? null;

    //     $columns = $request->get('columns');

    //     $order = $request->get('order');

    //     $sort = [];
    //     foreach ($order as  $orders){
    //         if(isset($columns[$orders['column']]['name'])){
    //             $sort[] = $columns[$orders['column']]['name']. ' ' .$orders['dir'];
    //         } 
    //     }


    //     $filters = [];
    //     $filterValues= [];

    //     $filtershaving = [];
    //     $filterValueshaving= [];
    //     if($request->get('search')['value'] &&  trim($request->get('search')['value']) != '') {
    //     $filters[] = "(e.id LIKE :searchTerm OR e.name LIKE :searchTerm OR e.email LIKE :searchTerm OR e.lastname LIKE :searchTerm OR e.firstname LIKE :searchTerm OR e.country LIKE :searchTerm OR e.phone LIKE :searchTerm)";
    //     $filterValues['searchTerm'] = '%'.trim($request->get('search')['value']).'%';
    //     }

    //     if($request->get('columns')) {
    //         foreach($request->get('columns') as $column){

    //             if(isset($column['search']['value']) && trim($column['search']['value']) != '') {
    //                 if($column['name'] == 'total_sales') {
    //                     $filtershaving[] = "(total_sales = :total_sales)";
    //                     $filterValueshaving[str_replace('.', '_',$column['name'])] = str_replace('%', '', $column['search']['value']);
    //                 }else{
    //                 $filters[] = "(".$column['name']." LIKE :".str_replace('.', '_',$column['name']).")";
    //                 $filterValues[str_replace('.', '_',$column['name'])] = $column['search']['value'];
    //             }

    //         }
    //         }

    //         }


    //         $sql1 = "SELECT s.* , p.* , u.*
    //         FROM sales s
    //         left JOIN user u ON s.user_id = u.id
    //         left JOIN plans p ON s.plan_id = p.id
    //              ". (!empty($filters) ? 'where s.account_id = :account_id and' : ''). implode(' AND',$filters)."
    //              GROUP BY s.id
    //              ". (!empty($filtershaving) ? ' having ' : ''). implode(' AND',$filtershaving)."
    //             ". (!empty($sort) ? 'order BY ' : ''). implode(' ,',$sort)."
    //             LIMIT :limit OFFSET :offset
    //             ;";

    //             //dd($sql1,$filters);
    //             $sql2 = "SELECT e.* , COUNT(d.contact_id) as total_sales
    //             FROM contacts e
    //             left JOIN sales d ON d.contact_id = e.id
    //             ". (!empty($filters) ? 'where e.account_id = :account_id and' : ''). implode(' AND',$filters)."
    //             GROUP BY e.id
    //             ". (!empty($filtershaving) ? ' having ' : ''). implode(' AND',$filtershaving)."
    //             ". (!empty($sort) ? 'order BY ' : ''). implode(' ,',$sort)."

    //             ;";

    //             $sql3= "SELECT * FROM contacts where account_id = :account_id";

    //     $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
    //     $statement3->bindValue('account_id',$id);

    //     $results3 = $statement3->executeQuery()->rowCount();

    //     $statement = $entityManagerInterface->getConnection()->prepare($sql1);
    //     $statement1 = $entityManagerInterface->getConnection()->prepare($sql2);
    //    // dd($statement1,$filters);

    //     foreach($filterValues AS $key => $value) {
    //     $statement->bindValue($key, $value);
    //     $statement1->bindValue($key, $value);
    //     }

    //     foreach($filterValueshaving AS $key => $value) {
    //         $statement->bindValue($key, $value);
    //         $statement1->bindValue($key, $value);
    //         }
    //     // $statement->bindValue('searchTerm', '%' . $search . '%');
    //     $statement->bindValue('limit', $length, \PDO::PARAM_INT);
    //     $statement->bindValue('offset', $start, \PDO::PARAM_INT);
    //     $statement->bindValue('account_id',$id);
    //     $statement1->bindValue('account_id',$id);

    //     $results = $statement->executeQuery()->fetchAllAssociative();
    //     $results1 = $statement1->executeQuery()->rowCount();
    //            // $data1 = $this->PredefindTextsRepository->findDataBySearch($search);
    //            // dd($results);

    //             return new JsonResponse([
    //                 'draw' => $draw,
    //                 'recordsTotal' => $results3,
    //                 'recordsFiltered' => $results1,
    //                 'data' => $results,
    //             ]);
    // }
}
