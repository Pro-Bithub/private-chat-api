<?php

namespace App\Controller;

use App\Entity\Contacts;
use App\Entity\Profiles;
use App\Entity\UserLogs;
use App\Repository\ContactsRepository;
use App\Repository\ProfilesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Namshi\JOSE\Signer\OpenSSL\RSA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sinergi\BrowserDetector\Os;
class CreatecontactaccountController extends AbstractController
{

    /**
    * @var ProfilesRepository
    */
    private $ProfilesRepository;
    public function __construct(ProfilesRepository $ProfilesRepository)
    {
        $this->ProfilesRepository = $ProfilesRepository;
    }

    #[Route('/createcontactaccount', name: 'app_createcontactaccount')]
    public function __invoke(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManagerInterface): Response
    {
        //dd($request->get('account'));
        $contact = new Contacts();
        $contact->accountId = $request->get('account');
        $contact->origin = $request->get('origin');
        $contact->name = $request->get('name');
        $contact->status = '1';
        $contact->email = $request->get('email');
        $contact->date_start = new \DateTime('@'.strtotime('now'));
        $contact->ip_address =  $this->container->get('request_stack')->getCurrentRequest()->getClientIp();

        $entityManagerInterface->persist($contact);
        $entityManagerInterface->flush();
        $time =  new \DateTimeImmutable();

      
        $UserLogs = new UserLogs();
        $UserLogs->user_id = $contact->id;
        $UserLogs->action = 'Register Contact';
        $UserLogs->element = '27';
        $UserLogs->element_id = $contact->id;
        $UserLogs->log_date = $time;
        $UserLogs->source = '3';
        $entityManagerInterface->persist($UserLogs);
        $entityManagerInterface->flush();

        $profiles = new Profiles();
        $profiles->ip_address =  $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
        $profiles->accountId = $request->get('account');
        $profiles->username = $request->get('name');
        $profiles->login = $request->get('email');
        $profiles->password = $userPasswordHasher->hashPassword($profiles,$request->get('password'));
        $profiles->u_type = '2';
        $profiles->u_id = $contact->id;
        $request1 = Request::createFromGlobals();
        $userAgent = $request1->headers->get('User-Agent');
        
        // Use a library like BrowserDetect to parse the user agent string
        $browser = new \Sinergi\BrowserDetector\Browser($userAgent);
        $os = new Os();
       
        
        
        //dd($os->getName());
        $browserName = $browser->getName();
        $profiles->browser_data = $browserName . ';' . $os->getName();
       // $profiles->browser_data = $_SERVER['HTTP_USER_AGENT'];
        $entityManagerInterface->persist($profiles);
        $entityManagerInterface->flush();

        $UserLogs = new UserLogs();
        $UserLogs->user_id = $contact->id;
        $UserLogs->action = 'new Profile';
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

    #[Route('/auth_profile', name: 'auth_profile')]
    public function CustomLogin(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManagerInterface):Response
    {
        // dd($request->all());
        
            $login = $request->get('login');
            $password = $request->get('password');
            $account = $request->get('account_id');
        // $pwd= Hash::check('password', $request->get('password'));
        // dd(array('login' => $login));
         $profiles = $this->ProfilesRepository->findContactProfileByemail($login,$account);
        // dd($account,$login);
        // $sql3= "SELECT * FROM `profiles` as p WHERE p.login = :login and p.u_type = 2 and p.account_id = :account_id";
        // $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        // $statement3->bindValue('login', $login);
        // $statement3->bindValue('account_id', $account);
        // $profiles = $statement3->executeQuery()->fetchAllAssociative();
        // dd($profiles);
        //$user = Profiles::where('login', $login)->first();
       // dd($userPasswordHasher->isPasswordValid($profiles, $password));
       if($profiles == null){
        return new JsonResponse([
            'success' => 'false',
            'message' => 'Email not exist.'
        ]);
       }
       else if($userPasswordHasher->isPasswordValid($profiles, $password) == false){
        return new JsonResponse([
            'success' => 'false',
            'message' => 'Password not valid'
        ]);
       }
        if ($profiles == null && $userPasswordHasher->isPasswordValid($profiles, $password) == false) {
            // error, you can't change your password 
            // throw exception or return, etc.
            return new JsonResponse([
                'success' => 'false',
                'message' => 'User not found.' 
            ]);
        }else{
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

            $entityManagerInterface->persist($profiles);
            $entityManagerInterface->flush();

            $UserLogs = new UserLogs();
            $UserLogs->user_id = $profiles->u_id;
            $UserLogs->action = 'Login Contact';
            $UserLogs->element = '25';
            $UserLogs->element_id = $profiles->u_id;
            $UserLogs->log_date = new \DateTimeImmutable();
            $UserLogs->source = '3';

            $entityManagerInterface->persist($UserLogs);
            $entityManagerInterface->flush();

            return new JsonResponse([
                'success' => 'true',
                'data' => $profiles
            ]);
        }
        

       
    
}

#[Route('/add_profile', name: 'add_profile')]
public function addprofile(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManagerInterface):Response
{
    $profiles = new Profiles();
    $profiles->ip_address =  $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
    $profiles->accountId = $request->get('accountId');
    $profiles->username = $request->get('username');
    $profiles->login = $request->get('login');
    $profiles->password = $userPasswordHasher->hashPassword($profiles,$request->get('password'));
    $profiles->u_type = '1';
    $profiles->u_id = $request->get('u_id');
    $request1 = Request::createFromGlobals();
    $userAgent = $request1->headers->get('User-Agent');
    
    // Use a library like BrowserDetect to parse the user agent string
    $browser = new \Sinergi\BrowserDetector\Browser($userAgent);
    $os = new Os();
   
    
    
    //dd($os->getName());
    $browserName = $browser->getName();
    $profiles->browser_data = $browserName . ';' . $os->getName();
   // $profiles->browser_data = $_SERVER['HTTP_USER_AGENT'];
    $entityManagerInterface->persist($profiles);
    $entityManagerInterface->flush();

    return new JsonResponse([
        'success' => 'true',
        'data' => $profiles
    ]);
}
#[Route('/delete_contact/{id}', name: 'app_delete_contact_controller')]
public function deletecontact(
    $id,
    ContactsRepository $contactsRepository,
    Request $request,
    EntityManagerInterface $entityManagerInterface,
): Response {
    $Contact = $contactsRepository->find($id);
    $data = json_decode($request->getContent(), true);
    $Contact->date_end = new \DateTimeImmutable();
    $Contact->status = '0';

   
    //dd($clickableLinksUser);
    $entityManagerInterface->persist($Contact);
    $entityManagerInterface->flush();

    $logs = new UserLogs();
    $logs->user_id = $data['user_id'];
    $logs->element = 20;
    $logs->action = 'delete';
    $logs->element_id = $Contact->id;
    $logs->source = 1;
    $logs->log_date = new \DateTimeImmutable();

    $entityManagerInterface->persist($logs);
    $entityManagerInterface->flush();

   

    return new JsonResponse([
        'success' => true,
        'data' => $Contact,
    ]);
}

#[Route('/check_email_contact', name: 'check_email_contact')]
public function checkemail(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManagerInterface):Response
{
    $login = $request->get('login');
    $account = $request->get('account');
    //$profiles = $this->ProfilesRepository->findContactProfileByemail(array('login' => $login));
    $sql = "SELECT p.login , c.status
    FROM `profiles` AS p
    LEFT JOIN `contacts` AS c ON c.id = p.u_id
    WHERE p.u_type = 2 AND c.status = 1 and p.login = :login and c.account_id = :account";
    
    $statement = $entityManagerInterface->getConnection()->prepare($sql);
    $statement->bindValue('login', $login);
    $statement->bindValue('account', $account);
    $profiles = $statement->executeQuery()->fetchAllAssociative();
    if(count($profiles) > 0){
        return new JsonResponse([
            'success' => 'false',
            'data' => $profiles
        ]);
    }else{
        return new JsonResponse([
        'success' => 'true',
        'data' => $profiles
    ]);
    }
    

    return new JsonResponse([
        'success' => 'true',
        'data' => $profiles
    ]);
}
}
