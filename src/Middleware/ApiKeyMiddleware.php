<?php

namespace App\Middleware;

use App\Controller\AddcontactformsController;
use App\Controller\AddlogsController;
use App\Controller\ContactGuestController;
use App\Controller\getPlansController;
use App\Controller\GetTypeFormController;
use App\Controller\MessageController;
use App\Controller\SalesController;
use App\Controller\UpdateBalanceController;
use App\Controller\GetUserAccountController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class PlanUserMiddleware
{
    public $entityManagerInterface;
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
         $this->entityManagerInterface = $entityManagerInterface;
    }
    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();
        //   $entityManagerInterface =  new EntityManagerInterface;
        if (!is_array($controller)) {
            return;
        }

        $object = $controller[0];

        if ($object instanceof getPlansController || $object instanceof AddlogsController || $object instanceof MessageController || $object instanceof AddcontactformsController || $object instanceof SalesController || $object instanceof GetTypeFormController || $object instanceof ContactGuestController || $object instanceof UpdateBalanceController || $object instanceof GetUserAccountController) {
            $request = $event->getRequest();
            $key = $request->headers->get('key');


            $RAW_QUERY1 = 'SELECT * FROM accounts WHERE api_key = :key';
            $stmt1 = $this->entityManagerInterface->getConnection()->prepare($RAW_QUERY1);
            $stmt1->bindValue('key', $key);
            $result = $stmt1->executeQuery()->fetchAllAssociative();

            if (count($result) === 0) {
                // $response = new Response(json_encode(['success' => false, 'message' => 'Invalid key']), Response::HTTP_UNAUTHORIZED);
                // $event->setController(static function () use ($response) {
                //     return $response;
                // });
                $RAW_QUERY1 = 'SELECT * FROM profiles WHERE user_key = :key';
                $stmt1 = $this->entityManagerInterface->getConnection()->prepare($RAW_QUERY1);
                $stmt1->bindValue('key', $key);
                $result = $stmt1->executeQuery()->fetchAllAssociative();
    
                if (count($result) === 0) {
                    $response = new Response(json_encode(['success' => false, 'message' => 'Invalid key' , 'key' => $key]), Response::HTTP_UNAUTHORIZED);
                    $event->setController(static function () use ($response) {
                        return $response;
                    });
                } else {
                    $request->attributes->set('admin', $result[0]['u_type'] == '3');
                    $request->attributes->set('agent_id', $result[0]['id']);
                    $request->attributes->set('account', $result[0]['account_id']);
                }
            } else {
                $request->attributes->set('account', $result[0]['id']);
            }
        }

        
    }
}
