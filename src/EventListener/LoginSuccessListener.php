<?php

namespace App\EventListener;

use App\Entity\Profiles;
use App\Entity\User;
use App\Entity\UserLogs;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class LoginSuccessListener
{
    public $entityManagerInterface;
public function __construct(EntityManagerInterface $entityManagerInterface)
{
    $this->entityManagerInterface = $entityManagerInterface;
}
    public function onLoginSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();
        //dd($user->username);
        $payload = $event->getData();
        
        if (!$user instanceof Profiles) {
            return;
        }
       // $username = $user['username'];
        //dd($username);
        // Add information to user payload
        $payload['user'] = [
            'account_id' => $user->getAccountId(),
            'email' => $user->getLogin(),
            'user_id' => $user->getId(),
            'username' => $user->getUsername(),
            'type' => $user->isUType(),
            'u_id' => $user->getUId()


        ];

        $logs = new UserLogs();
        $logs->user_id = $user->getId();
        $logs->element = 25;
        $logs->action = 'login';
        $logs->element_id = $user->getId();
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
   
        $this->entityManagerInterface->persist($logs);
        $this->entityManagerInterface->flush();
        //dd($payload);
        $event->setData($payload);
    }
}