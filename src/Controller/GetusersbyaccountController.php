<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetusersbyaccountController extends AbstractController
{
   /**
    * @var UserRepository
    */
    private $UserRepository;
    public function __construct(UserRepository $UserRepository)
    {
        $this->UserRepository = $UserRepository;
    }

    public function __invoke(Request $request, $id)
    {

        $user_account = $this->UserRepository->loaduserByAccount($id);
        //dd($user);
        return $user_account;
    }
    
}
