<?php

namespace App\Controller;

use App\Entity\Accounts;
use App\Entity\ContactCustomFields;
use App\Entity\Profiles;
use App\Entity\User;
use App\Repository\ContactsRepository;
use App\Repository\ProfilesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AddAccountsController extends AbstractController
{
    protected $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    
    public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {

        
        $RAW_QUERY2 = "SELECT a.*
                FROM `accounts` AS a
                ;";

        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
      
        $result1 = $stmt->executeQuery()->fetchAllAssociative();

      
        return new JsonResponse(   $result1);
    }

    #[Route('/addaccount')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher,  EntityManagerInterface $entityManagerInterface, ContactsRepository $contactsRepository, ProfilesRepository $profilesRepository): Response
    {


        $data = json_decode($request->getContent(), true);
        $response = [];

        $success = true;
        if (!isset($data['account'])) {
            $response['error'] = "The 'account' object is missing in the request.";
            $success = false;
        }
        if (!isset($data['user'])) {
            $response['error'] = "The 'user' object is missing in the request.";
            $success = false;
        }
        if ($success) {
            $account = $data['account'];
            $user = $data['user'];
            $successfiled = true;
            $requiredFields = ['name', 'api_key', 'url', 'folder'];
            $missingFields = [];

            foreach ($requiredFields as $field) {
                if (!isset($account[$field]) || empty($account[$field])) {
                    $missingFields[] = $field;
                }
            }
            if (!empty($missingFields)) {
                $successfiled = false;
                $response['account_error'] = (implode(', ', $missingFields)) . (count($missingFields) > 1 ? ' are' : ' is') . " missing or empty.";
            }

            $requiredUserFields = ['username', 'login', 'password'];
            $missingUserFields = [];

            foreach ($requiredUserFields as $field) {
                if (!isset($user[$field]) || empty($user[$field])) {
                    $missingUserFields[] = $field;
                }
            }

            if (!empty($missingUserFields)) {
                $successfiled = false;
                $response['user_error'] = (implode(', ', $missingUserFields)) . (count($missingUserFields) > 1 ? ' are' : ' is') . " missing or empty for the user.";
            }

            if ($successfiled) {

                $newaccounts = new Accounts();
                if (isset($account['name'])) {
                    $newaccounts->name = $account['name'];
                }
                if (isset($account['status'])) {
                    $newaccounts->status = $account['status'];
                } else {
                    $newaccounts->status = '1';
                }
                if (isset($account['date_start'])) {
                    $newaccounts->date_start = $account['date_start'];
                } else {
                    $newaccounts->date_start = new \DateTimeImmutable();
                }
                if (isset($account['date_end'])) {
                    $newaccounts->date_end = $account['date_end'];
                }

                if (isset($account['api_key'])) {
                    $newaccounts->api_key = $account['api_key'];
                }
                if (isset($account['url'])) {
                    $newaccounts->url = $account['url'];
                }
                if (isset($account['folder'])) {
                    $newaccounts->folder = $account['folder'];
                }

                $entityManagerInterface->persist($newaccounts);
                $entityManagerInterface->flush();

                $newAccountId = $newaccounts->id;
          
                $newuser = new User();
                $newuser->accountId = (string) $newAccountId;

                if (isset($user['gender'])) {
                    $newuser->gender =  $user['gender'];
                }
                if (isset($user['lastname'])) {
                    $newuser->lastname =  $user['lastname'];
                }
                if (isset($user['firstname'])) {
                    $newuser->firstname =  $user['firstname'];
                }

                $newuser->email =  $user['login'];


                $newuser->password = $userPasswordHasher->hashPassword($newuser,  $user['password']);

                $newuser->status = '1';
                $newuser->date_start =  new \DateTimeImmutable();

                $entityManagerInterface->persist($newuser);
                $entityManagerInterface->flush();


                $newuserId = $newuser->id;


                $profiles = new Profiles();
                $profiles->accountId = $newAccountId;
                $profiles->username = $user['username'];
                $profiles->login =   $user['login'];
                if (isset($user['user_key'])) {
                    $profiles->user_key = $user['user_key'];
                }
                $profiles->password = $userPasswordHasher->hashPassword($profiles, $user['password']);
                $profiles->u_type = '3';
                $profiles->u_id = $newuserId;
                $entityManagerInterface->persist($profiles);
                $entityManagerInterface->flush();

                $profiles->password = $userPasswordHasher->hashPassword($profiles,$user['password']);
                $entityManagerInterface->persist($profiles);
                $entityManagerInterface->flush();

                $response['success_db'] = 'Account successfully inserted into the database.';


                function addTrailingSlashIfMissing($str)
                {
                    if (!in_array(substr($str, -1), ['/', '\\'])) {
                        $str .= '/';
                    }
                    return $str;
                }
        
                $content = null;
                try {
                    $client = HttpClient::create();
                    $data = [
                        "nickname" =>$user['username'],
                        "full_name" => $user['username'],
                        "role" => "ADMIN",
                        "is_active" => false,
                        "is_online" => false,
                        "created_at" =>  date('Y-m-d H:i:s'),
                        "id" => $newuser->id,
                        "accountId" =>  $newuser->accountId,
                    ];
                    $ws_library = addTrailingSlashIfMissing($this->parameterBag->get('ws_library'));
                    $url = $ws_library . 'users';
                    $responsepost = $client->request('POST', $url, [
                        'json' => $data,
                    ]);
        
                    $status = $responsepost->getStatusCode();
                    if ($status < 400) {
                        $content = $responsepost->getContent();
                    }
                  $response['success_mango'] = 'Account successfully inserted into the mangoDB.';
                } catch (\Throwable $e) {
                    $response['account_error'] = $e->getMessage();
                    $status = 500;
                }


        
                try {
                    $client = HttpClient::create();
                 
                    $ws_library = addTrailingSlashIfMissing($this->parameterBag->get('ws_library'));
                    $url = $ws_library . 'users/changed/accounts';
                    $responsepost = $client->request('GET', $url);
        
                    $status = $responsepost->getStatusCode();
                    if ($status < 400) {
                        $content = $responsepost->getContent();
                    }
                  $response['success_RELOADING_list_account'] = 'Account successfully updated.';
                } catch (\Throwable $e) {
                    $response['account_error'] = $e->getMessage();
                    $status = 500;
                }

        


            }
        }

        return new JsonResponse($response);
    }
}
