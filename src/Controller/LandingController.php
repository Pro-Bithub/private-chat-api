<?php

namespace App\Controller;

use App\Repository\LandingPagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LandingController extends AbstractController
{
   /**
    * @var LandingPagesRepository
    */
    private $LandingPagesRepository;
    public function __construct(LandingPagesRepository $LandingPagesRepository)
    {
        $this->LandingPagesRepository = $LandingPagesRepository;
    }
    
    public function __invoke()
    {

        $LandingPages = $this->LandingPagesRepository->loadLandingPages();
        //dd($user);
        return $LandingPages;
    }
}
