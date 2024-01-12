<?php

namespace App\Controller;

use App\Entity\Supportickets;
use App\Repository\PlansRepository;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sinergi\BrowserDetector\Os;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class DetailsController extends AbstractController
{
  
    /**
    * @var PlansRepository
    */
    private $PlansRepository;
    protected $parameterBag;
    public function __construct(ParameterBagInterface $parameterBag,PlansRepository $PlansRepository)
    {
        $this->parameterBag = $parameterBag;
        $this->PlansRepository = $PlansRepository;
    }

 
    #[Route('/getagentsDetails', name: 'app_getagentsDetails')]
    public function getagentsDetails(Request $request , UserRepository $userRepository)
    {

        function addTrailingSlashIfMissing($str)
        {
            if (!in_array(substr($str, -1), ['/', '\\'])) {
                $str .= '/';
            }
            return $str;
        }

        $uploads_directory = addTrailingSlashIfMissing($this->parameterBag->get('APP_URL'))."uploads/";
   

        $account_id = $request->attributes->get('account');
        
        $users = $userRepository->loaduserByAccount($account_id);

       // $salesArray = $plans_account->toArray();

       $resultArray = [];
       foreach ($users as $user) {

        $UserRights = $user->userRights->toArray();
        $userRightsArray = [];
        if (count($UserRights) > 0) {
            foreach ($UserRights as $userRight) {
                if($userRight->status=='1'){
                    $userRightsArray[] = [
                        'id' => $userRight->id,
                        'contact_gender' => $userRight->contact_gender,
                        'contact_firstname' => $userRight->contact_firstname,
                        'contact_lastname' => $userRight->contact_lastname,
                        'contact_name' => $userRight->contact_name,
                        'contact_phone' => $userRight->contact_phone,
                        'contact_country' => $userRight->contact_country,
                        'contact_address' => $userRight->contact_address,
                        'contact_ipaddress' => $userRight->contact_ipaddress,
                        'contact_request_category' => $userRight->contact_request_category,
                        'contact_request' => $userRight->contact_request,
                        'contact_origin' => $userRight->contact_origin,
                        'contact_date_of_birth' => $userRight->contact_date_of_birth,
                        'contact_company_name' => $userRight->contact_company_name,
                        'contact_custom_fields' => $userRight->contact_custom_fields,
                        'status' => $userRight->status,
                        'date_start' => $userRight->date_start,
                        'date_end' => $userRight->date_end,
                    ];
                }
             
            }
        }
        $userPermissions = $user->userPermissions->toArray();
        $userPermissionsArray = [];
        if (count($userPermissions) > 0) {
            foreach ($userPermissions as $userPermission) {
                if($userPermission->status=='1'){
                $userPermissionsArray[] = [
                    'id' => $userPermission->id,
                    'pre_defined_messages' => $userPermission->pre_defined_messages,
                    'planning_management' => $userPermission->planning_management,
                    'package_creation' => $userPermission->package_creation,
                    'package_visibility' => $userPermission->package_visibility,
                    'business_tools' => $userPermission->business_tools,
                    'communications' => $userPermission->communications,
                    'visitors_rating' => $userPermission->visitors_rating,
                    'status' => $userPermission->status,
                    'date_start' => $userPermission->date_start,
                    'date_end' => $userPermission->date_end,
                ];
                }
            }
        }

    
        $userPresentations = $user->userPresentations->toArray();
    
        $userPresentationsArray = [];
        if (count($userPresentations) > 0) {
            foreach ($userPresentations as $userPresentation) {
                if($userPresentation->status=='1'){
                $avatar="";
                if($userPresentation->picture!=null)
                if(!empty($userPresentation->picture))
                $avatar=$uploads_directory.$userPresentation->picture;
                
                $userPresentationsArray[] = [
                    'id' => $userPresentation->id,
                    'gender' => $userPresentation->gender,
                    'nickname' => $userPresentation->nickname,
                    'role' => $userPresentation->role,
                    'skills' => $userPresentation->skills,
                    'presentation' => $userPresentation->presentation,
                    'picture' => $userPresentation->picture,
                    'brand_name' => $userPresentation->brand_name,
                    'website' => $userPresentation->website,
                    'country' => $userPresentation->country,
                    'languages' => $userPresentation->languages,
                    'contact_phone' => $userPresentation->contact_phone,
                    'contact_phone_comment' => $userPresentation->contact_phone_comment,
                    'contact_mail' => $userPresentation->contact_mail,
                    'atrological_sign' => $userPresentation->atrological_sign,
                    'expertise' => $userPresentation->expertise,
                    'diploma' => $userPresentation->diploma,
                    'status' => $userPresentation->status,
                    'date_start' => $userPresentation->date_start,
                    'avatar' => $avatar,
                    'date_end' => $userPresentation->date_end,
                ];
            }   
          }
        }
        $userNotifications = $user->userNotifications->toArray();
        $userNotificationsArray = [];
        if (count($userNotifications) > 0) {
            foreach ($userNotifications as $userNotification) {
             
                $userNotificationsArray[] = [
                    'id' => $userNotification->id,
                    'visitor_register' => $userNotification->visitor_register,
                    'visitor_login' => $userNotification->visitor_login,
                    'plan_actions' => $userNotification->plan_actions,
                    'contact_form_actions' => $userNotification->contact_form_actions,
                    'predefined_text_actions' => $userNotification->predefined_text_actions,
                    'links_actions' => $userNotification->links_actions,
                    'user_actions' => $userNotification->user_actions,
                    'landing_page_actions' => $userNotification->landing_page_actions,
                    'contact_actions' => $userNotification->contact_actions,
                    'email_notifications' => $userNotification->email_notifications,
                    'sales' => $userNotification->sales,
                ];
            }   
        
        }

        $resultArray[] = [
            'id' => $user->getId(),
            'email' => $user->email,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'login' => $user->login,
            'notification_mail' => $user->notification_mail,
            'notification_audio' => $user->notification_audio,
            'notification_browser' => $user->notification_browser,
            'timezone' => $user->getTimezone(),
            'shortcut' => $user->isShortcut(),
            'status' => $user->status,
            'date_start' => $user->date_start,
            'date_end' => $user->date_end,
            'gender' => $user->gender,
            'login' => $user->login,
            'userRights' => $userRightsArray, 
            'userPresentations' => $userPresentationsArray,
            'userPermissions' => $userPermissionsArray,
            'userNotifications' => $userNotificationsArray,

        ];
       }


       return new JsonResponse($resultArray);

   


    }


    #[Route('/getplans', name: 'app_getplans')]
    public function getplans(Request $request)
    {
        $account_id = $request->attributes->get('account');
        
        $plansWithSales = $this->PlansRepository->loadplansByAccount($account_id);

       // $salesArray = $plans_account->toArray();

       $resultArray = [];
       foreach ($plansWithSales as $plan) {
        if($plan->isStatus()=='1'){

            
        $planUsers = $plan->getPlanUsers()->toArray();
    
        $usersArray = [   'account_id' => $account_id];
        if (count($planUsers) > 0) {
            foreach ($planUsers as $user) {
                if($user->status=='1'){
                    $usersArray[] = [
                        'id' => $user->getId(),
                        'status' => $user->status,
                        'date_start' => $user->getDateStart(),
                        'date_end' => $user->getDateEnd(),
                        'user' => $user->getUser(),
                    ];
              }
            }
        }

        $planDiscounts = $plan->getPlanDiscounts()->toArray();
        $discountsArray = [];
        if (count($planDiscounts) > 0) {
         
            foreach ($planDiscounts as $discount) {
                if($discount->status=='1'){
                        $planDiscountUsers = $discount->getPlanDiscountUsers()->toArray();
                        $planDiscountUsersArray = [];
                        if (count($planDiscountUsers) > 0) {
                            foreach ($planDiscountUsers as $user) {
                                if($user->status=='1'){
                                    $planDiscountUsersArray[] = [
                                        'id' => $user->getId(),
                                        'status' => $user->status,
                                        'date_start' => $user->getDateStart(),
                                        'date_end' => $user->getDateEnd(),
                                        'user' => $user->getUser(),
                                    ];
                                }
                            }
                        }  
                    
                        $discountsArray[] = [
                            'id' => $discount->getId(),
                            'name' => $discount->getName(),
                            'discount_type' => $discount->isDiscountType(),
                            'discount_value' => $discount->isDiscountValue(),
                            'status' => $discount->status,
                            'date_start' => $discount->getDateStart(),
                            'date_end' => $discount->getDateEnd(),
                            'planDiscountUsers' =>$planDiscountUsersArray,
                        ];
                } 
            }
        }
    
        $resultArray[] = [
            'id' => $plan->getId(),
            'name' => $plan->getName(),
            'currency' => $plan->getCurrency(),
            'tariff' => $plan->getTariff(),
            'billing_type' => $plan->isBillingType(),
            'billing_volume' => $plan->getBillingVolume(),
            'status' => $plan->isStatus(),
            'date_start' => $plan->getDateStart(),
            'date_end' => $plan->getDateEnd(),
            'language' => $plan->getLanguage(),
            'planUsers' => $usersArray, 
            'planDiscounts' => $discountsArray,
         

        

        ];

        }

       }


       return new JsonResponse($resultArray);

   


    }

    #[Route('/newticket', name: 'newticket')]
    public function newticket(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
      
        $data = json_decode($request->getContent(), true);


    $profile_id = $data['profile_id'];
    $subsql="";
    if(isset($profile_id) && $profile_id!=null ){
        $subsql=" and p.id =".$profile_id;
    }else{
        $subsql=" and c.email like '".$data['mail']."'";
    }
    $account_id = $request->attributes->get('account');
        
    $sql = "SELECT p.id , c.source_type 
    FROM `contacts` AS c
    LEFT JOIN `profiles` AS p ON p.u_id = c.id and p.u_type =2
    WHERE c.account_id = :accountId ".$subsql;

    $statement = $entityManagerInterface->getConnection()->prepare($sql);
    $statement->bindValue('accountId', $account_id);
    $contact = $statement->executeQuery()->fetchAssociative();
    $id = null;
    $type = null;
 
    if($contact ){
        $id = $contact['id'];
        $type = $contact['source_type'];
  
    }

        $supporticket = new Supportickets();
        $supporticket->first_name = $data['firstname'];
        $supporticket->last_name = $data['lastname'];
        $supporticket->email = $data['mail'];
        $supporticket->subject = $data['object'];
        if($supporticket->subject ==3)
        $supporticket->details = $data['details'];
        $supporticket->created_at = new \DateTimeImmutable();

        $supporticket->ip_address = $data['ip_address'];
        $supporticket->source = $data['source'];
        $supporticket->profile_type = $type;
        $supporticket->profile_id = $id;
        $supporticket->status = 1;
        $supporticket->customer_account_id =$account_id ;

   
        $userAgent = $request->headers->get('User-Agent');
        
        // Use a library like BrowserDetect to parse the user agent string
        $browser = new \Sinergi\BrowserDetector\Browser($userAgent);
        $os = new Os();
       
        
        
        //dd($os->getName());
        $browserName = $browser->getName();
        $supporticket->browser = $browserName . ';' . $os->getName();


        $entityManagerInterface->persist($supporticket);
        $entityManagerInterface->flush();

    

     


        $client = HttpClient::create();
        $data = [
            "event" => "new_ticket",
          "ticket"=>$supporticket,
          "account_id"=>$account_id,
          "user"=>["id"=>$id , "type"=>$type ,"source"=>$data['source'] , "ip"=> $data['ip_address']] ,
        ];



        function addTrailingSlashIfMissing1($str)
        {
            if (!in_array(substr($str, -1), ['/', '\\'])) {
                $str .= '/';
            }
            return $str;
        }


        $ws_library = addTrailingSlashIfMissing1($this->parameterBag->get('ws_library'));
        $url = $ws_library . 'users/new_ticket';



        $response = $client->request('POST', $url, [
            'json' => $data,
        ]);

        $content = null;


        $status = $response->getStatusCode();


        //SupporticketsRepository

        return new JsonResponse([
            'success' => true,
            "ticket"=>$supporticket,
            "status_ws"=>$status,
        ]);
    }
    
}
