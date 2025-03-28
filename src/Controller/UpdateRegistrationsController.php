<?php

namespace App\Controller;

use App\Entity\Registrations;
use App\Entity\UserLogs;
use App\Repository\AccountsRepository;
use App\Repository\RegistrationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UpdateRegistrationsController extends AbstractController
{
    protected $parameterBag;
    /**
     * @var RegistrationsRepository
     */
    private $RegistrationsRepository;
    public function __construct(ParameterBagInterface $parameterBag, RegistrationsRepository $RegistrationsRepository)
    {
        $this->parameterBag = $parameterBag;
        $this->RegistrationsRepository = $RegistrationsRepository;
    }
    #[Route('/update/registrations')]
    /**
     * Undocumented function
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateregistrations(Request $request,  EntityManagerInterface $entityManagerInterface, AccountsRepository $accountsRepository)
    {
        function addTrailingSlashIfMissing($str)
        {
            if (!in_array(substr($str, -1), ['/', '\\'])) {
                $str .= '/';
            }
            return $str;
        }

        $authorizationHeader = $request->headers->get('Authorization');

        // Check if the token is present and in the expected format (Bearer TOKEN)
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            throw new AccessDeniedException('Invalid or missing authorization token.');
        }

        $APP_PUBLIC_DIR = addTrailingSlashIfMissing($this->parameterBag->get('APP_PUBLIC_DIR'));
        $APP_URL = addTrailingSlashIfMissing($this->parameterBag->get('APP_URL'));

        //$data = json_decode($request->getContent(), true);
        $Registrations1 = $this->RegistrationsRepository->findOneById($request->get('idregister'));
        $Registrations1->status = 0;
        $Registrations1->date_end = new \DateTimeImmutable();

        $logs = new UserLogs();
        $logs->user_id = $request->get('user_id');
        $logs->element = 26;
        $logs->action = 'update';
        $logs->element_id = $Registrations1->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();
        //$account = $accountsRepository->find($request->get('account_id'));
        //dd($account);
        $Registrations = new Registrations();
        $Registrations->name = $request->get('name');
        $Registrations->accountId = $request->get('account_id');
        $Registrations->slug_url = $request->get('slug_url');
        $Registrations->redirect_url = $request->get('redirect_url');
        $Registrations->comment = $request->get('comment');
        $Registrations->template = $request->get('template');
        $Registrations->status = $request->get('status');
        $Registrations->url = $request->get('url');
        $Registrations->lang = $request->get('lang');
        $Registrations->date_start = new \DateTimeImmutable();

        $lang = ($Registrations->lang == 1) ? 'en' : 'fr';


        $filesystem = new Filesystem();


           //for page contact
           $formstemplateContact = 'forms/template-contact';
           $newBaseContactHref = $APP_URL . $formstemplateContact . '/';
           $fileContact = new SplFileInfo($APP_PUBLIC_DIR . $formstemplateContact . '/index.html', '', '');
           $fileContentsContact = $fileContact->getContents();
           $fileContentsContact = str_replace('[base-href]',  $newBaseContactHref, $fileContentsContact);
           $fileContentsContact = str_replace('[lang]',  $lang, $fileContentsContact);
           $filesystem->dumpFile($request->get('slug_url') . '/contact/index.html',  $fileContentsContact);
   
           $fileContacterror = new SplFileInfo($APP_PUBLIC_DIR . $formstemplateContact . '/error.html', '', '');
           $fileContentsContacterror = $fileContacterror->getContents();
           $fileContentsContacterror = str_replace('[base-href]',  $newBaseContactHref, $fileContentsContacterror);
           $fileContentsContacterror = str_replace('[lang]',  $lang, $fileContentsContacterror);
           $filesystem->dumpFile($request->get('slug_url') . '/contact/error.html',  $fileContentsContacterror);
   
           $fileContactsuccess = new SplFileInfo($APP_PUBLIC_DIR . $formstemplateContact . '/success.html', '', '');
           $fileContentsContactsuccess = $fileContactsuccess->getContents();
           $fileContentsContactsuccess = str_replace('[base-href]',  $newBaseContactHref, $fileContentsContactsuccess);
           $fileContentsContactsuccess = str_replace('[lang]',  $lang, $fileContentsContactsuccess);
           $filesystem->dumpFile($request->get('slug_url') . '/contact/success.html',  $fileContentsContactsuccess);
   
           $fileContactrequest = new SplFileInfo($APP_PUBLIC_DIR . $formstemplateContact . '/request.php', '', '');
           $fileContentsContactrequest = $fileContactrequest->getContents();
           $fileContentsContactrequest = str_replace('[base-href]',  $newBaseContactHref, $fileContentsContactrequest);
           $fileContentsContactrequest = str_replace('[lang]',  $lang, $fileContentsContactrequest);

           $filesystem->dumpFile($request->get('slug_url') . '/contact/request.php',  $fileContentsContactrequest);
  

          //


        $formstemplate = 'forms/template-' . $request->get('template');
        $newBaseHref = $APP_URL . $formstemplate . '/';

   



        $file = new SplFileInfo($APP_PUBLIC_DIR . $formstemplate . '/index.html', '', '');
        $fileContents = $file->getContents();
        $fileContents = str_replace('[base-href]',  $newBaseHref, $fileContents);
        $fileContents = str_replace('[api-url]',  $APP_URL, $fileContents);
        $fileContents = str_replace('[lang]',  $lang, $fileContents);


        $file_forget_password = new SplFileInfo($APP_PUBLIC_DIR . $formstemplate . '/forget_password.html', '', '');
        $fileContentsfgp = $file_forget_password->getContents();
        $fileContentsfgp = str_replace('[base-href]',  $newBaseHref, $fileContentsfgp);
        $fileContentsfgp = str_replace('[api-url]',  $APP_URL, $fileContentsfgp);
        $fileContentsfgp =  str_replace('[lang]', $lang, $fileContentsfgp);



        $filesystem->dumpFile($request->get('slug_url') . '/index.html',  $fileContents);
        $filesystem->dumpFile($request->get('slug_url') . '/forget_password.html',  $fileContentsfgp);
    
        
        $file_reset_password = new SplFileInfo($APP_PUBLIC_DIR . $formstemplate . '/reset_password.html', '', '');
        $fileContentsrestpwd = $file_reset_password->getContents();
        $fileContentsrestpwd = str_replace('[base-href]',  $newBaseHref, $fileContentsrestpwd);
        $fileContentsrestpwd = str_replace('[api-url]',  $APP_URL, $fileContentsrestpwd);
        $fileContentsrestpwd = str_replace('[lang]',$lang, $fileContentsrestpwd);

        $filesystem->dumpFile($request->get('slug_url') . '/reset_password.html',  $fileContentsrestpwd);




        $json = json_encode(array('data' => $Registrations, 'api_url'=>$APP_URL));
        $filesystem->dumpFile($request->get('slug_url') . '/data.json', $json);

        $filesystem->chmod($request->get('slug_url') , 0755);
        $filesystem->chmod($request->get('slug_url').'/contact' , 0755);
        $filesystem->chmod($request->get('slug_url').'/contact/request.php' , 0644);


        $entityManagerInterface->persist($Registrations);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $request->get('user_id');
        $logs->element = 26;
        $logs->action = 'create';
        $logs->element_id = $Registrations->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();
        return new JsonResponse([
            'success' => 'true',
            'data' => $Registrations
        ]);
    }

    #[Route('/delete/registrations')]
    /**
     * Undocumented function
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteregistrations(Request $request,  EntityManagerInterface $entityManagerInterface)
    {


        $authorizationHeader = $request->headers->get('Authorization');

        // Check if the token is present and in the expected format (Bearer TOKEN)
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            throw new AccessDeniedException('Invalid or missing authorization token.');
        }

        $data = json_decode($request->getContent(), true);
        $Registrations = $this->RegistrationsRepository->findOneById($request->get('idregister'));
        $Registrations->status = $request->get('status');


        $entityManagerInterface->persist($Registrations);
        $entityManagerInterface->flush();
        return new JsonResponse([
            'success' => 'true',
            'data' => $Registrations
        ]);
    }
}
