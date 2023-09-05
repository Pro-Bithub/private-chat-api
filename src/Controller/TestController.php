<?php

namespace App\Controller;

use App\Entity\Contacts;
use App\Entity\Profiles;
use App\Entity\UserLogs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sinergi\BrowserDetector\Browser;
use Sinergi\BrowserDetector\Os;
use Sinergi\BrowserDetector\UserAgent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestController extends AbstractController
{

    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    private $requestStack;

public function __construct(RequestStack $requestStack)
{
    $this->requestStack = $requestStack;
}

    //  #[Route('/browsertest', name: 'app_add_guest_contact')]
    //  public function getBrowserAndOS($id,Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManagerInterface,RequestStack $requestStack)
    //  {
    //      $request = $this->requestStack->getCurrentRequest();
    //      $userAgentString = $request->headers->get('User-Agent');
         
    //      $userAgent = new UserAgent($userAgentString);
    //      $browser = new Browser($userAgent);
    //      $os = new Os($userAgent);
         
    //      $browserName = $browser->getName();
    //      $osName = $os->getName();

    //      //dd($request->get('account'));
    //     $dateTime = new \DateTime('@'.strtotime('now')); // Create a DateTime object representing the current date and time
        
    //     $timestamp = $dateTime->getTimestamp(); // Get the timestamp
        
    //   //dd($timestamp);
    
    //     // Encode the password
    
    //     // return new Response(
    //     //     'Generated Login: ' . $login . '<br>' .
    //     //     'Generated Password: ' . $password . '<br>' 
    //     //     //'Encoded Password: ' . $encodedPassword
    //     // );
    //     $data = json_decode($request->getContent(), true);

    //     $contact = new Contacts();
    //     $contact->accountId = $id;
    //     $contact->origin = $request->get('origin');
    //     $contact->name = '';
    //     $contact->status = '1';
    //     $contact->email = '';
    //     $contact->date_start = new \DateTime('@'.strtotime('now'));
    //    // $contact->ip_address =  $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
    //     $contact->ip_address =  $data['ipAddress'];
    //     $contact->country =  $data['country'];

    //     $entityManagerInterface->persist($contact);
    //     $entityManagerInterface->flush();
    //     $time =  new \DateTimeImmutable();
           
    //     $login = $contact->id.$timestamp; // Generate a random email-like login
    //     $password = bin2hex(random_bytes(8)); // Generate a random 16-character password
    //     $UserLogs = new UserLogs();
    //     //$UserLogs->user_id = $contact->id;
    //     $UserLogs->action = 'New Guest Contact';
    //     $UserLogs->element = '27';
    //     $UserLogs->element_id = $contact->id;
    //     $UserLogs->log_date = $time;
    //     $UserLogs->source = '3';
    //     $entityManagerInterface->persist($UserLogs);
    //     $entityManagerInterface->flush();

    //     $profiles = new Profiles();
    //     $profiles->ip_address =  $contact->ip_address;
    //     $profiles->accountId = $id;
    //     $profiles->username = '';
    //     $profiles->login = $login;
    //    // $encodedPassword = $this->passwordEncoder->encodePassword(null, $password);
    //     $profiles->password = $userPasswordHasher->hashPassword($profiles,$password);
    //     $profiles->u_type = '2';
    //     $profiles->u_id = $contact->id;
    //    // $request1 = Request::createFromGlobals();
    //     //$userAgent = $request1->headers->get('User-Agent');
        
    //     // Use a library like BrowserDetect to parse the user agent string
    //     // $browser = new \Sinergi\BrowserDetector\Browser($userAgent);
    //     // $os = new Os();
    //     $request = $this->requestStack->getCurrentRequest();
    //     $userAgentString = $request->headers->get('User-Agent');
        
    //     // $userAgent = new UserAgent($userAgentString);
    //     // $browser = new Browser($userAgent);
    //     // $os = new Os($userAgent);
        
    //     // $browserName = $browser->getName();
    //     // $osName = $os->getName();
        
        
    //     // //dd($os->getName());
    //     // $browserName = $browser->getName();
    //     $profiles->browser_data = $browserName . ';' . $osName;
    //     //$profiles->browser_data = $_SERVER['HTTP_USER_AGENT'];
    //    //$profiles->browser_data = get_browser(null, true);
    //     $entityManagerInterface->persist($profiles);
    //     $entityManagerInterface->flush();

    //     $UserLogs = new UserLogs();
    //     //$UserLogs->user_id = $contact->id;
    //     $UserLogs->action = 'New Guest Profile';
    //     $UserLogs->element = '30';
    //     $UserLogs->element_id = $profiles->id;
    //     $UserLogs->log_date = $time;
    //     $UserLogs->source = '3';
    //     $entityManagerInterface->persist($UserLogs);
    //     $entityManagerInterface->flush();

    //     return new JsonResponse([
    //         'success' => 'true',
    //         'data' => $profiles,
    //         'Generated Login' => $login,
    //         'Generated Password' => $password
    //     ]);

    //      dd($browserName, $osName);
    //      return [
    //          'browser' => $browserName,
    //          'os' => $osName
    //      ];
    //  }
     
}
