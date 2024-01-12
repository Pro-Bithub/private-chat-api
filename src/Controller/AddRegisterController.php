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

    public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface)
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

        $token = substr($authorizationHeader, 7);

        $tokenData = $this->get('security.token_storage')->getToken();

        if ($tokenData === null) {
            throw new AccessDeniedException('Invalid token.');
        }



        $user = $tokenData->getUser();
        $RAW_QUERY5 =
            'SELECT a.folder
       FROM accounts AS a
       WHERE a.id = :id
        ;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY5);
        $stmt->bindValue('id', $user->accountId);

        $results = $stmt->executeQuery()->fetchAllAssociative();

        if (!empty($results) && isset($results[0]['folder'])) {
           
            $folderValue = $results[0]['folder'];

            if (substr($folderValue, -1) !== '\\') {
            
                $folderValue .= '\\';
            }
            
            $APP_PUBLIC_DIR = addTrailingSlashIfMissing($this->parameterBag->get('APP_PUBLIC_DIR'));
            $APP_URL = addTrailingSlashIfMissing($this->parameterBag->get('APP_URL'));

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
            $Registrations->date_start = new \DateTime('@' . strtotime('now'));
            $Registrations->status = $data['status'];
            $Registrations->url = $data['url'];
            $Registrations->lang = $data['lang'];

            $formstemplate = 'forms/template-' . $data['template'];
            $newBaseHref = $APP_URL . $formstemplate . '/';


            //for page contact
            $formstemplateContact = 'forms/template-contact';
            $newBaseContactHref = $APP_URL . $formstemplateContact . '/';
            $fileContact = new SplFileInfo($APP_PUBLIC_DIR . $formstemplateContact . '/index.html', '', '');
            $fileContentsContact = $fileContact->getContents();
            $fileContentsContact = str_replace('[base-href]',  $newBaseContactHref, $fileContentsContact);
            $filesystem->dumpFile($folderValue.$data['slug_url'] . '/contact/index.html',  $fileContentsContact);

            $fileContacterror = new SplFileInfo($APP_PUBLIC_DIR . $formstemplateContact . '/error.html', '', '');
            $fileContentsContacterror = $fileContacterror->getContents();
            $fileContentsContacterror = str_replace('[base-href]',  $newBaseContactHref, $fileContentsContacterror);
            $filesystem->dumpFile($folderValue.$data['slug_url'] . '/contact/error.html',  $fileContentsContacterror);

            $fileContactsuccess = new SplFileInfo($APP_PUBLIC_DIR . $formstemplateContact . '/success.html', '', '');
            $fileContentsContactsuccess = $fileContactsuccess->getContents();
            $fileContentsContactsuccess = str_replace('[base-href]',  $newBaseContactHref, $fileContentsContactsuccess);
            $filesystem->dumpFile($folderValue.$data['slug_url'] . '/contact/success.html',  $fileContentsContactsuccess);

            $fileContactrequest = new SplFileInfo($APP_PUBLIC_DIR . $formstemplateContact . '/request.php', '', '');
            $fileContentsContactrequest = $fileContactrequest->getContents();
            $fileContentsContactrequest = str_replace('[base-href]',  $newBaseContactHref, $fileContentsContactrequest);
            $filesystem->dumpFile($folderValue.$data['slug_url'] . '/contact/request.php',  $fileContentsContactrequest);
            //

            $lang = ($Registrations->lang == 1) ? 'en' : 'fr';



            $file = new SplFileInfo($APP_PUBLIC_DIR . $formstemplate . '/index.html', '', '');
            $fileContents = $file->getContents();
            $fileContents = str_replace('[base-href]',  $newBaseHref, $fileContents);
            $fileContents = str_replace('[api-url]',  $APP_URL, $fileContents);
            $fileContents = str_replace('[lang]',  $lang, $fileContents);



            $file_forget_password = new SplFileInfo($APP_PUBLIC_DIR . $formstemplate . '/forget_password.html', '', '');
            $fileContentsfgp = $file_forget_password->getContents();
            $fileContentsfgp = str_replace('[base-href]',  $newBaseHref, $fileContentsfgp);
            $fileContentsfgp =  str_replace('[api-url]', $APP_URL, $fileContentsfgp);
            $fileContentsfgp =  str_replace('[lang]', $lang, $fileContentsfgp);



            $filesystem->dumpFile($folderValue.$data['slug_url'] . '/index.html',  $fileContents);
            $filesystem->dumpFile($folderValue.$data['slug_url'] . '/forget_password.html',  $fileContentsfgp);
            // $filesystem->dumpFile($folderValue.$data['slug_url'].'/reset_password.html', $file_reset_password1->getContents());

            $file_reset_password = new SplFileInfo($APP_PUBLIC_DIR . $formstemplate . '/reset_password.html', '', '');
            $fileContentsrestpwd = $file_reset_password->getContents();
            $fileContentsrestpwd = str_replace('[base-href]',  $newBaseHref, $fileContentsrestpwd);
            $fileContentsrestpwd = str_replace('[api-url]',  $APP_URL, $fileContentsrestpwd);
            $fileContentsrestpwd = str_replace('[lang]', $lang, $fileContentsrestpwd);

            $filesystem->dumpFile($folderValue.$data['slug_url'] . '/reset_password.html',  $fileContentsrestpwd);



            $json = json_encode(array('data' => $Registrations, 'api_url' => $APP_URL));
            $filesystem->dumpFile($folderValue.$data['slug_url']  . '/data.json', $json);




            $filesystem->chmod($folderValue.$data['slug_url'], 0755);
            $filesystem->chmod($folderValue.$data['slug_url'] . '/contact', 0755);
            $filesystem->chmod($folderValue.$data['slug_url'] . '/contact/request.php', 0644);



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

 

            return new JsonResponse([
                'success' => true,
                'data' => $Registrations,
                'cc'=>$folderValue.$data['slug_url']
            ]);

        }
        return new JsonResponse([
            'success' => false,

        ]);
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
    public function getPageUrl(): Response
    {
        function addTrailingSlashIfMissing2($str)
        {
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
