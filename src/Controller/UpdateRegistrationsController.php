<?php

namespace App\Controller;

use App\Entity\Registrations;
use App\Entity\UserLogs;
use App\Repository\AccountsRepository;
use App\Repository\RegistrationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UpdateRegistrationsController extends AbstractController
{
    /**
    * @var RegistrationsRepository
    */
    private $RegistrationsRepository;
    public function __construct(RegistrationsRepository $RegistrationsRepository)
    {
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
        
         
        $authorizationHeader = $request->headers->get('Authorization');

        // Check if the token is present and in the expected format (Bearer TOKEN)
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            throw new AccessDeniedException('Invalid or missing authorization token.');
        }
       
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
       $Registrations->comment = $request->get('comment');
       $Registrations->template = $request->get('template');
       $Registrations->status = $request->get('status');
       $Registrations->url = $request->get('url');
       $Registrations->date_start = new \DateTimeImmutable();
       $filesystem = new Filesystem();
       $file1 = new SplFileInfo('/home/ihebitwi/public_html/private-chat-app/public/forms/template-1/index.html', '', '');
       $file_forget_password1 = new SplFileInfo('/home/ihebitwi/public_html/private-chat-app/public/forms/template-1/forget_password.html', '', '');
      // $file_reset_password1 = new SplFileInfo('/home/ihebitwi/public_html/private-chat-app/public/forms/template-1/reset_password.html', '', '');

       $file2 = new SplFileInfo('/home/ihebitwi/public_html/private-chat-app/public/forms/template-2/index.html', '', '');
       $file_forget_password2 = new SplFileInfo('/home/ihebitwi/public_html/private-chat-app/public/forms/template-2/forget_password.html', '', '');
       //$file_reset_password2 = new SplFileInfo('/home/ihebitwi/public_html/private-chat-app/public/forms/template-2/reset_password.html', '', '');

       $file3 = new SplFileInfo('/home/ihebitwi/public_html/private-chat-app/public/forms/template-3/index.html', '', '');
       $file_forget_password3 = new SplFileInfo('/home/ihebitwi/public_html/private-chat-app/public/forms/template-3/forget_password.html', '', '');
       //$file_reset_password3 = new SplFileInfo('/home/ihebitwi/public_html/private-chat-app/public/forms/template-3/reset_password.html', '', '');
       if($request->get('template') == '1'){
           $filesystem->dumpFile($request->get('slug_url').'/index.html', $file1->getContents());
           $filesystem->dumpFile($request->get('slug_url').'/forget_password.html', $file_forget_password1->getContents());
           //$filesystem->dumpFile($request->get('slug_url').'/reset_password.html', $file_reset_password1->getContents());
           $json = json_encode(array('data' => $Registrations));
           $filesystem->dumpFile('forms/template-1/assets/js/'.$request->get('slug_url').'/data.json', $json);
       }else if($request->get('template') == '2'){
           $filesystem->dumpFile($request->get('slug_url').'/index.html', $file2->getContents());
           $filesystem->dumpFile($request->get('slug_url').'/forget_password.html', $file_forget_password2->getContents());
          // $filesystem->dumpFile($request->get('slug_url').'/reset_password.html', $file_reset_password2->getContents());
           $json = json_encode(array('data' => $Registrations));
           $filesystem->dumpFile('forms/template-2/assets/js/'.$request->get('slug_url').'/data.json', $json);
       }else{
           $filesystem->dumpFile($request->get('slug_url').'/index.html', $file3->getContents());
           $filesystem->dumpFile($request->get('slug_url').'/forget_password.html', $file_forget_password3->getContents());
          // $filesystem->dumpFile($request->get('slug_url').'/reset_password.html', $file_reset_password3->getContents());
           $json = json_encode(array('data' => $Registrations));
           $filesystem->dumpFile('forms/template-3/js/'.$request->get('slug_url').'/data.json', $json);
       }
       
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
    //    $filesystem = new Filesystem();
    //    $file1 = new SplFileInfo('/home/ihebitwi/public_html/private-chat-app/public/forms/template-1/index.html', '', '');
    //    $file2 = new SplFileInfo('/home/ihebitwi/public_html/private-chat-app/public/forms/template-2/index.html', '', '');
    //    $file3 = new SplFileInfo('/home/ihebitwi/public_html/private-chat-app/public/forms/template-3/index.html', '', '');
    //    if($request->get('template') == '1'){
    //        $filesystem->dumpFile($request->get('name').'/index.html', $file1->getContents());
    //    }else if($request->get('template') == '2'){
    //        $filesystem->dumpFile($request->get('name').'/index.html', $file2->getContents());
    //    }else{
    //        $filesystem->dumpFile($request->get('name').'/index.html', $file3->getContents());
    //    }
       
       $entityManagerInterface->persist($Registrations);
       $entityManagerInterface->flush();
       return new JsonResponse([
        'success' => 'true',
        'data' => $Registrations
    ]);

        
    }

    
}
