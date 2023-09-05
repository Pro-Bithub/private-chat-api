<?php

namespace App\Controller;

use App\Entity\ClickableLinksUsers;
use App\Repository\ClickableLinksUsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetlinksbyuserController extends AbstractController
{
    
    /**
    * @var ClickableLinksUsersRepository
    */
    private $ClickableLinksUsersRepository;
    public function __construct(ClickableLinksUsersRepository $ClickableLinksUsersRepository)
    {
        $this->ClickableLinksUsersRepository = $ClickableLinksUsersRepository;
    }

    public function __invoke(Request $request, $id)
    {

        $user = $this->ClickableLinksUsersRepository->loadlinksByUser($id);
        //dd($user);
        return $user;
    }
    
    
}
