<?php
// api/src/EventListener/DeserializeListener.php

namespace App\EventListener;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthenticationFailureListener
{

 /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        $data = [
            'status'  => 'error',
            'code' => Response::HTTP_UNAUTHORIZED,
            'message' => 'Please Verify Your Password',
        ];

        $exception = $event->getException();
        if ($exception instanceof AuthenticationException) {
            $message = $exception->getMessage();
           
            if ($message === 'Bad credentials.') {
                $data['message'] = 'Please Verify Your Email Address';
            } elseif ($message === 'User not found.') {
                $data['message'] = 'Please Verify Your Email Address.';
            }
        }
        $response = new JsonResponse($data, Response::HTTP_UNAUTHORIZED, ['WWW-Authenticate' => 'Bearer']);

        $event->setResponse($response);

        
    
    // $event->setResponse($response);
    return new JsonResponse([
        'success' => 'false',
        'data' => $data
    ]);


    // $data = [
    //     'status' => 'error',
    // ];

    // $exception = $event->getException();
    // if ($exception instanceof AuthenticationException) {
    //     $message = $exception->getMessage();
    //     if ($message === 'Bad credentials.') {
    //         $data['message'] = 'Invalid email or password.';
    //     } elseif ($message === 'User not found.') {
    //         $data['message'] = 'Email not found.';
    //     }
    // }

    // $response = new JsonResponse($data);
    // $event->setResponse($response);
}
}
