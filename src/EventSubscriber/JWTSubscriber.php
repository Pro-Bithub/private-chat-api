<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class JWTSubscriber implements EventSubscriberInterface
{
    public function onLexikJwtAuthenticationOnJwtCreated(JWTCreatedEvent $event)
    {
        $data = $event->getData();
        //dd($data);
        $data['username'] = $event->getUser()->getLogin();
        $data['id'] = $event->getUser()->getId();
        $data['exp'] = 162005723333;
        $event->setData($data);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onLexikJwtAuthenticationOnJwtCreated',
        ];
    }

    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event, JWTCreatedEvent $event1)
    {
        $data = $event1->getData();
   
        $response = new JWTAuthenticationFailureResponse('Bad credentials, please verify that your Email/password are correctly set', JsonResponse::HTTP_UNAUTHORIZED);
        $response->setData($data);

        $event->setResponse($response);
    }
}
