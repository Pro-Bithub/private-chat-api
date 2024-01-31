<?php

namespace App\Controller;


use App\Entity\UserLogs;
use App\Repository\ContactsRepository;
use App\Repository\ProfilesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class UpdatePasswordContactController extends AbstractController
{
    protected $parameterBag;

    /**
     * @var ContactsRepository
     * @var profilesRepository
     */
    private $ContactsRepository;
    private $profilesRepository;
    public function __construct(ParameterBagInterface $parameterBag, private MailerInterface $mailer, ContactsRepository $ContactsRepository, ProfilesRepository $profilesRepository)
    {
        $this->parameterBag = $parameterBag;
        $this->ContactsRepository = $ContactsRepository;
        $this->profilesRepository = $profilesRepository;
    }
    #[Route('/contact/email')]
    /**
     * Undocumented function
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendEmail(Request $request)
    {

        function addTrailingSlashIfMissing($str)
        {
            if (!in_array(substr($str, -1), ['/', '\\'])) {
                $str .= '/';
            }
            return $str;
        }
/* 
        $APP_PUBLIC_DIR = addTrailingSlashIfMissing($this->parameterBag->get('APP_PUBLIC_DIR'));

        $formstemplate = 'lang/email_verification_' . $request->get('lang') . '.json';
        $filePath = $APP_PUBLIC_DIR . $formstemplate;


        if (file_exists($filePath)) {

            $fileContent = file_get_contents($filePath);


            $dataArray = json_decode($fileContent, true);

            if ($dataArray !== null) {

                $subject = $dataArray['subject'];

                $email_verification_templete = 'templete/email_verification.html';
                $filePathemail_verification = $APP_PUBLIC_DIR . $email_verification_templete;
                $htmlTemplate = file_get_contents($filePathemail_verification);
                $replacement = $dataArray['content']['header'];
                $search = '${languageText.content.header}';
                $htmlTemplate = str_replace($search, $replacement, $htmlTemplate);
                $replacement = $dataArray['content']['body'];
                $search = '${languageText.content.body}';
                $htmlTemplate = str_replace($search, $replacement, $htmlTemplate);
                $replacement =       $TwoFactorAuthCode->code;
                $search = '${response.data.generated_code}';
                $htmlTemplate = str_replace($search, $replacement, $htmlTemplate);
                $replacement = $dataArray['content']['footer']['greeting'];
                $search = '${languageText.content.footer.greeting}';
                $htmlTemplate = str_replace($search, $replacement, $htmlTemplate);
                $replacement = $dataArray['content']['footer']['brand'];
                $search = '${languageText.content.footer.brand}';
                $htmlTemplate = str_replace($search, $replacement, $htmlTemplate);
                $replacement = $dataArray['content']['footer']['team'];
                $search = '${languageText.content.footer.team}';
                $htmlTemplate = str_replace($search, $replacement, $htmlTemplate);



                $email = (new Email())
                    ->from('hello@example.com')
                    ->to($request->get('email'))
                    ->subject($subject)
                    ->html($htmlTemplate);

                $mailer->send($email);
            }
        }
 */
    

        // dd($request->get('email'));
        $data = json_decode($request->getContent(), true);
        $name = $request->get('name');
        $url = $request->get('url');
        //dd($data);
        $contact = $this->ContactsRepository->loadContactByEmail($request->get('login'));
        if ($contact != null) {

            $APP_URL = addTrailingSlashIfMissing($this->parameterBag->get('APP_URL'));

            $key = "gcsit";
            $encryptedidcontact = openssl_encrypt($contact->id, 'AES-128-ECB', $key);
            $encryptedIdContactUrlSafe = urlencode($encryptedidcontact);

            $email = (new Email())
                ->from('hello@example.com')
                ->to($request->get('login'))
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Forgot password?')
                ->text('Sending emails is fun again!')
                ->html('
                <p>Hi,</p>
                <p>There was a request to change your password!</p>

                <p>If you did not make this request then please ignore this email.</p>

                <p>Otherwise, please click this link to change your password: <a href="' . $url . 'reset_password.html?uid=' . $encryptedIdContactUrlSafe . '" target="_top">[' . $url . 'reset_password.html?uid=' . $encryptedIdContactUrlSafe . ']</a></p>             
            ');

            $this->mailer->send($email);

            //dd($user->id);
            //return $user;







            return new JsonResponse([
                'success' => 'true',
                'data' => $contact,
                '$encryptedIdContactUrlSafe ' => $encryptedIdContactUrlSafe
            ]);
        } else {
            return new JsonResponse([
                'success' => 'false',
                'data' => null
            ]);
            //return null;
        }

        // ...
    }
    #[Route('/contact/reset_password')]
    public function ResetPassword(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManagerInterface): Response
    {


        $uid = $request->get('uid');

        $key = "gcsit"; // Replace with your secret key
        $encryptedEmail = $uid;

        // Decrypt the email
        $idContact = openssl_decrypt($encryptedEmail, 'AES-128-ECB', $key);
        if ($idContact !== false && !empty($idContact)) {

            $Contact = $this->ContactsRepository->findOneById($idContact);
            $profile = $this->profilesRepository->findOneByContact($idContact);
            //dd($profiles);
            //   $user = new User();
            if ($Contact &&  $profile) {

                $profile->password = $userPasswordHasher->hashPassword($profile, $request->get('password'));

                $time =  new \DateTimeImmutable();

                $UserLogs = new UserLogs();
                $UserLogs->user_id = $Contact->id;
                $UserLogs->action = 'Update Password Profile';
                $UserLogs->element = '30';
                $UserLogs->element_id = $profile->id;
                $UserLogs->log_date = $time;
                $UserLogs->source = '1';
                $entityManagerInterface->persist($UserLogs);
                $entityManagerInterface->flush();
                //$user->password = $userPasswordHasher->hashPassword($user,$request->get('password'));
                // $plainPassword = $request->get('password');
                // $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
                // $user->setPassword($hashedPassword);


                $entityManagerInterface->persist($profile);
                $entityManagerInterface->flush();


                $logs = new UserLogs();
                $logs->user_id = null;
                $logs->element = 20;
                $logs->action = 'update password';
                $logs->element_id = (int) $idContact;
                $logs->source = 3;
                $logs->log_date = new \DateTimeImmutable();

                $entityManagerInterface->persist($logs);
                $entityManagerInterface->flush();

                return new JsonResponse([
                    'success' => 'true',
                    'data' => $Contact
                ]);
            }
        }

        //$data = json_decode($request->getContent(), true);




        return new JsonResponse([
            'success' => 'false',
            'data' => null
        ]);
    }
}
