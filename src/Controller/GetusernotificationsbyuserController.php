<?php

namespace App\Controller;

use App\Repository\UserNotificationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetusernotificationsbyuserController extends AbstractController
{
   /**
    * @var UserNotificationsRepository
    */
    private $UserNotificationsRepository;
    public function __construct(UserNotificationsRepository $UserNotificationsRepository)
    {
        $this->UserNotificationsRepository = $UserNotificationsRepository;
    }

    public function __invoke(Request $request, $id)
    {

        $notification_user_id = $this->UserNotificationsRepository->loadusernotificationsbyUserId($id);
        //dd($user);
        return $notification_user_id;
    }
}
