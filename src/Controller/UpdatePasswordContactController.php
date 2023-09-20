<?php

namespace App\Controller;

use App\Entity\Profiles;
use App\Entity\UserLogs;
use App\Repository\ContactsRepository;
use App\Repository\ProfilesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
class UpdatePasswordContactController extends AbstractController
{
    protected $parameterBag;

    /**
    * @var ContactsRepository
    * @var profilesRepository
    */
    private $ContactsRepository;
    private $profilesRepository;
    public function __construct(ParameterBagInterface $parameterBag,private MailerInterface $mailer , ContactsRepository $ContactsRepository, ProfilesRepository $profilesRepository)
    {
        $this->parameterBag = $parameterBag;
        $this->ContactsRepository = $ContactsRepository;
        $this->profilesRepository = $profilesRepository;
    }
    #[Route('/contact/email')]
    /**
     * Undocumented function
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendEmail(Request $request) 
    {
        function addTrailingSlashIfMissing($str) {
            if (!in_array(substr($str, -1), ['/', '\\'])) {
                $str .= '/';
            }
            return $str;
        }

       

       // dd($request->get('email'));
       $data = json_decode($request->getContent(), true);
       $name = $request->get('name');
       $template = $request->get('template');
       //dd($data);
       $contact = $this->ContactsRepository->loadContactByEmail($request->get('login'));
       if($contact != null){
        $APP_PUBLIC_DIR = addTrailingSlashIfMissing($this->parameterBag->get('APP_PUBLIC_DIR'));
        $APP_URL = addTrailingSlashIfMissing($this->parameterBag->get('APP_URL'));

        $email = (new Email())
            ->from('hello@example.com')
            ->to($request->get('login'))
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Forgot password?')
            ->text('Sending emails is fun again!')
            ->html('
                <p>Hi,</p>
                <p>There was a request to change your password!</p>

                <p>If you did not make this request then please ignore this email.</p>

                <p>Otherwise, please click this link to change your password: <a href="'.$APP_URL.$name.'/reset_password'. '/'.$contact->id.'.html'.'">[link]</a></p>             
            ');

     $this->mailer->send($email);
   
    //dd($user->id);
     //return $user;

    

     $formstemplate='forms/template-'.$template;
     $newBaseHref = $APP_URL.$formstemplate.'/'; 

     $filesystem = new Filesystem();
        
     $file = new SplFileInfo($APP_PUBLIC_DIR.$formstemplate.'/reset_password.html', '', '');
     $fileContents = $file->getContents();
     $fileContents = str_replace('[base-href]',  $newBaseHref , $fileContents);

     $filesystem->dumpFile($name.'/reset_password' .'/'. $contact->id . '.html',  $fileContents );



        return new JsonResponse([
            'success' => 'true',
            'data' => $contact
        ]);
    }
    else{
        return new JsonResponse([
            'success' => 'false',
            'data' => null
        ]);
        //return null;
    }

        // ...
    }
    #[Route('/contact/reset_password')]
    public function ResetPassword(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManagerInterface): Response 
    {
        //$data = json_decode($request->getContent(), true);
        $Contact = $this->ContactsRepository->findOneById($request->get('idContact'));
        $profile = $this->profilesRepository->findOneByContact($request->get('idContact'));
        //dd($profiles);
     //   $user = new User();
     if($Contact &&  $profile){
       // $profiles->password = $userPasswordHasher->hashPassword($profiles,$request->get('password'));
       
       /*  $profiles = new Profiles();
        $profiles->accountId = $profile->accountId;
        $profiles->username = $profile->username; */
        $profile->password = $userPasswordHasher->hashPassword($profile,$request->get('password'));
       /*  $profiles->login = $profile->login;
        $profiles->u_id = $profile->u_id;
        $profiles->u_type = '2'; */
        $time =  new \DateTimeImmutable();
        
        $UserLogs = new UserLogs();
        $UserLogs->user_id = $Contact->id;
        $UserLogs->action = 'Update Password Profile';
        $UserLogs->element = '30';
        $UserLogs->element_id = $profile->id;
        $UserLogs->log_date = $time;
        $UserLogs->source = '1';
        $entityManagerInterface->persist($UserLogs);
        $entityManagerInterface->flush();
        //$user->password = $userPasswordHasher->hashPassword($user,$request->get('password'));
        // $plainPassword = $request->get('password');
        // $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
        // $user->setPassword($hashedPassword);

       
        $entityManagerInterface->persist($profile);
        $entityManagerInterface->flush();
      
    
        $logs = new UserLogs();
        $logs->user_id = null;
        $logs->element = 20;
        $logs->action = 'update password';
        $logs->element_id = (int) $request->get('idContact');
        $logs->source = 3;
        $logs->log_date = new \DateTimeImmutable();
       
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();

        return new JsonResponse([
            'success' => 'true',
            'data' => $Contact
        ]);
    }
    else{
        return new JsonResponse([
            'success' => 'false',
            'data' => null
        ]);
        //return null;
    }
  

       



        



       
    }
}
