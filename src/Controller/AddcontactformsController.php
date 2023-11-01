<?php

namespace App\Controller;

use App\Entity\ContactCustomFields;
use App\Entity\ContactFormFields;
use App\Entity\Contacts;
use App\Entity\Profiles;
use App\Entity\UserLogs;
use App\Repository\ContactsRepository;
use App\Repository\ProfilesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sinergi\BrowserDetector\Os;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AddcontactformsController extends AbstractController
{
    #[Route('/addcontactforms')]
    public function index(Request $request,UserPasswordHasherInterface $userPasswordHasher,  EntityManagerInterface $entityManagerInterface,ContactsRepository $contactsRepository,ProfilesRepository $profilesRepository): Response
    {

        // $authorizationHeader = $request->headers->get('Authorization');

        // // Check if the token is present and in the expected format (Bearer TOKEN)
        // if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
        //     throw new AccessDeniedException('Invalid or missing authorization token.');
        // }

        // // Extract the token value (without the "Bearer " prefix)
        // $token = substr($authorizationHeader, 7);

        // $tokenData = $this->get('security.token_storage')->getToken();
        // // dd($tokenData);
        // if ($tokenData === null) {
        //     throw new AccessDeniedException('Invalid token.');
        // }$data['account']
    
        // // Now you can access the user data from the token (assuming your User class has a `getUsername()` method)
        // $user = $tokenData->getUser();

        $data = json_decode($request->getContent(), true);
        $contactforms = $data['forms'];
      
    
        $sql1 = "SELECT c.id AS contact_id, p.id AS profile_id
        FROM `profiles` AS p
        LEFT JOIN `contacts` AS c ON c.id = p.u_id  
        WHERE p.id = :id AND c.status = 1";
        $statement1 = $entityManagerInterface->getConnection()->prepare($sql1);
        $statement1->bindValue('id', $data['contact']);
        $result = $statement1->executeQuery()->fetchAssociative();

        $contactId = $result['contact_id'];
        $profileId = $result['profile_id'];

       

        $firstname="";
        $lastname="";
        $email="";
        $phone="";
        $country="";
        $name="";
        $gender = '';
        $date_birth=null;
        
        
        //$fieldid = $data['forms']['fieldId'];
       //dd($data,$contactforms, $contactid);
        foreach ($contactforms  as $i => $value) {
            //dd($benefit[$i]['title']);
            //dd($value['fieldId']);
            //dd($title);
            $ContactCustomFields = new ContactCustomFields();
            $ContactCustomFields->contactId = $contactId;
            $ContactCustomFields->formFieldId = $value['fieldId'];
            $ContactCustomFields->field_value = $value['value'];
            $ContactCustomFields->created_at = new \DateTimeImmutable();
           // $ContactCustomFields->save();
            $entityManagerInterface->persist($ContactCustomFields);
            $entityManagerInterface->flush();

          /* user not known  
            $logs = new UserLogs();
            $logs->user_id = $data['user_id'];
            $logs->element = 22;
            $logs->action = 'create';
            $logs->element_id = $ContactCustomFields->id;
            $logs->source = 1;
            $logs->log_date = new \DateTimeImmutable();

            $entityManagerInterface->persist($logs);
            $entityManagerInterface->flush(); */

            $sql = "SELECT c.field_name , c.field_type
            from `contact_form_fields` AS cf 
            LEFT JOIN `custom_fields` AS c  ON cf.field_id = c.id
            WHERE cf.id = :id and c.status = 1";
            
            $statement = $entityManagerInterface->getConnection()->prepare($sql);
            $statement->bindValue('id', $value['fieldId']);
            $field = $statement->executeQuery()->fetchAssociative();
            //  dd($field);
     
        

            if($field['field_type']!=null){
                $field_type = intval($field['field_type']);
                switch ($field_type) {
                    case 10:
                        $firstname = $value['value'];
                        break;
                    case 11:
                        $lastname = $value['value'];
                        break;
                    case 6:
                        $email = $value['value'];
                        break;
                    case 7:
                        $phone = $value['value'];
                        break;
                    case 8:
                        $country = $value['value'];
                        break;
                    case 13:
                      if($value['value'])  {
                        $input = $value['value'];
                            if (strcasecmp(substr($input, 0, 1), 'H') === 0 || strcasecmp(substr($input, 0, 1), 'M') === 0) {
                                $gender = 'H';
                            }else{
                                $gender = 'F';
                            }
                       }
                  
                        break;
                    case 14:
                        $dateOfBirth = \DateTimeImmutable::createFromFormat('Y-m-d', $value['value']);
                        if ($dateOfBirth) {
                            $date_birth = $dateOfBirth;
                        }
                        break;
                    case 15:
                        $name = $value['value'];
                        break;
                 
                        
                }
            }

    /*    old if($field['field_name']!=null){
            $field_name = strtolower(str_replace(' ', '', $field['field_name']));
            switch ($field_name) {
                case 'firstname':
                    $firstname = $value['value'];
                    break;
                case 'lastname':
                    $lastname = $value['value'];
                    break;
                case 'email':
                case 'e-mail':
                case 'mail':
                    $email = $value['value'];
                    break;
                case 'phone':
                    $phone = $value['value'];
                    break;
                case 'country':
                    $country = $value['value'];
                    break;
                case 'birthdate':
                    $dateOfBirth = \DateTimeImmutable::createFromFormat('Y-m-d', $value['value']);
                    if ($dateOfBirth) {
                        $date_birth = $dateOfBirth;
                    }
                    break;
                case 'name':
                    $name = $value['value'];
                    break;
            }

        }
       */

     
     


        }

        $contact = $contactsRepository->find($contactId);
        if (!empty($firstname)) 
        $contact->firstname = $firstname;
        if (!empty($lastname)) 
        $contact->lastname = $lastname;
        if (!empty($email)) 
        $contact->email = $email;
        if (!empty($phone)) 
        $contact->phone = $phone;
        if (!empty($country)) 
        $contact->country = $country;
        if ($date_birth!=null) 
        $contact->date_birth = $date_birth;
        if (!empty($name)) 
        $contact->name = $name;
        if (!empty($gender)) 
        $contact->gender = $gender;
        $entityManagerInterface->persist($contact);
        $entityManagerInterface->flush();

        $profile = $profilesRepository->find($profileId);
        if (!empty($email)) {
            $profile->password = $userPasswordHasher->hashPassword($profile,$email);  
            $entityManagerInterface->persist($profile);
            $entityManagerInterface->flush();
        }
   
      
        $data = [];
        $data['login'] = $profile->login;
        $data['password'] = $email;
        $data['firstname'] = $contact->firstname ?? ''; 
        $data['lastname'] = $contact->lastname ?? '';   

        return new JsonResponse([
            'success' => 'true',
            'data' => $data
        ]);
    }


    #[Route('/plan_client')]
    public function getPlanclient(Request $request, EntityManagerInterface $entityManagerInterface): JsonResponse
    {
        // $authorizationHeader = $request->headers->get('Authorization');

        // // Check if the token is present and in the expected format (Bearer TOKEN)
        // if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
        //     throw new AccessDeniedException('Invalid or missing authorization token.');
        // }

        // // Extract the token value (without the "Bearer " prefix)
        // $token = substr($authorizationHeader, 7);

        // $tokenData = $this->get('security.token_storage')->getToken();

        // if ($tokenData === null) {
        //     throw new AccessDeniedException('Invalid token.');
        // }
    
        // Now you can access the user data from the token (assuming your User class has a `getUsername()` method)
        //  $user = $tokenData->getUser();
        // dd($request->headers->get('account'));
        $data = json_decode($request->getContent(), true);
        // $key = $request->headers->get('key');

        // $RAW_QUERY1 = 'SELECT * FROM accounts WHERE app_key = :key';
        // $stmt1 = $entityManagerInterface->getConnection()->prepare($RAW_QUERY1);
        // $stmt1->bindValue('key', $key);
        // $result = $stmt1->executeQuery()->fetchAllAssociative();



        $RAW_QUERY2 = "SELECT p.*
        FROM `plans` AS p
        LEFT JOIN `plan_users` AS pu ON p.id = pu.plan_id 
        LEFT JOIN `profiles` AS pr ON pr.u_id = pu.user_id
        WHERE p.status = 1 and (p.account_id = :account and p.date_start <= CURDATE() and (p.date_end >= CURDATE() or p.date_end is null))
        group by p.id
        ;";

        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        $stmt->bindValue('account', $request->attributes->get('account'));

        $result1 = $stmt->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'success' => 'true',
            'data' => $result1
        ]);
    }
   
  
}
