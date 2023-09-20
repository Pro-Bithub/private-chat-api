<?php

namespace App\Controller;

use App\Entity\Registrations;
use App\Entity\UserLogs;
use App\Repository\RegistrationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AddRegisterController extends AbstractController
{
    protected $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;

    }
    
    public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface): Registrations
    {
        function addTrailingSlashIfMissing($str) {
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

        // Extract the token value (without the "Bearer " prefix)
        $token = substr($authorizationHeader, 7);

        $tokenData = $this->get('security.token_storage')->getToken();

        if ($tokenData === null) {
            throw new AccessDeniedException('Invalid token.');
        }
        $APP_PUBLIC_DIR = addTrailingSlashIfMissing($this->parameterBag->get('APP_PUBLIC_DIR'));
        $APP_URL = addTrailingSlashIfMissing($this->parameterBag->get('APP_URL'));
        // Now you can access the user data from the token (assuming your User class has a `getUsername()` method)
        $user = $tokenData->getUser();
       // dd($this->parameterBag->get('kernel.project_dir'));
       $filesystem = new Filesystem();
        // dd($filesystem);
        $Registrations = new Registrations();
        $data = json_decode($request->getContent(), true);
        //dump($data);
        $Registrations->accountId = $user->accountId;
        $Registrations->name = $data['name'];
        $Registrations->slug_url = $data['slug_url'];
        $Registrations->redirect_url = $data['redirect_url'];
        $Registrations->comment = $data['comment'];
        $Registrations->template = $data['template'];
        $Registrations->date_start = new \DateTime('@'.strtotime('now'));
        $Registrations->status = $data['status'];
        $Registrations->url = $data['url'];

        $formstemplate='forms/template-'.$data['template'];
        $newBaseHref = $APP_URL.$formstemplate.'/'; 

        $file = new SplFileInfo($APP_PUBLIC_DIR.$formstemplate.'/index.html', '', '');
        $fileContents = $file->getContents();
        $fileContents = str_replace('[base-href]',  $newBaseHref , $fileContents);
      
        $file_forget_password = new SplFileInfo($APP_PUBLIC_DIR.$formstemplate.'/forget_password.html', '', '');
        $fileContentsfgp = $file_forget_password->getContents();
        $fileContentsfgp = str_replace('[base-href]',  $newBaseHref , $fileContentsfgp);

        
        $filesystem->dumpFile($data['slug_url'].'/index.html',  $fileContents);
        $filesystem->dumpFile($data['slug_url'].'/forget_password.html',  $fileContentsfgp);
       // $filesystem->dumpFile($data['slug_url'].'/reset_password.html', $file_reset_password1->getContents());
        $json = json_encode(array('data' => $Registrations));
        $filesystem->dumpFile('forms/template-'.$data['template'].'/assets/js/'.$data['slug_url'].'/data.json', $json);

   
     


        $entityManagerInterface->persist($Registrations);
        $entityManagerInterface->flush();
        
        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 26;
        $logs->action = 'create';
        $logs->element_id = $Registrations->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();
        
        return $Registrations; 
    }

    #[Route('/delete_registrations/{id}', name: 'app_delete_registrations_controller')]
    public function deleteregistrations(
        $id,
        RegistrationsRepository $registrationsRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
    ): Response {

         
        $authorizationHeader = $request->headers->get('Authorization');

        // Check if the token is present and in the expected format (Bearer TOKEN)
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            throw new AccessDeniedException('Invalid or missing authorization token.');
        }

        // Extract the token value (without the "Bearer " prefix)
        $token = substr($authorizationHeader, 7);

        $tokenData = $this->get('security.token_storage')->getToken();

        if ($tokenData === null) {
            throw new AccessDeniedException('Invalid token.');
        }
    
        // Now you can access the user data from the token (assuming your User class has a `getUsername()` method)
        // $user = $tokenData->getUser();
        $registrations = $registrationsRepository->find($id);
        $data = json_decode($request->getContent(), true);
        $registrations->date_end = new \DateTimeImmutable();
        $registrations->status = '0';
    
       
        //dd($clickableLinksUser);
        $entityManagerInterface->persist($registrations);
        $entityManagerInterface->flush();
    
        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 26;
        $logs->action = 'delete';
        $logs->element_id = $registrations->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();
    
        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();
    
       
    
        return new JsonResponse([
            'success' => true,
            'data' => $registrations,
        ]);
    }

    #[Route('get/page/url', name: 'app_get_page_url')]
    public function getPageUrl(
    ): Response {
        function addTrailingSlashIfMissing2($str) {
            if (!in_array(substr($str, -1), ['/', '\\'])) {
                $str .= '/';
            }
            return $str;
        }

       $APP_URL = addTrailingSlashIfMissing2($this->parameterBag->get('APP_URL'));
       
       return new JsonResponse([
        'success' => true,
        'data' => $APP_URL,
    ]);
       
    }

}
