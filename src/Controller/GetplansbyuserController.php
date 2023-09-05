<?php

namespace App\Controller;

use App\Repository\PlansRepository;
use App\Repository\PlanUsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetplansbyuserController extends AbstractController
{
    /**
    * @var PlansRepository
    */
    private $PlansRepository;
    public function __construct(PlansRepository $PlansRepository)
    {
        $this->PlansRepository = $PlansRepository;
    }

    public function __invoke(Request $request, $id)
    {

        $plans_account = $this->PlansRepository->loadplansByAccount($id);
        //dd($);
        return $plans_account;
    }
}
