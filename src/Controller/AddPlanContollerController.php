<?php

namespace App\Controller;

use App\Entity\PlanDiscounts;
use App\Entity\PlanDiscountUsers;
use App\Entity\Plans;
use App\Entity\PlanUsers;
use App\Entity\UserLogs;
use App\Repository\AccountsRepository;
use App\Repository\PlanDiscountsRepository;
use App\Repository\PlanDiscountUsersRepository;
use App\Repository\PlansRepository;
use App\Repository\PlanUsersRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AddPlanContollerController extends AbstractController
{
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
        $plans->language = $data['language'];
        $plans->currency = $data['currency'];
        $plans->tariff = $data['tariff'];
        $plans->billing_type = $data['billingType'];
        $plans->billing_volume = $data['billingVolume'];
        $planUserData = $data['planUser'];
        //$planDiscountUserData = $data['plandiscountUser'];

        $entityManagerInterface->persist($plans);
        $entityManagerInterface->flush();

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

        return new JsonResponse([
            'success' => true,
            'data' => $plans,
        ]);
    }

    #[Route('/update_plan/{id}', name: 'app_update_plan_controller')]
    public function updatePlan(
        $id,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        AccountsRepository $accountsRepository,
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
    
        // Now you can access the user data from the token (assuming your User class has a `getUsername()` method)
        // $user = $tokenData->getUser();
        $plans = $plansRepository->find($id);

        $data = json_decode($request->getContent(), true);
        //$date = DateTime::createFromFormat('Y-m-d', $data['dateStart']);
        // $datediscount = DateTime::createFromFormat('Y-m-d', $data['statusplandiscount']);
        // dd($data);
        if ($data['discountdateStart'] != null) {
            // $datetime1 = new DateTime($data['discountdateStart']);

            // Format the DateTime object to the desired date format
            // $date1 = $datetime1->format('Y-m-d');
            // //$date2 = DateTime::createFromFormat('Y-m-d', $date);

            // //dd($date);
            // $dateStartString1 = $date1;
            // $datediscount = DateTime::createFromFormat('Y-m-d', $dateStartString1);
        }

        // $datetime = new DateTime($data['dateStart']);

        // // Format the DateTime object to the desired date format
        // $date = $datetime->format('Y-m-d');
        // //$date2 = DateTime::createFromFormat('Y-m-d', $date);

        // //dd($date);
        // $dateStartString = $date;

        // // Convert the string input to a DateTime object
        // $dateStart = DateTime::createFromFormat('Y-m-d', $dateStartString);

        $plans->date_start = new DateTime($data['dateStart']);

        $plans->name = $data['name'];
        //$plans->date_start = $date;
        if ($data['dateEnd'] != null) {
            $date_end = new DateTime($data['dateEnd']);
            $plans->date_end = $date_end;
        } else {
            $plans->date_end = $data['dateEnd'];
        }
        $plans->status = $data['status'];
        $plans->language = $data['language'];
        $plans->currency = $data['currency'];
        $plans->tariff = $data['tariff'];
        $plans->billing_type = $data['billingType'];
        $plans->billing_volume = $data['billingVolume'];
        //$planUserData = $data['planUser'];

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

        $sql4 = "SELECT * FROM `plan_discounts` as d WHERE d.plan_id = :id and d.status = 1";
        $statement4 = $entityManagerInterface->getConnection()->prepare($sql4);
        $statement4->bindValue('id', $plans->id);
        $discount = $statement4->executeQuery()->fetchAllAssociative();
        if ($data['discountname'] != null || $data['discounttype'] != null || $data['discountvalue'] != null || $data['plandiscountUser'] != null || $data['discountdateStart'] != null) {
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


                $sql5 = "SELECT pdu.user_id FROM `plan_discount_users` as pdu WHERE pdu.discount_id = :id and pdu.status = 1";
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
                $sqlUpdate = "UPDATE `plan_discounts` SET `status` = 0 WHERE `plan_id` = :id ";
                $statementUpdate = $entityManagerInterface->getConnection()->prepare($sqlUpdate);
                $statementUpdate->bindValue('id', $plans->id);
                $statementUpdate->execute();
                
            }
        }



        // $planDiscountUserData1 = isset($data['plandiscountUser']) ? $data['plandiscountUser'] : [];

        $planUserData = isset($data['planUser']) ? $data['planUser'] : [];



        $sql3 = "SELECT p.user_id FROM `plan_users` as p WHERE p.plan_id = :id and p.status = 1";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $plans->id);

        $PlanUserIds = $statement3->executeQuery()->fetchAllAssociative();
        // $sql3="SELECT t.user_id FROM `predefined_text_users` as t WHERE t.text_id = :id and t.status = 1";
        //     $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        //     $statement3->bindValue('id', $predefindText->id);
        //     $predefinedTextUserIds = $statement3->executeQuery()->fetchAllAssociative();
        $result = array_column($PlanUserIds, 'user_id');

        $difference2 = array_diff($result, $planUserData);

        if (!empty($difference2)) {
            foreach ($difference2 as $difference) {
                if (in_array($difference, $result)) {
                    $plan_user_data = $planUsersRepository->loadPlanByUserData($plans->id, $difference);

                    if (!empty($plan_user_data)) {
                        $plan_user = $plan_user_data[0];
                        $plan_user->status = '0';
                        $plan_user->date_end = new \DateTimeImmutable();

                        $entityManagerInterface->persist($plan_user);
                        $entityManagerInterface->flush();
                    }
                } else {
                    $planUser = new PlanUsers();
                    $user = $userRepository->find($difference);
                    $planUser->plan = $plans;
                    $planUser->user = $user;
                    $planUser->status = '1';
                    $planUser->date_start = new \DateTimeImmutable();

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
        } else {
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

        return new JsonResponse([
            'success' => true,
            'data' => $plans,
        ]);
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



        return new JsonResponse([
            'success' => true,
            'data' => $plans,
        ]);
    }
}
