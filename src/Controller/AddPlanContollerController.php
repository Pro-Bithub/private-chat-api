<?php

namespace App\Controller;

use App\Entity\PlanDiscounts;
use App\Entity\PlanDiscountUsers;
use App\Entity\Plans;
use App\Entity\PlanTariffs;
use App\Entity\PlanUsers;
use App\Entity\UserLogs;
use App\Repository\AccountsRepository;
use App\Repository\PlanDiscountsRepository;
use App\Repository\PlanDiscountUsersRepository;
use App\Repository\PlansRepository;
use App\Repository\PlanTariffsRepository;
use App\Repository\PlanUsersRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpClient\HttpClient;

class AddPlanContollerController extends AbstractController
{
    protected $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }
    
    // #[Route('/add/plan', name: 'app_add_plan_contoller')]
    public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface, AccountsRepository $accountsRepository, UserRepository $userRepository): Response
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
        
         $user = $tokenData->getUser();
    
        // Now you can access the user data from the token (assuming your User class has a `getUsername()` method)
        // $user = $tokenData->getUser();
        $plans = new Plans();
        $data = json_decode($request->getContent(), true);
        //dd($data['planUser']);

        //dump($data);
        $account = $accountsRepository->find($data['account']);
        //$date = DateTime::createFromFormat('Y-m-d', $data['dateStart']);
        $datediscount = DateTime::createFromFormat('Y-m-d', $data['discountdateStart']);
        $date = new DateTime($data['dateStart']);
        // dd($date->format('Y-m-d'));
        $plans->account = $account;
        $plans->name = $data['name'];
        $plans->date_start = $date;
        if ($data['dateEnd'] != null) {
            // $date_end = DateTime::createFromFormat('Y-m-d', $data['dateEnd']);
            $date_end = new DateTime($data['dateEnd']);
            $plans->date_end = $date_end;
        } else {
            $plans->date_end = $data['dateEnd'];
        }
        $plans->status = "1";
    /*     $plans->language = $data['language'];
        $plans->currency = $data['currency'];
        $plans->tariff = $data['tariff']; */
        $plans->billing_type = $data['billingType'];
        if (isset($data['billingVolume'])) {
            $plans->billing_volume =$data['billingVolume'] ;
        }
 
        $planUserData = $data['planUser'];
        //$planDiscountUserData = $data['plandiscountUser'];
        if (isset($data['duration'])) {
            $plans->duration =$data['duration'] ;
        }
        if (isset($data['introductory'])) {
            if( $plans->introductory==true){
                $plans->introductory =1 ;
            }else if($plans->introductory==false){
                $plans->introductory =0 ;
            }
      
        }

        $plans->type =$data['type'] ;

        $entityManagerInterface->persist($plans);
        $entityManagerInterface->flush();

        
        $tariffs = $data['tariffs'];


        if (!empty($tariffs)) {
            $iterationCount = 0;

            foreach ($tariffs as $tariff) {
                $planTariffs = new PlanTariffs();
                $planTariffs->country = $tariff['country'];
                $planTariffs->currency = $tariff['currency'];
                $planTariffs->language = $tariff['language'];
                $planTariffs->date_start = new \DateTimeImmutable();
                $planTariffs->status = 1;

                if (isset($tariff['price'])) {
                    $planTariffs->price = $tariff['price']; 
                }
                if($data['type'] ==2){
                    if (isset($tariff['minute_cost'])){
                        $planTariffs->minute_cost = $tariff['minute_cost']; 
                    }
                    if (isset($tariff['details'])){
                        $planTariffs->details = $tariff['details']; 
                    }
                    if (isset($tariff['phone_number'])){
                        $planTariffs->phone_number = $tariff['phone_number']; 
                    }

                
                }
             
            
                $planTariffs->plan = $plans;
                $entityManagerInterface->persist($planTariffs);
                $entityManagerInterface->flush(); 

  

                if($data['type'] ==2){
                    $id = $planTariffs->id;
                   
      
                    foreach ($tariff['openingInfoArray'] as  $openingInfo) {
                        $sql = "INSERT INTO plan_info (value,element, plan_tariff_id) 
                        VALUES (:value, '2', :plan_tariff_id)";
                        $stmt4 = $entityManagerInterface->getConnection()->prepare($sql);
                        $stmt4->bindValue('value', $openingInfo);
                        $stmt4->bindValue('plan_tariff_id',$id);
                        $result = $stmt4->executeQuery();
                    }
                    foreach ($tariff['regulationArray'] as  $regulation) {
                        $sql = "INSERT INTO plan_info (value,element, plan_tariff_id) 
                        VALUES (:value, '1', :plan_tariff_id)";
                        $stmt4 = $entityManagerInterface->getConnection()->prepare($sql);
                        $stmt4->bindValue('value', $regulation);
                        $stmt4->bindValue('plan_tariff_id',$id);
                        $result = $stmt4->executeQuery();
                    }

                }


                $iterationCount++;
                if ($iterationCount >= 100) {
                    break;
                }
            }
            $entityManagerInterface->flush();
        }


        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 3;
        $logs->action = 'create';
        $logs->element_id = $plans->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        if ($planUserData != null) {
            foreach ($planUserData  as $i => $value) {
                $planUser = new PlanUsers();
                $user = $userRepository->find($value);
                $planUser->plan =  $plans;
                $planUser->user = $user;
                $planUser->status = '1';
                $planUser->date_start = $plans->date_start;

                $entityManagerInterface->persist($planUser);
                $entityManagerInterface->flush();

                $logs = new UserLogs();
                $logs->user_id = $data['user_id'];
                $logs->element = 4;
                $logs->action = 'create';
                $logs->element_id = $planUser->id;
                $logs->source = 1;
                $logs->log_date = new \DateTimeImmutable();

                $entityManagerInterface->persist($logs);
                $entityManagerInterface->flush();
            }
        }
        // plan: 'api/plans/' + res.id,
        // status: '1',
        // dateStart: this.form.value.discountdateStart,
        // dateEnd: this.form.value.discountdateEnd,
        // discountType: this.form.value.discounttype,
        // discountValue: this.form.value.discountvalue,
        // name: this.form.value.discountname,
        if ($data['discountname'] != null || $data['discounttype'] != null || $data['discountvalue'] != null || $data['plandiscountUser'] != null || $data['discountdateStart'] != null) {
            $planDiscount = new PlanDiscounts();
            $planDiscount->status = '1';
            // $planDiscount->date_start = $datediscount;
            $planDiscount->date_start = new DateTime($data['discountdateStart']);
            if ($data['discountdateEnd'] != null) {
                // $date_end_discount = DateTime::createFromFormat('Y-m-d', $data['discountdateEnd']);
                $date_end_discount = new DateTime($data['discountdateEnd']);
                $planDiscount->date_end = $date_end_discount;
            } else {
                $planDiscount->date_end = $data['discountdateEnd'];
            }
            //$planDiscount->date_end = $data['discountdateEnd'];
            $planDiscount->name = $data['discountname'];
            $planDiscount->discount_type = $data['discounttype'];
            $planDiscount->discount_value = $data['discountvalue'];
            $planDiscount->plan = $plans;
            $entityManagerInterface->persist($planDiscount);
            $entityManagerInterface->flush();


            $logs = new UserLogs();
            $logs->user_id = $data['user_id'];
            $logs->element = 5;
            $logs->action = 'create';
            $logs->element_id = $planDiscount->id;
            $logs->source = 1;
            $logs->log_date = new \DateTimeImmutable();
            $entityManagerInterface->persist($logs);
            $entityManagerInterface->flush();
        }
        $planDiscountUserData = $data['plandiscountUser'];
        if ($planDiscountUserData != null) {
            foreach ($planDiscountUserData  as $i => $value) {
                $PlanDiscountUsers = new PlanDiscountUsers();
                $user1 = $userRepository->find($value);

                $PlanDiscountUsers->discount =  $planDiscount;
                $PlanDiscountUsers->user = $user1;
                $PlanDiscountUsers->status = '1';
                $PlanDiscountUsers->date_start = $plans->date_start;

                $entityManagerInterface->persist($PlanDiscountUsers);
                $entityManagerInterface->flush();

                $logs = new UserLogs();
                $logs->user_id = $data['user_id'];
                $logs->element = 6;
                $logs->action = 'create';
                $logs->element_id = $PlanDiscountUsers->id;
                $logs->source = 1;
                $logs->log_date = new \DateTimeImmutable();

                $entityManagerInterface->persist($logs);
                $entityManagerInterface->flush();
            }
        }

          
        $content = $this->fetchDataFromWebService($this->parameterBag,'messages/changed/plans/'.$user->accountId);
     

        return new JsonResponse([
            'success' => true,
            'data' => $plans,
            'content' => $content,
        ]);
    }

    #[Route('/update_plan/{id}', name: 'app_update_plan_controller')]
    public function updatePlan(
        $id,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
    
        UserRepository $userRepository,
        PlansRepository $plansRepository,
        PlanUsersRepository $planUsersRepository,
        PlanDiscountsRepository $planDiscountsRepository,
     
        PlanDiscountUsersRepository $planDiscountUsersRepository
    ): Response {
    
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

        $user = $tokenData->getUser();

        $data = json_decode($request->getContent(), true);
    
         $plans = $plansRepository->find($id);

         $oldplans = new plans();
       
       
/*          $oldplans->planUsers = $plans->planUsers ; */
         $oldplans->planDiscounts = $plans->planDiscounts;
         $oldplans->sales = $plans->sales;
         $oldplans->name = $plans->name;
         $oldplans->billing_type = $plans->billing_type;
         $oldplans->billing_volume = $plans->billing_volume;
         $oldplans->planTariffs = $plans->planTariffs;
         $oldplans->date_end = new \DateTimeImmutable();
         $oldplans->date_start =$plans->date_start ;
         $oldplans->account =$plans->account ;
         $oldplans->planTariffs =$plans->planTariffs ;
         $oldplans->duration =$plans->duration ;
         $oldplans->type =$plans->type ;
         $oldplans->introductory =$plans->introductory ;
     
         $oldplans->status = '0';
         $entityManagerInterface->persist($oldplans);
         $entityManagerInterface->flush();

         $plan_user_data = $planUsersRepository->loaduserByPlansID($id);
         foreach ($plan_user_data as $plan_user) {
            $plan_user->plan=$oldplans;
            $entityManagerInterface->persist($plan_user);
            $entityManagerInterface->flush();

            
        }


        if ($data['discountdateStart'] != null) {
         
        }

 

        $plans->date_start = new DateTime($data['dateStart']);

        $plans->name = $data['name'];
    
        if ($data['dateEnd'] != null) {
            $date_end = new DateTime($data['dateEnd']);
            $plans->date_end = $date_end;
        } else {
            $plans->date_end = $data['dateEnd'];
        }
        $plans->status = $data['status'];
   
        $plans->billing_type = $data['billingType'];

        if (isset($data['billingVolume'])) 
        $plans->billing_volume = $data['billingVolume'];

        if (isset($data['duration'])) 
        $plans->duration = $data['duration'];

        $plans->type =$data['type'];

        if (array_key_exists('introductory', $data)) {
            $plans->introductory = $data['introductory'] ? '1' : '0';
        }
        

        $entityManagerInterface->persist($plans);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 3;
        $logs->action = 'update';
        $logs->element_id = $plans->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();
      
        $sql4 = "SELECT * FROM `plan_discounts` as d WHERE d.plan_id = :id ";
        $statement4 = $entityManagerInterface->getConnection()->prepare($sql4);
        $statement4->bindValue('id', $plans->id);
        $discount = $statement4->executeQuery()->fetchAllAssociative();
        

        $sqldelete = "UPDATE `plan_tariffs` SET `status` = '0' ,  `date_end` = CURDATE()  WHERE `plan_tariffs`.`plan_id` =  :id ";
        $statementdelete = $entityManagerInterface->getConnection()->prepare($sqldelete);
        $statementdelete->bindValue('id', $plans->id);
        $deletetariffs= $statementdelete->executeQuery();


        

        $tariffs = $data['tariffs'];

            if (!empty($tariffs)) {
                $iterationCount = 0;

                foreach ($tariffs as $tariff) {
            
                    $planTariffs = new PlanTariffs();
                    $planTariffs->country = $tariff['country'];
                    $planTariffs->currency = $tariff['currency'];
                    $planTariffs->language = $tariff['language'];
                   $planTariffs->date_start = new \DateTimeImmutable();
                    $planTariffs->status = 1;
      
                    $planTariffs->plan = $plans;

                    if (isset($tariff['price'])) {
                        $planTariffs->price = $tariff['price']; 
                    }
                    if($data['type'] ==2){
                        if (isset($tariff['minute_cost'])){
                            $planTariffs->minute_cost = $tariff['minute_cost']; 
                        }
                        if (isset($tariff['details'])){
                            $planTariffs->details = $tariff['details']; 
                        }
                        if (isset($tariff['phone_number'])){
                            $planTariffs->phone_number = $tariff['phone_number']; 
                        }
                    }
             
                    
                    $entityManagerInterface->persist($planTariffs);
                    $entityManagerInterface->flush(); 
                    $iterationCount++;
                    if ($iterationCount >= 100) {
                        break;
                    }

                    if($data['type'] ==2){
                        $id = $planTariffs->id;
                       
          
                        foreach ($tariff['openingInfoArray'] as  $openingInfo) {
                            $sql = "INSERT INTO plan_info (value,element, plan_tariff_id) 
                            VALUES (:value, '2', :plan_tariff_id)";
                            $stmt4 = $entityManagerInterface->getConnection()->prepare($sql);
                            $stmt4->bindValue('value', $openingInfo);
                            $stmt4->bindValue('plan_tariff_id',$id);
                            $result = $stmt4->executeQuery();
                        }
                        foreach ($tariff['regulationArray'] as  $regulation) {
                            $sql = "INSERT INTO plan_info (value,element, plan_tariff_id) 
                            VALUES (:value, '1', :plan_tariff_id)";
                            $stmt4 = $entityManagerInterface->getConnection()->prepare($sql);
                            $stmt4->bindValue('value', $regulation);
                            $stmt4->bindValue('plan_tariff_id',$id);
                            $result = $stmt4->executeQuery();
                        }
    
                    }
    
                    


                }
                $entityManagerInterface->flush();
            }


      
        if ($data['discountname'] != null || $data['discounttype'] != null || $data['discountvalue'] != null || ($data['plandiscountUser'] != null && (is_array($data['plandiscountUser']) && count($data['plandiscountUser']) > 0)) || $data['discountdateStart'] != null) {
          
       
            if (count($discount) == 0) {

                $planDiscount = new PlanDiscounts();
                $planDiscount->status = "1";
                $planDiscount->date_start = new DateTime($data['discountdateStart']);
                if ($data['discountdateEnd'] != null) {
                    $date_end_discount = DateTime::createFromFormat('Y-m-d', $data['discountdateEnd']);
                    $planDiscount->date_end = new DateTime($data['discountdateEnd']);
                } else {
                    $planDiscount->date_end = null;
                }
                //$planDiscount->date_end = $data['discountdateEnd'];
                $planDiscount->name = $data['discountname'];
                $planDiscount->discount_type = $data['discounttype'];
                $planDiscount->discount_value = $data['discountvalue'];
                $planDiscount->plan = $plans;
                $entityManagerInterface->persist($planDiscount);
                $entityManagerInterface->flush();


                $logs = new UserLogs();
                $logs->user_id = $data['user_id'];
                $logs->element = 5;
                $logs->action = 'update';
                $logs->element_id = $planDiscount->id;
                $logs->source = 1;
                $logs->log_date = new \DateTimeImmutable();
                $entityManagerInterface->persist($logs);
                $entityManagerInterface->flush();

                $planDiscountUserData = $data['plandiscountUser'];

                foreach ($planDiscountUserData  as $i => $value) {
                    $PlanDiscountUsers = new PlanDiscountUsers();
                    $user1 = $userRepository->find($value);

                    $PlanDiscountUsers->discount =  $planDiscount;
                    $PlanDiscountUsers->user = $user1;
                    $PlanDiscountUsers->status = '1';
                    $PlanDiscountUsers->date_start = $plans->date_start;

                    $entityManagerInterface->persist($PlanDiscountUsers);
                    $entityManagerInterface->flush();

                    $logs = new UserLogs();
                    $logs->user_id = $data['user_id'];
                    $logs->element = 6;
                    $logs->action = 'create';
                    $logs->element_id = $PlanDiscountUsers->id;
                    $logs->source = 1;
                    $logs->log_date = new \DateTimeImmutable();

                    $entityManagerInterface->persist($logs);
                    $entityManagerInterface->flush();
                }
            } else {
                $planDiscountUserData1 = isset($data['plandiscountUser']) ? $data['plandiscountUser'] : [];
                //dd($planDiscountUserData1);
                $planDiscount = $planDiscountsRepository->find($discount[0]['id']);
                if ($data['statusplandiscount'] == null) {
                    $planDiscount->status = $planDiscount->status;
                } else {
                    $planDiscount->status = $data['statusplandiscount'];
                }
                // $planDiscount->status = $data['statusplandiscount'];
                $planDiscount->date_start = new DateTime($data['discountdateStart']);
                if ($data['discountdateEnd'] != null) {
                    $date_end_discount = DateTime::createFromFormat('Y-m-d', $data['discountdateEnd']);
                    $planDiscount->date_end = new DateTime($data['discountdateEnd']);
                } else {
                    $planDiscount->date_end = null;
                }
                //$planDiscount->date_end = $data['discountdateEnd'];
                $planDiscount->name = $data['discountname'];
                $planDiscount->discount_type = $data['discounttype'];
                $planDiscount->discount_value = $data['discountvalue'];
                $planDiscount->plan = $plans;
                $entityManagerInterface->persist($planDiscount);
                $entityManagerInterface->flush();


                $logs = new UserLogs();
                $logs->user_id = $data['user_id'];
                $logs->element = 6;
                $logs->action = 'create';
                $logs->element_id = $planDiscount->id;
                $logs->source = 1;
                $logs->log_date = new \DateTimeImmutable();
                $entityManagerInterface->persist($logs);
                $entityManagerInterface->flush();


                $sql5 = "SELECT pdu.user_id FROM `plan_discount_users` as pdu WHERE pdu.discount_id = :id ";
                $statement6 = $entityManagerInterface->getConnection()->prepare($sql5);
                $statement6->bindValue('id', $discount[0]['id']);

                $PlandiscountUserIds = $statement6->executeQuery()->fetchAllAssociative();
                // $sql3="SELECT t.user_id FROM `predefined_text_users` as t WHERE t.text_id = :id and t.status = 1";
                //     $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
                //     $statement3->bindValue('id', $predefindText->id);
                //     $predefinedTextUserIds = $statement3->executeQuery()->fetchAllAssociative();
                $result3 = array_column($PlandiscountUserIds, 'user_id');

                $difference6 = array_diff($result3, $planDiscountUserData1);

                if (!empty($difference6)) {
                    foreach ($difference6 as $difference1) {
                        if (in_array($difference1, $result3)) {
                            $plan_discount_user_data = $planDiscountUsersRepository->loadPlanDiscountByUserData($discount[0]['id'], $difference1);

                            if (!empty($plan_discount_user_data)) {
                                $plan_user2 = $plan_discount_user_data[0];
                                $plan_user2->status = '0';
                                $plan_user2->date_end = new \DateTimeImmutable();

                                $entityManagerInterface->persist($plan_user2);
                                $entityManagerInterface->flush();
                            }
                        } else {
                            $planUser1 = new PlanDiscountUsers();
                            $user = $userRepository->find($difference1);
                            $planUser1->discount = $planDiscount;
                            $planUser1->user = $user;
                            $planUser1->status = '1';
                            $planUser1->date_start = new \DateTimeImmutable();

                            $entityManagerInterface->persist($planUser1);
                            $entityManagerInterface->flush();

                            $logs = new UserLogs();
                            $logs->user_id = $data['user_id'];
                            $logs->element = 6;
                            $logs->action = 'create';
                            $logs->element_id = $planUser1->id;
                            $logs->source = 1;
                            $logs->log_date = new \DateTimeImmutable();

                            $entityManagerInterface->persist($logs);
                            $entityManagerInterface->flush();
                        }
                    }
                } else {
                    foreach ($planDiscountUserData1 as $user_id) {
                        $planDiscountUserData1 = $planDiscountUsersRepository->loadPlanDiscountByUserData($discount[0]['id'], $user_id);

                        if (empty($planDiscountUserData1)) {
                            $planDiscountUser = new PlanDiscountUsers();
                            $user = $userRepository->find($user_id);
                            $planDiscountUser->discount = $planDiscount;
                            $planDiscountUser->user = $user;
                            $planDiscountUser->status = '1';
                            $planDiscountUser->date_start = new \DateTimeImmutable();

                            $entityManagerInterface->persist($planDiscountUser);
                            $entityManagerInterface->flush();

                            $logs = new UserLogs();
                            $logs->user_id = $data['user_id'];
                            $logs->element = 6;
                            $logs->action = 'create';
                            $logs->element_id = $planDiscountUser->id;
                            $logs->source = 1;
                            $logs->log_date = new \DateTimeImmutable();

                            $entityManagerInterface->persist($logs);
                            $entityManagerInterface->flush();
                        }
                    }
                }
            }
        }else{
          
            if (count($discount) != 0) {
              
                $logs = new UserLogs();
                $logs->user_id = $data['user_id'];
                $logs->element = 5;
                $logs->action = 'delete';
                $logs->element_id = $discount[0]['id'];
                $logs->source = 1;
                $logs->log_date = new \DateTimeImmutable();
                $entityManagerInterface->persist($logs);
                $entityManagerInterface->flush();


                $sqlDelete = "DELETE FROM `plan_discount_users` WHERE `discount_id` = :id ";
                $statementDelete = $entityManagerInterface->getConnection()->prepare($sqlDelete);
                $statementDelete->bindValue('id', $discount[0]['id']);
                $statementDelete->execute();

                $sqlDelete = "DELETE FROM `plan_discounts` WHERE `plan_id` = :id ";
                $statementDelete = $entityManagerInterface->getConnection()->prepare($sqlDelete);
                $statementDelete->bindValue('id', $plans->id);
                $statementDelete->execute();
         

             


            }
        }




        $planUserData = isset($data['planUser']) ? $data['planUser'] : [];
        if (!empty($planUserData)) 
        foreach ($planUserData as $user_id) {
            $plan_user_data = $planUsersRepository->loadPlanByUserData($plans->id, $user_id);

            if (empty($plan_user_data)) {
                $planUser = new PlanUsers();
                $user = $userRepository->find($user_id);
                $planUser->plan = $plans;
                $planUser->user = $user;
                $planUser->status = '1';
                $planUser->date_start = new \DateTimeImmutable();
                $entityManagerInterface->persist($planUser);
                $entityManagerInterface->flush();

                if( ! $this-> userExistsInPlan($user_id,  $plan_user_data)){

                    $logs = new UserLogs();
                    $logs->user_id = $data['user_id'];
                    $logs->element = 4;
                    $logs->action = 'create';
                    $logs->element_id = $planUser->id;
                    $logs->source = 1;
                    $logs->log_date = new \DateTimeImmutable();
                    $entityManagerInterface->persist($logs);
                    $entityManagerInterface->flush();
                }
             
           

            }
        }

        $plan_user_data = $planUsersRepository->loaduserByPlansID(    $oldplans->id);
     
        foreach ($plan_user_data as $plan_user) {
          
            if (!in_array($plan_user->user->id, $planUserData)) {
                $logs = new UserLogs();
                $logs->user_id = $data['user_id'];
                $logs->element = 4;
                $logs->action = 'delete';
                $logs->element_id = $plan_user->id;
                $logs->source = 1;
                $logs->log_date = new \DateTimeImmutable();
                $entityManagerInterface->persist($logs);
                $entityManagerInterface->flush();
            }
        }

    
      
       $content = $this->fetchDataFromWebService($this->parameterBag,'messages/changed/plans/'.$user->accountId);
     

  


        return new JsonResponse([
            'success' => true,
            'data' => $plans,
            'content' => $content
        ]);
    }

    function userExistsInPlan($userId,  $planUserData)
        {
            foreach ($planUserData as $user) {
                if ($user->id === $userId) {
                    return true;
                }
            }
            return false;
        }

     


    function fetchDataFromWebService($parameterBag,$urlparam)
    {
        function addTrailingSlashIfMissing($str)
        {
            if (!in_array(substr($str, -1), ['/', '\\'])) {
                $str .= '/';
            }
            return $str;
        }

        $content = null;


        try {
            $client = HttpClient::create();


            $ws_library = addTrailingSlashIfMissing($parameterBag->get('ws_library'));

            $url = $ws_library .$urlparam ;
            $response = $client->request('GET', $url);

            $status = $response->getStatusCode();
            if ($status < 400) {
                $content = $response->getContent();
            }
        } catch (\Throwable $e) {
            $content = $e->getMessage();
        }

        return $content;
    }

    #[Route('/delete_plan/{id}', name: 'app_delete_plan_controller')]
    public function deletePlan(
        $id,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        PlansRepository $plansRepository,

    ): Response {

         
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
        $user = $tokenData->getUser();
    
        // Now you can access the user data from the token (assuming your User class has a `getUsername()` method)
        // $user = $tokenData->getUser();
        $plans = $plansRepository->find($id);

        $data = json_decode($request->getContent(), true);

        $plans->status = "0";
        $plans->date_end = new \DateTimeImmutable();


        //$planUserData = $data['planUser'];

        $entityManagerInterface->persist($plans);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 3;
        $logs->action = 'delete';
        $logs->element_id = $plans->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        $content = $this->fetchDataFromWebService($this->parameterBag,'messages/changed/plans/'.$user->accountId);
    

        return new JsonResponse([
            'success' => true,
            'data' => $plans,
            'content' => $content,
        ]);
    }


    #[Route('/tariff/{id}', name: 'app_get_tariff_plan_controller')]
    public function tariffbyid(
        $id,
        Request $request,
        PlanTariffsRepository $planTariffsRepository,
        EntityManagerInterface $entityManagerInterface
    ): Response {

         
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
    
        $Tariffs = $planTariffsRepository->find($id);

	    $sql = "SELECT p.*
    FROM `plan_info` AS p
    WHERE p.plan_tariff_id = :id ";

        $statement = $entityManagerInterface->getConnection()->prepare($sql);
        $statement->bindValue('id', $id);
        $plan_infos = $statement->executeQuery()->fetchAllAssociative();


        return new JsonResponse([
            'success' => true,
            'data' => $Tariffs,
            'plan_infos' => $plan_infos,
            
        ]);
    }

    #[Route('/delete_tariff/{id}', name: 'app_delete_tariff_controller')]
    public function deleteTariff(
        $id,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        PlanTariffsRepository $planTariffsRepository,

    ): Response {

         
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
    
        $data = json_decode($request->getContent(), true);

        $Tariffs = $planTariffsRepository->find($id);
        $Tariffs->status = "0";
        $Tariffs->date_end = new \DateTimeImmutable();

        $entityManagerInterface->persist($Tariffs);
        $entityManagerInterface->flush();


        
        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 4;
        $logs->action = 'delete';
        $logs->element_id = $Tariffs->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        $user = $tokenData->getUser();
        $content = $this->fetchDataFromWebService($this->parameterBag,'messages/changed/plans/'.$user->accountId);
     

        return new JsonResponse([
            'success' => true,
            'data' => $Tariffs,
            'content' => $content,
        ]);
    }



    #[Route('/update_tariff', name: 'app_tariff_plan_controller')]
    public function update_tariff(
   
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        PlanTariffsRepository $planTariffsRepository,

    ): Response {

         
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
    
    
   

        $data = json_decode($request->getContent(), true);

        $Tariffs = $planTariffsRepository->find($data['id']);
        $Tariffs->status = "0";
        $Tariffs->date_end = new \DateTimeImmutable();

        $entityManagerInterface->persist($Tariffs);
        $entityManagerInterface->flush();



        $newTariffs =new PlanTariffs();
        $newTariffs->status = "1";
        $newTariffs->plan = $Tariffs->plan;
        if(isset($data['price']))
        $newTariffs->price = $data['price'];
        $newTariffs->country = $data['country'];
        $newTariffs->currency = $data['currency'];
        $newTariffs->language = $data['language'];

        if(isset($data['phone_number']))
        $newTariffs->phone_number = $data['phone_number'];

        if(isset($data['minute_cost']))
        $newTariffs->minute_cost = $data['minute_cost'];

        if(isset($data['details']))
        $newTariffs->details = $data['details'];

        

     
        $newTariffs->date_start = new \DateTimeImmutable();

        $entityManagerInterface->persist($newTariffs);
        $entityManagerInterface->flush();



        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 4;
        $logs->action = 'updated';
        $logs->element_id = $Tariffs->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();



        $id = $newTariffs->id;
                   
      
        foreach ($data['opening_info'] as  $openingInfo) {
            $sql = "INSERT INTO plan_info (value,element, plan_tariff_id) 
            VALUES (:value, '2', :plan_tariff_id)";
            $stmt4 = $entityManagerInterface->getConnection()->prepare($sql);
            $stmt4->bindValue('value', $openingInfo);
            $stmt4->bindValue('plan_tariff_id',$id);
            $result = $stmt4->executeQuery();
        }
        foreach ($data['regulation'] as  $regulation) {
            $sql = "INSERT INTO plan_info (value,element, plan_tariff_id) 
            VALUES (:value, '1', :plan_tariff_id)";
            $stmt4 = $entityManagerInterface->getConnection()->prepare($sql);
            $stmt4->bindValue('value', $regulation);
            $stmt4->bindValue('plan_tariff_id',$id);
            $result = $stmt4->executeQuery();
        }

        $user = $tokenData->getUser();
        $content = $this->fetchDataFromWebService($this->parameterBag,'messages/changed/plans/'.$user->accountId);

        return new JsonResponse([
            'success' => true,
            'data' => $Tariffs,
            'content' => $content,
        ]);
    }

}
