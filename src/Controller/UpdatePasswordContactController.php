<?php

namespace App\Controller;

use App\Entity\Profiles;
use App\Entity\UserLogs;
use App\Repository\ContactsRepository;
use App\Repository\ProfilesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    /**
    * @var ContactsRepository
    * @var profilesRepository
    */
    private $ContactsRepository;
    private $profilesRepository;
    public function __construct(private MailerInterface $mailer , ContactsRepository $ContactsRepository, ProfilesRepository $profilesRepository)
    {
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
       // dd($request->get('email'));
       $data = json_decode($request->getContent(), true);
       $name = $request->get('name');
       $template = $request->get('template');
       //dd($data);
       $contact = $this->ContactsRepository->loadContactByEmail($request->get('login'));
       if($contact != null){
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

                <p>Otherwise, please click this link to change your password: <a href="https://iheb.local.itwise.pro/private-chat-app/public/'.$name.'/reset_password'. '/'.$contact->id.'.html'.'">[link]</a></p>             
            ');

     $this->mailer->send($email);
   
    //dd($user->id);
     //return $user;
     $filesystem = new Filesystem();
        
    
     $file_reset_password1 = new SplFileInfo('/home/ihebitwi/public_html/private-chat-app/public/forms/template-1/reset_password.html', '', '');

    
     $file_reset_password2 = new SplFileInfo('/home/ihebitwi/public_html/private-chat-app/public/forms/template-2/reset_password.html', '', '');

    
     $file_reset_password3 = new SplFileInfo('/home/ihebitwi/public_html/private-chat-app/public/forms/template-3/reset_password.html', '', '');
     if($template == '1'){
       
         $filesystem->dumpFile($name.'/reset_password' .'/'. $contact->id . '.html', $file_reset_password1->getContents());
        
     }else if($template == '2'){
       
         $filesystem->dumpFile($name.'/reset_password' .'/'. $contact->id . '.html', $file_reset_password2->getContents());
       
     }else{
       
         $filesystem->dumpFile($name.'/reset_password' .'/'. $contact->id . '.html', $file_reset_password3->getContents());
         
     }
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
     if($Contact){
       // $profiles->password = $userPasswordHasher->hashPassword($profiles,$request->get('password'));
       
        $profiles = new Profiles();
        $profiles->accountId = $profile->accountId;
        $profiles->username = $profile->username;
        $profiles->password = $userPasswordHasher->hashPassword($profiles,$request->get('password'));
        $profiles->login = $profile->login;
        $profiles->u_id = $profile->u_id;
        $profiles->u_type = '1';
        $time =  new \DateTimeImmutable();
        
        $UserLogs = new UserLogs();
        $UserLogs->user_id = $Contact->id;
        $UserLogs->action = 'Update Password Profile';
        $UserLogs->element = '30';
        $UserLogs->element_id = $profiles->id;
        $UserLogs->log_date = $time;
        $UserLogs->source = '1';
        $entityManagerInterface->persist($UserLogs);
        $entityManagerInterface->flush();
        //$user->password = $userPasswordHasher->hashPassword($user,$request->get('password'));
        // $plainPassword = $request->get('password');
        // $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
        // $user->setPassword($hashedPassword);

       
        $entityManagerInterface->persist($profiles);
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
