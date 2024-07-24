<?php

namespace App\Controller;

use App\Entity\ContactLogs;
use App\Entity\Contacts;
use App\Entity\Profiles;
use App\Entity\TwoFactorAuthAccount;
use App\Entity\TwoFactorAuthCode;
use App\Entity\TwoFactorAuthRequests;
use App\Entity\UserLogs;
use App\Repository\ContactsRepository;
use App\Repository\ProfilesRepository;
use App\Repository\TwoFactorAuthAccountRepository;
use App\Repository\TwoFactorAuthRequestsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Namshi\JOSE\Signer\OpenSSL\RSA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sinergi\BrowserDetector\Os;
use SplFileInfo;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class CreatecontactaccountController extends AbstractController
{
    protected $parameterBag;


    /**
     * @var ProfilesRepository
     */
    private $ProfilesRepository;
    public function __construct(ParameterBagInterface $parameterBag, ProfilesRepository $ProfilesRepository)
    {
        $this->parameterBag = $parameterBag;
        $this->ProfilesRepository = $ProfilesRepository;
    }





    #[Route('/createcontactaccount', name: 'app_createcontactaccount')]
    public function createcontactaccount(MailerInterface $mailer, Request $request, TwoFactorAuthAccountRepository $TwoFactorAuthAccountRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManagerInterface): Response
    {



        $receiver = $request->get('email');
        $account_id = $request->get('account');
        $method = 1;
        $id_2fa_accounts = null;
        $sql = "SELECT  fa.id , fr.status , fc.id as id_code , fc.status as status_code   FROM `2fa_accounts` AS fa
         LEFT JOIN `2fa_codes` AS fc ON fc.account_id = fa.id
        LEFT JOIN `2fa_requests` AS fr ON fr.code_id = fc.id
        WHERE fr.id  is not null  and  fa.customer_account_id = :account_id and  fa.method = :method and fa.receiver= :receiver  and DATE(fa.date_start) <= CURDATE() and fa.status = 1   AND (fa.date_end IS NULL OR CURDATE() < DATE(fa.date_end)) 
        ";
        $statement = $entityManagerInterface->getConnection()->prepare($sql);
        $statement->bindValue('receiver', $receiver);
        $statement->bindValue('account_id', $account_id);
        $statement->bindValue('method', $method);
        $results = $statement->executeQuery()->fetchAllAssociative();
        $list_code_id_used = []; //for add all old code used
        if (count($results) > 0) {
            $isVerify = false;
            foreach ($results as $row) {
                if (isset($row['status'])) {
                    if ($row['status'] === 3) {
                        $isVerify = true;
                    }
                }
                if (isset($row['id'])) {
                    if (!isset($id_2fa_accounts))
                        $id_2fa_accounts = $row['id'];
                }

                if (isset($row['status_code'])) {
                    if ($row['status_code'] != 3) {
                        $list_code_id_used[] = $row['id_code'];
                    }
                }
            }
            if ($isVerify)
                return new JsonResponse([
                    'success' => false,
                    'error_type' => "ready_have_account",
                    'generated_code' =>  null,
                ]);
        }
        if (isset($id_2fa_accounts)) {
            $TwoFactorAuthAccount = $TwoFactorAuthAccountRepository->find($id_2fa_accounts);
            if (count($list_code_id_used) > 0) {
                $codes = implode(', ', array_map('intval', $list_code_id_used));
                $sql = "UPDATE 2fa_codes SET status = 3 WHERE id IN ($codes)";
                $statementused = $entityManagerInterface->getConnection()->prepare($sql);
                $statementused->execute();
            }
        } else {
            $TwoFactorAuthAccount = new TwoFactorAuthAccount();
            $TwoFactorAuthAccount->receiver = $receiver;
            $TwoFactorAuthAccount->method = $method;
            $TwoFactorAuthAccount->status = 1;
            $TwoFactorAuthAccount->date_start = new \DateTimeImmutable();
            $TwoFactorAuthAccount->customer_account_id = $account_id;
            $entityManagerInterface->persist($TwoFactorAuthAccount);
            $entityManagerInterface->flush();
        }


        $TwoFactorAuthCode = new TwoFactorAuthCode();
        $TwoFactorAuthCode->account_id  = $TwoFactorAuthAccount->id;
        $TwoFactorAuthCode->code  = mt_rand(100000, 999999); // Generates a random 6-digit number
        $TwoFactorAuthCode->status = 1;
        $TwoFactorAuthCode->date_creation = new \DateTimeImmutable();
        $entityManagerInterface->persist($TwoFactorAuthCode);
        $entityManagerInterface->flush();




        $TwoFactorAuthRequests = new TwoFactorAuthRequests();
        $TwoFactorAuthRequests->account_id = $TwoFactorAuthAccount->id;
        $TwoFactorAuthRequests->code_id = $TwoFactorAuthCode->id;
        $TwoFactorAuthRequests->date_sent  = new \DateTimeImmutable();
        $TwoFactorAuthRequests->status = 1;
        $entityManagerInterface->persist($TwoFactorAuthRequests);
        $entityManagerInterface->flush();




        $contact = new Contacts();
        $contact->accountId = $request->get('account');
        $contact->origin = $request->get('origin');
        $contact->name = $request->get('name');
        $contact->status = '1';
        $contact->email = $request->get('email');
        $contact->date_start = new \DateTime('@' . strtotime('now'));
        $contact->ip_address =  $this->container->get('request_stack')->getCurrentRequest()->getClientIp();

        $entityManagerInterface->persist($contact);
        $entityManagerInterface->flush();
        $time =  new \DateTimeImmutable();


   /*      $UserLogs = new UserLogs();
        $UserLogs->user_id = $contact->id;
        $UserLogs->action = 'Register Contact';
        $UserLogs->element = '27';
        $UserLogs->element_id = $contact->id;
        $UserLogs->log_date = $time;
        $UserLogs->source = '3';
        $entityManagerInterface->persist($UserLogs);
        $entityManagerInterface->flush();
 */
        $profiles = new Profiles();
        $profiles->ip_address =  $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
        $profiles->accountId = $request->get('account');
        $profiles->username = $request->get('name');
        $profiles->login = $request->get('email');
        $profiles->password = $userPasswordHasher->hashPassword($profiles, $request->get('password'));
        $profiles->u_type = '2';
        $profiles->u_id = $contact->id;
        $request1 = Request::createFromGlobals();
        $userAgent = $request1->headers->get('User-Agent');

        // Use a library like BrowserDetect to parse the user agent string
        $browser = new \Sinergi\BrowserDetector\Browser($userAgent);
        $os = new Os();



        //dd($os->getName());
        $browserName = $browser->getName();
        $profiles->browser_data = $browserName . ';' . $os->getName();
        // $profiles->browser_data = $_SERVER['HTTP_USER_AGENT'];
        $entityManagerInterface->persist($profiles);
        $entityManagerInterface->flush();

   /*      $UserLogs = new UserLogs();
        $UserLogs->user_id = $contact->id;
        $UserLogs->action = 'new Profile';
        $UserLogs->element = '30';
        $UserLogs->element_id = $profiles->id;
        $UserLogs->log_date = $time;
        $UserLogs->source = '3';
        $entityManagerInterface->persist($UserLogs);
        $entityManagerInterface->flush();
 */

        $ContactLogs = new ContactLogs();
        $ContactLogs->profile_id = $profiles->id;
        $ContactLogs->action = 8;
        $ContactLogs->element = 'register-form';
   /*      $ContactLogs->element_value =  $request->get('email'); */
        $ContactLogs->log_date = $time;
        $ContactLogs->browsing_data =    $browserName . ';' . $os->getName();
        $entityManagerInterface->persist($ContactLogs);
        $entityManagerInterface->flush();
        

        $email = urlencode($request->get('email'));


        function addTrailingSlashIfMissing($str)
        {
            if (!in_array(substr($str, -1), ['/', '\\'])) {
                $str .= '/';
            }
            return $str;
        }

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






        return new JsonResponse([
            'success' => true,
            'data' => $profiles,
            'account_id' => $contact->accountId,
            'password'=> $request->get('password')
        ]);
    }




    #[Route('/auth_profile', name: 'auth_profile')]
    public function CustomLogin(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManagerInterface): Response
    {
        // dd($request->all());

        $login = $request->get('login');
        $password = $request->get('password');
        $account = $request->get('account_id');
        // $pwd= Hash::check('password', $request->get('password'));
        // dd(array('login' => $login));
        $profiles = $this->ProfilesRepository->findContactProfileByemail($login, $account);
        // dd($account,$login);
        // $sql3= "SELECT * FROM `profiles` as p WHERE p.login = :login and p.u_type = 2 and p.account_id = :account_id";
        // $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        // $statement3->bindValue('login', $login);
        // $statement3->bindValue('account_id', $account);
        // $profiles = $statement3->executeQuery()->fetchAllAssociative();
        // dd($profiles);
        //$user = Profiles::where('login', $login)->first();
        // dd($userPasswordHasher->isPasswordValid($profiles, $password));
    
        if ($profiles == null) {
            return new JsonResponse([
                'success' => 'false',
                'message' => 'Email not exist'
            ]);
        } else if ($userPasswordHasher->isPasswordValid($profiles, $password) == false) {
            return new JsonResponse([
                'success' => 'false',
                'message' => 'Password not valid'
            ]);
        }
        if ($profiles == null && $userPasswordHasher->isPasswordValid($profiles, $password) == false) {
            // error, you can't change your password 
            // throw exception or return, etc.
            return new JsonResponse([
                'success' => 'false',
                'message' => 'User not found'
            ]);
        } else {
            //dd($this->container->get('request_stack')->getCurrentRequest()->getClientIp());
            $profiles->ip_address =  $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
            $request1 = Request::createFromGlobals();
            $userAgent = $request1->headers->get('User-Agent');

            // Use a library like BrowserDetect to parse the user agent string
            $browser = new \Sinergi\BrowserDetector\Browser($userAgent);
            $os = new Os();



            //dd($os->getName());
            $browserName = $browser->getName();
            $profiles->browser_data = $browserName . ';' . $os->getName();
            $entityManagerInterface->persist($profiles);
            $entityManagerInterface->flush();

         /*    $UserLogs = new UserLogs();
            $UserLogs->user_id = $profiles->u_id;
            $UserLogs->action = 'Login Contact';
            $UserLogs->element = '25';
            $UserLogs->element_id = $profiles->u_id;

            $UserLogs->log_date = new \DateTimeImmutable();
            $UserLogs->source = '3';

            $entityManagerInterface->persist($UserLogs);
            $entityManagerInterface->flush(); */

                $time =  new \DateTimeImmutable();
                $ContactLogs = new ContactLogs();
                $ContactLogs->profile_id = $profiles->id;
                $ContactLogs->action = 8;
                $ContactLogs->element = 'login-form';
                $ContactLogs->log_date = $time;
                $ContactLogs->browsing_data =    $browserName . ';' . $os->getName();
                $entityManagerInterface->persist($ContactLogs);
                $entityManagerInterface->flush();


            return new JsonResponse([
                'success' => 'true',
                'data' => $profiles
            ]);
        }
    }

    #[Route('/add_profile', name: 'add_profile')]
    public function addprofile(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManagerInterface): Response
    {
        $profiles = new Profiles();
        $profiles->ip_address =  $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
        $profiles->accountId = $request->get('accountId');
        $profiles->username = $request->get('username');
        $profiles->login = $request->get('login');
        $profiles->password = $userPasswordHasher->hashPassword($profiles, $request->get('password'));
        $profiles->u_type = '1';
        $profiles->u_id = $request->get('u_id');
        $request1 = Request::createFromGlobals();
        $userAgent = $request1->headers->get('User-Agent');

        // Use a library like BrowserDetect to parse the user agent string
        $browser = new \Sinergi\BrowserDetector\Browser($userAgent);
        $os = new Os();



        //dd($os->getName());
        $browserName = $browser->getName();
        $profiles->browser_data = $browserName . ';' . $os->getName();
        // $profiles->browser_data = $_SERVER['HTTP_USER_AGENT'];
        $entityManagerInterface->persist($profiles);
        $entityManagerInterface->flush();

        return new JsonResponse([
            'success' => 'true',
            'data' => $profiles
        ]);
    }
    #[Route('/delete_contact/{id}', name: 'app_delete_contact_controller')]
    public function deletecontact(
        $id,
        ContactsRepository $contactsRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
    ): Response {
        $Contact = $contactsRepository->find($id);
        $data = json_decode($request->getContent(), true);
        $Contact->date_end = new \DateTimeImmutable();
        $Contact->status = '0';


        //dd($clickableLinksUser);
        $entityManagerInterface->persist($Contact);
        $entityManagerInterface->flush();

        $logs = new UserLogs();
        $logs->user_id = $data['user_id'];
        $logs->element = 20;
        $logs->action = 'delete';
        $logs->element_id = $Contact->id;
        $logs->source = 1;
        $logs->log_date = new \DateTimeImmutable();

        $entityManagerInterface->persist($logs);
        $entityManagerInterface->flush();



        return new JsonResponse([
            'success' => true,
            'data' => $Contact,
        ]);
    }

    #[Route('/check_email_contact', name: 'check_email_contact')]
    public function checkemail(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManagerInterface): Response
    {
        $login = $request->get('login');
        $account = $request->get('account');
        //$profiles = $this->ProfilesRepository->findContactProfileByemail(array('login' => $login));
        $sql = "SELECT p.login , c.status
        FROM `profiles` AS p
        LEFT JOIN `contacts` AS c ON c.id = p.u_id
        WHERE p.u_type = 2 AND c.status = 1 and p.login = :login and c.account_id = :account";

        $statement = $entityManagerInterface->getConnection()->prepare($sql);
        $statement->bindValue('login', $login);
        $statement->bindValue('account', $account);
        $profiles = $statement->executeQuery()->fetchAllAssociative();
        if (count($profiles) > 0) {
            return new JsonResponse([
                'success' => 'false',
                'data' => $profiles
            ]);
        } else {
            return new JsonResponse([
                'success' => 'true',
                'data' => $profiles
            ]);
        }


        return new JsonResponse([
            'success' => 'true',
            'data' => $profiles
        ]);
    }

    #[Route('/login/2fa/verify', name: 'app_login_2fa_verify')]
    public function verify(Request $request, EntityManagerInterface $entityManagerInterface, TwoFactorAuthRequestsRepository $TwoFactorAuthRequestsRepository): Response
    {


        $data = json_decode($request->getContent(), true);

        $account_id = $request->get('account');
        $method = 1;
        $receiver = $request->get('receiver');
        $code =  $request->get('code');

        $id_2fa_requests = null;

        $sql = "SELECT  fr.id , fr.status,fr.date_sent   , fc.status as status_code   FROM `2fa_accounts` AS fa
        LEFT JOIN `2fa_requests` AS fr ON fr.account_id = fa.id
        LEFT JOIN `2fa_codes` AS fc ON fc.id = fr.code_id
        WHERE fa.method = :method  and  fa.customer_account_id = :account_id and fa.receiver= :receiver and fc.code= :code  and fa.status = 1 
        ";

        $statement = $entityManagerInterface->getConnection()->prepare($sql);
        $statement->bindValue('receiver', $receiver);
        $statement->bindValue('method', $method);
        $statement->bindValue('account_id', $account_id);
        $statement->bindValue('code', $code);
        $results = $statement->executeQuery()->fetchAllAssociative();

        if (count($results) == 0) {
            return new JsonResponse([
                'success' => false,
                'error_type' => "INCORRECET_CODE",
            ]);
        }
        $isREADYVerify = false;
        $isvaliddate = false;
        $isreject = false;
        $isUsed = false;

        foreach ($results as $row) {
            if (isset($row['status_code'])) {
                if ($row['status_code'] === 3) {
                    $isUsed = true;
                    break;
                }
            }

            if (isset($row['status'])) {
                if ($row['status'] === 3) {
                    $isREADYVerify = true;
                    break;
                }
                if ($row['status'] === 2) {
                    $isreject = true;
                    break;
                }
            }
            if (isset($row['date_sent'])) {

                $id_2fa_requests = $row['id'];
                $currentDateTime = new \DateTime();

                $dateSent = new \DateTime($row['date_sent']);
                $dateSent->modify('+1 hour');

                if ($currentDateTime <= $dateSent) {

                    $isvaliddate = true;
                    break;
                }
            }
        }
        if ($isreject)
            return new JsonResponse([
                'success' => false,
                'error_type' => "EXPIRED_DATE",
            ]);

        if ($isUsed) {
            return new JsonResponse([
                'success' => false,
                'error_type' => "INCORRECET_CODE",
            ]);
        }
        if ($isREADYVerify)
            return new JsonResponse([
                'success' => false,
                'error_type' => "READY_VRIFYIED",
            ]);

        if ($isvaliddate == false) {

            $TwoFactorAuthRequests = $TwoFactorAuthRequestsRepository->find($id_2fa_requests);
            $TwoFactorAuthRequests->date_reject = new \DateTimeImmutable();
            $TwoFactorAuthRequests->status = 2;
            $entityManagerInterface->persist($TwoFactorAuthRequests);
            $entityManagerInterface->flush();
            return new JsonResponse([
                'success' => false,
                'error_type' => "EXPIRED_DATE",
            ]);
        }


        $TwoFactorAuthRequests = $TwoFactorAuthRequestsRepository->find($id_2fa_requests);
        $TwoFactorAuthRequests->date_verification = new \DateTimeImmutable();
        $TwoFactorAuthRequests->status = 3;
        $entityManagerInterface->persist($TwoFactorAuthRequests);
        $entityManagerInterface->flush();


        $profiles = $this->ProfilesRepository->findContactProfileByemail($receiver,  $account_id);

        if ($profiles != null) {
            $profiles->ip_address =  $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
            $request1 = Request::createFromGlobals();
            $userAgent = $request1->headers->get('User-Agent');

            // Use a library like BrowserDetect to parse the user agent string
            $browser = new \Sinergi\BrowserDetector\Browser($userAgent);
            $os = new Os();



            //dd($os->getName());
            $browserName = $browser->getName();
            $profiles->browser_data = $browserName . ';' . $os->getName();
            $entityManagerInterface->persist($profiles);
            $entityManagerInterface->flush();

    /*         $UserLogs = new UserLogs();
            $UserLogs->user_id = $profiles->u_id;
            $UserLogs->action = 'Login Contact';
            $UserLogs->element = '25';
            $UserLogs->element_id = $profiles->u_id;

            $UserLogs->log_date = new \DateTimeImmutable();
            $UserLogs->source = '3';

            $entityManagerInterface->persist($UserLogs);
            $entityManagerInterface->flush();
 */
            $time =  new \DateTimeImmutable();
            $ContactLogs = new ContactLogs();
            $ContactLogs->profile_id = $profiles->id;
            $ContactLogs->action = 8;
            $ContactLogs->element = 'sing-in-two-steps-form';
            $ContactLogs->log_date = $time;
            $ContactLogs->browsing_data =    $browserName . ';' . $os->getName();
            $entityManagerInterface->persist($ContactLogs);
            $entityManagerInterface->flush();
      

            return new JsonResponse([
                'success' => true,
                'verify' => true,
                'data' => $profiles
            ]);
        } else {

            return new JsonResponse([
                'success' => false,
                'error_type' => "NOACCOUNT",
            ]);
        }
    }

    #[Route('/login/2fa/generate', name: 'app_login_2fa_generate')]
    public function generate(MailerInterface $mailer,Request $request, EntityManagerInterface $entityManagerInterface, TwoFactorAuthAccountRepository $TwoFactorAuthAccountRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        $receiver = $request->get('receiver');
        $account_id = $request->get('account');

        if (
            isset($receiver) && !is_null($receiver) && $receiver !== ''
            && isset($account_id) && !is_null($account_id) && $account_id !== ''
        ) {



            $method = 1;

            $id_2fa_accounts = null;

            $sql = "SELECT  fa.id , fr.status , fc.id as id_code , fc.status as status_code   FROM `2fa_accounts` AS fa
         LEFT JOIN `2fa_codes` AS fc ON fc.account_id = fa.id
        LEFT JOIN `2fa_requests` AS fr ON fr.code_id = fc.id
        WHERE fr.id  is not null  and  fa.customer_account_id = :account_id and  fa.method = :method and fa.receiver= :receiver  and DATE(fa.date_start) <= CURDATE() and fa.status = 1   AND (fa.date_end IS NULL OR CURDATE() < DATE(fa.date_end)) 
        ";

            $statement = $entityManagerInterface->getConnection()->prepare($sql);
            $statement->bindValue('receiver', $receiver);
            $statement->bindValue('account_id', $account_id);
            $statement->bindValue('method', $method);
            $results = $statement->executeQuery()->fetchAllAssociative();
            $list_code_id_used = []; //for add all old code used
            if (count($results) > 0) {
                $isVerify = false;
                foreach ($results as $row) {
                    if (isset($row['status'])) {
                        if ($row['status'] === 3) {
                            $isVerify = true;
                        }
                    }
                    if (isset($row['id'])) {
                        if (!isset($id_2fa_accounts))
                            $id_2fa_accounts = $row['id'];
                    }

                    if (isset($row['status_code'])) {
                        if ($row['status_code'] != 3) {
                            $list_code_id_used[] = $row['id_code'];
                        }
                    }
                }
                if ($isVerify)
                    return new JsonResponse([
                        'success' => false,
                        'error_type' => "READY_VRIFYIED",
                        'generated_code' =>  null,
                    ]);
            }
            if (isset($id_2fa_accounts)) {
                $TwoFactorAuthAccount = $TwoFactorAuthAccountRepository->find($id_2fa_accounts);
                if (count($list_code_id_used) > 0) {
                    $codes = implode(', ', array_map('intval', $list_code_id_used));
                    $sql = "UPDATE 2fa_codes SET status = 3 WHERE id IN ($codes)";
                    $statementused = $entityManagerInterface->getConnection()->prepare($sql);
                    $statementused->execute();
                }
            } else {
                return new JsonResponse([
                    'success' => false,
                    'error_type' => "usernotfound",
                    'generated_code' =>  null,
                ]);
                /*       $TwoFactorAuthAccount = new TwoFactorAuthAccount();
            $TwoFactorAuthAccount->receiver = $receiver;
            $TwoFactorAuthAccount->method = $method;
            $TwoFactorAuthAccount->status = 1;
            $TwoFactorAuthAccount->date_start = new \DateTimeImmutable();
            $TwoFactorAuthAccount->customer_account_id = $account_id;
            $entityManagerInterface->persist($TwoFactorAuthAccount);
            $entityManagerInterface->flush(); */
            }


            $TwoFactorAuthCode = new TwoFactorAuthCode();
            $TwoFactorAuthCode->account_id  = $TwoFactorAuthAccount->id;
            $TwoFactorAuthCode->code  = mt_rand(100000, 999999); // Generates a random 6-digit number
            $TwoFactorAuthCode->status = 1;
            $TwoFactorAuthCode->date_creation = new \DateTimeImmutable();
            $entityManagerInterface->persist($TwoFactorAuthCode);
            $entityManagerInterface->flush();




            $TwoFactorAuthRequests = new TwoFactorAuthRequests();
            $TwoFactorAuthRequests->account_id = $TwoFactorAuthAccount->id;
            $TwoFactorAuthRequests->code_id = $TwoFactorAuthCode->id;
            $TwoFactorAuthRequests->date_sent  = new \DateTimeImmutable();
            $TwoFactorAuthRequests->status = 1;
            $entityManagerInterface->persist($TwoFactorAuthRequests);
            $entityManagerInterface->flush();



            
        function addTrailingSlashIfMissing2($str)
        {
            if (!in_array(substr($str, -1), ['/', '\\'])) {
                $str .= '/';
            }
            return $str;
        }

        $APP_PUBLIC_DIR = addTrailingSlashIfMissing2($this->parameterBag->get('APP_PUBLIC_DIR'));

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
                    ->to($receiver )
                    ->subject($subject)
                    ->html($htmlTemplate);

                $mailer->send($email);
            }
        }





            return new JsonResponse([
                'success' => true,

            ]);
        }

        return new JsonResponse([
            'success' => false,
            'error_type' => "usernotfound",
            'generated_code' =>  null,
        ]);
    }
}
