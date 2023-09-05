<?php

namespace App\Controller;

use App\Repository\ContactFormsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetcontactformsbyaccountController extends AbstractController
{
    /**
    * @var ContactFormsRepository
    */
    private $ContactFormsRepository;
    public function __construct(ContactFormsRepository $ContactFormsRepository)
    {
        $this->ContactFormsRepository = $ContactFormsRepository;
    }

    public function __invoke(Request $request, $id, $formtype, $status)
    {
        $contact_forms_account =$this->ContactFormsRepository->findBy(array('account' => $id,'form_type' => explode(",", $formtype), 'status' => $status));

       // $contact_forms_account = $this->ContactFormsRepository->loadfromsByAccount($id, explode(",", $formtype), $status);
        //dd($user);
        return $contact_forms_account;
    }
    
}
