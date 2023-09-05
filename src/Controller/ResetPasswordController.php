<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
class ResetPasswordController extends AbstractController
{
    /**
    * @var UserRepository
    */
    private $userRepository;
    public function __construct(private MailerInterface $mailer , UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    #[Route('/email')]
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
       $user = $this->userRepository->loadUserByEmail($data['email']);
       if($user != null){
        $email = (new Email())
            ->from('hello@example.com')
            ->to($data['email'])
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

                <p>Otherwise, please click this link to change your password: <a href="https://iheb.local.itwise.pro/private-chat-app/public/reset_password/'.$user->id.'">[link]</a></p>             
            ');

     $this->mailer->send($email);
   
    //dd($user->id);
     //return $user;
        return new JsonResponse([
            'success' => 'true',
            'data' => $user
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
}
