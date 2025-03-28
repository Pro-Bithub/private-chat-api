<?php

namespace App\Controller;

use App\Entity\Contacts;
use App\Entity\Profiles;
use App\Entity\UserLogs;
use App\Repository\ContactsRepository;
use App\Repository\ProfilesRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sinergi\BrowserDetector\Browser;
use Sinergi\BrowserDetector\Os;
use Sinergi\BrowserDetector\UserAgent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ContactGuestController extends AbstractController
{
    private $requestStack;

public function __construct(RequestStack $requestStack)
{
    $this->requestStack = $requestStack;
}

    #[Route('/AddGuestContact', name: 'app_add_guest_contact')]
    public function addContactGuest(Request $request, ProfilesRepository $ProfilesRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManagerInterface,ContactsRepository $contactsRepository,RequestStack $requestStack): Response
    {
       
        $dateTime = new \DateTime('@'.strtotime('now')); // Create a DateTime object representing the current date and time
        $time =  new \DateTimeImmutable();
        $timestamp = $dateTime->getTimestamp(); // Get the timestamp
 
        $data = json_decode($request->getContent(), true);


        if (isset($data['source_type']) && isset($data['source_id']) ) {
          $contacts=  $contactsRepository->loadContactBsourceAndsourceType($data['source_id'],$data['source_type']);

          if (count($contacts) > 0) {
       
            $profiles = $ProfilesRepository->findOneByContact($contacts[0]->id );
            return new JsonResponse([
                'success' => true,
                'data' => $profiles,
                'firstname' => $contacts[0]->firstname??$contacts[0]->firstname ,
                'lastname' => $contacts[0]->lastname??$contacts[0]->lastname,
                'account_id' => $contacts[0]->accountId,
         
                'existed'=>true
                 ]);
          }else{

            $contact = new Contacts();
            $contact->email = $data['email'] ?? null;

            $contact->firstname = $data['firstname'] ?? null;
            $contact->lastname = $data['lastname'] ?? null;
            $contact->accountId =  $data['accountId'] ?? $request->attributes->get('account');

            $contact->source_type = $data['source_type'] ?? null;
            $contact->source_id = $data['source_id'] ?? null;
            $contact->source = $data['source'] ?? null;

         
            $contact->origin = $request->get('origin');
            $contact->name = '';
            $contact->status = '1';
         
            $contact->date_start = new \DateTime('@'.strtotime('now'));
           // $contact->ip_address =  $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
            $contact->ip_address =  $data['ipAddress'] ?? '';
            $contact->country =  $data['country'] ?? '';
            $contact->phone =  $data['phone'] ?? '';

            if(isset($data['date_birth']) ){
                $dateOfBirth =  \DateTimeImmutable::createFromFormat('Y-m-d',  date('Y-m-d', strtotime($data['date_birth'])));
                if ($dateOfBirth) {
                   $contact->date_birth=   $dateOfBirth;
                }
            }

            


            if(isset($data['gender']) ){
             if(preg_match('/^[mM1]/', $data['gender'])){
                $contact->gender = 'H';
             }else{
                $contact->gender = 'F';
             }
            }

            $contact->phone =  $data['phone'] ?? '';
            

    
            $entityManagerInterface->persist($contact);
            $entityManagerInterface->flush();

            $UserLogs = new UserLogs();
            $UserLogs->action = 'New Guest Contact from GOCC';
            $UserLogs->element = '27';
            $UserLogs->element_id = $contact->id;
            $UserLogs->log_date = $time;
            $UserLogs->source = '3';
            $entityManagerInterface->persist($UserLogs);
            $entityManagerInterface->flush();


            $profiles = new Profiles();
            $profiles->ip_address =  $contact->ip_address;
            $profiles->accountId =  $data['accountId'] ?? $request->attributes->get('account');
            $profiles->username = '';
           // $login = $contact->id.$timestamp; 
          //  $profiles->login =  $login;

          

        
           if ( isset($contact->email)   ) {
            $profiles->login =  $contact->email;
            }else{
                $login = $contact->id.$timestamp; 
            $profiles->login =  $login;
            }

           $password = bin2hex(random_bytes(8)); 
           $profiles->password = $userPasswordHasher->hashPassword($profiles, $password);

            $profiles->u_type = '2';
            $profiles->u_id = $contact->id;

            if(isset($data['browser'])){
                $userAgentString = $data['browser'];
                $userAgent = new UserAgent($userAgentString);
                $browser = new Browser($userAgent);
                $os = new Os($userAgent);
                $browserName = $browser->getName();
                $osName = $os->getName();
                $profiles->browser_data = $browserName . ';' . $osName;
            }
            $entityManagerInterface->persist($profiles);
            $entityManagerInterface->flush();
            $UserLogs = new UserLogs();
            $UserLogs->action = 'New Guest Profile';
            $UserLogs->element = '30';
            $UserLogs->element_id = $profiles->id;
            $UserLogs->log_date = $time;
            $UserLogs->source = '3';
            $entityManagerInterface->persist($UserLogs);
            $entityManagerInterface->flush();
            

          }

          return new JsonResponse([
            'success' => true,
            'data' => $profiles,
            'firstname' => $contact->firstname??$contact->firstname ,
            'lastname' => $contact->lastname??$contact->lastname,
            'account_id' => $contact->accountId,
            'existed'=> false,
            
             ]);

        }


       
        $contact = new Contacts();
        $contact->accountId = $request->attributes->get('account');
        $contact->origin = $request->get('origin');
        $contact->name = '';
        $contact->status = '1';
        $contact->email = '';
        $contact->date_start = new \DateTime('@'.strtotime('now'));
       // $contact->ip_address =  $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
        $contact->ip_address =  $data['ipAddress'] ?? '';
        $contact->country =  $data['country'] ?? '';

        $entityManagerInterface->persist($contact);
        $entityManagerInterface->flush();

           
       // $login = $contact->id.$timestamp; // Generate a random email-like login

        $UserLogs = new UserLogs();
        //$UserLogs->user_id = $contact->id;
        $UserLogs->action = 'New Guest Contact';
        $UserLogs->element = '27';
        $UserLogs->element_id = $contact->id;
        $UserLogs->log_date = $time;
        $UserLogs->source = '3';
        $entityManagerInterface->persist($UserLogs);
        $entityManagerInterface->flush();

        $profiles = new Profiles();
        $profiles->ip_address =  $contact->ip_address;
        $profiles->accountId = $request->attributes->get('account');
        $profiles->username = '';
        if ( isset($contact->email)   ) {
            $profiles->login =  $contact->email;
            }else{
                $login = $contact->id.$timestamp; 
            $profiles->login =  $login;
            }
       // $encodedPassword = $this->passwordEncoder->encodePassword(null, $password);
       $password = bin2hex(random_bytes(8)); // Generate a random 16-character password
        $profiles->password = $userPasswordHasher->hashPassword($profiles,$password);
        $profiles->u_type = '2';
        $profiles->u_id = $contact->id;
  
        $request = $this->requestStack->getCurrentRequest();
        if(isset($data['browser'])){
            $userAgentString = $data['browser'];
            $userAgent = new UserAgent($userAgentString);
            $browser = new Browser($userAgent);
            $os = new Os($userAgent);
            $browserName = $browser->getName();
            $osName = $os->getName();
            $profiles->browser_data = $browserName . ';' . $osName;
        }
     
        //$profiles->browser_data = $_SERVER['HTTP_USER_AGENT'];
       //$profiles->browser_data = get_browser(null, true);
        $entityManagerInterface->persist($profiles);
        $entityManagerInterface->flush();

        $UserLogs = new UserLogs();
        //$UserLogs->user_id = $contact->id;
        $UserLogs->action = 'New Guest Profile';
        $UserLogs->element = '30';
        $UserLogs->element_id = $profiles->id;
        $UserLogs->log_date = $time;
        $UserLogs->source = '3';
        $entityManagerInterface->persist($UserLogs);
        $entityManagerInterface->flush();

        return new JsonResponse([
            'success' => 'true',
            'data' => $profiles,
            'account_id' => $contact->accountId,
          
        ]);

    }

   
    #[Route('/getcontact/{id}')]
    public function getcontact(Request $request, EntityManagerInterface $entityManagerInterface, $id)
    {
 
        $sql = "SELECT c.*
        FROM `contacts` AS c
        LEFT JOIN `profiles` AS p ON p.u_id = c.id and p.u_type=2
        WHERE p.id  = :id and c.status = 1 and  p.account_id = :account ";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql);
        $statement3->bindValue('id', $id);
        $statement3->bindValue('account', $request->attributes->get('account'));
        $results = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => 'success',
            'data' => $results
        ]);
    }


    #[Route('/update/contact/{id}', name: 'app_update_contact_for_ws_controller')]
    public function updateContact($id,Request $request,EntityManagerInterface $entityManagerInterface,ContactsRepository $contactsRepository,ProfilesRepository $profilesRepository): Response {
      
        $data = json_decode($request->getContent(), true);

        $profile = $profilesRepository->findContactProfileByIdProfile($id, $request->attributes->get('account'));
        if($profile == null){
            return new JsonResponse([
                'success' => false,
                'message' => 'contact not exist.'
            ]);
        }
        $contact = $contactsRepository->find($profile->u_id);
        if($contact == null){
            return new JsonResponse([
                'success' => false,
                'message' => 'contact not exist.'
            ]);
        }
    
        $contact->gender = $data['gender'] ?? $contact->gender;
        $contact->firstname = !empty($data['firstname']) ? $data['firstname'] : $contact->firstname;
        $contact->lastname = !empty($data['lastname']) ? $data['lastname'] : $contact->lastname;
        $contact->country = !empty($data['country']) ? $data['country'] : $contact->country;
        $contact->name = !empty($data['name']) ? $data['name'] : $contact->name;
        $contact->email = !empty($data['email']) ? $data['email'] : $contact->email;
        $contact->phone =  !empty($data['phone']) ? $data['phone'] : $contact->phone;
        if (isset($data['date_birth'])) {
            $dateOfBirth = \DateTimeImmutable::createFromFormat('Y-m-d', date('Y-m-d', strtotime($data['date_birth'])));
            if ($dateOfBirth) {
                $contact->date_birth = $dateOfBirth;
            }
        }
        
        $contact->address = $data['address'] ?? $contact->address;
        $contact->origin = $data['origin'] ?? $contact->origin;
        $contact->status =!empty($data['status']) ? $data['status'] : $contact->status;
        $contact->company = !empty($data['company']) ? $data['company'] : $contact->company;
        $contact->source_type =!empty($data['source_type']) ? $data['source_type'] : $contact->source_type;
        $contact->source = !empty($data['source']) ? $data['source'] : $contact->source;
        $contact->source_id = !empty($data['source_id']) ? $data['source_id'] : $contact->source_id;
        
        $entityManagerInterface->persist($contact);
        $entityManagerInterface->flush();
        

        

        $logs = new UserLogs();
     
        $logs->element = 20;
        $logs->action = 'update';
        $logs->element_id = $contact->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        $time =  new \DateTimeImmutable();
        $profile = $profilesRepository->findContactProfileById($contact->id);
        $profile->username = $contact->name;
        if ( isset($contact->email)   ) {
            $profile->login =  $contact->email;
        }
        $entityManagerInterface->persist($profile);
        $entityManagerInterface->flush();

        $UserLogs = new UserLogs();
     
        $UserLogs->action = 'Update Profile';
        $UserLogs->element = '30';
        $UserLogs->element_id = $profile->id;
        $UserLogs->log_date = $time;
        $UserLogs->source = '1';
        $entityManagerInterface->persist($UserLogs);
        $entityManagerInterface->flush();
    
        return new JsonResponse([
            'success' => true,
            'data' => $contact,
        ]);


    }



    #[Route('/new_password', name: 'new_password')]
    public function newPassword(Request $request,ProfilesRepository $ProfilesRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManagerInterface):Response
    {
        // dd($request->all());
        $data = json_decode($request->getContent(), true);
      
            $id_profile = $data['id'];
           
            $newpassword =  $data['new_password'] ;
            $account =  $data['account_id'] ;
        // $pwd= Hash::check('password', $request->get('password'));
        // dd(array('login' => $login));
         $profiles = $ProfilesRepository->findContactProfileByIdProfile($id_profile,$account);
   
       if($profiles == null){
        return new JsonResponse([
            'success' => false,
            'message' => 'contact not exist.'
        ]);
       }
       else{
            //dd($this->container->get('request_stack')->getCurrentRequest()->getClientIp());
            $profiles->ip_address =  $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
            $request1 = Request::createFromGlobals();
        $userAgent = $request1->headers->get('User-Agent');
        
        // Use a library like BrowserDetect to parse the user agent string
        $browser = new \Sinergi\BrowserDetector\Browser($userAgent);
        $os = new Os();
       
        
        
        //dd($os->getName());
        $browserName = $browser->getName();
        $profiles->browser_data = $browserName . ';' . $os->getName();
        $profiles->password = $userPasswordHasher->hashPassword($profiles,$newpassword);

            $entityManagerInterface->persist($profiles);
            $entityManagerInterface->flush();

            $UserLogs = new UserLogs();
            $UserLogs->user_id = $profiles->u_id;
            $UserLogs->action = 'changed password contact';
            $UserLogs->element = '26';
            $UserLogs->element_id = $profiles->u_id;
          
            $UserLogs->log_date = new \DateTimeImmutable();
            $UserLogs->source = '3';

            $entityManagerInterface->persist($UserLogs);
            $entityManagerInterface->flush();

            return new JsonResponse([
                'success' => true
            ]);
        }
        

       
    
    }


}
