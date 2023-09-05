<?php

namespace App\Controller;

use App\Repository\PredefindTextsRepository;
use App\Repository\PredefinedTextUsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetpredefinedtextbyuserController extends AbstractController
{
   /**
    * @var PredefinedTextUsersRepository
    */
    private $PredefinedTextUsersRepository;
    public function __construct(PredefinedTextUsersRepository $PredefinedTextUsersRepository)
    {
        $this->PredefinedTextUsersRepository = $PredefinedTextUsersRepository;
    }

    public function __invoke(Request $request, $id)
    {

        $predefined_text_user = $this->PredefinedTextUsersRepository->loadpredefinedtextByUser($id);
        //dd($user);
        return $predefined_text_user;
    }
}
