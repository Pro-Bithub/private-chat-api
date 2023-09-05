<?php

namespace App\Controller;

use App\Entity\ContactCustomFields;
use App\Entity\ContactFormFields;
use App\Repository\ContactsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AddcontactformsController extends AbstractController
{
    #[Route('/addcontactforms')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface,ContactsRepository $contactsRepository): Response
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
        // }
    
        // // Now you can access the user data from the token (assuming your User class has a `getUsername()` method)
        // $user = $tokenData->getUser();

        $data = json_decode($request->getContent(), true);
        $contactforms = $data['forms'];

        $sql1 = "SELECT c.id
        from `profiles` AS p
        left join `contacts` as c on c.id = p.u_id  
        WHERE p.id = :id and c.status = 1";
        
        $statement1 = $entityManagerInterface->getConnection()->prepare($sql1);
        $statement1->bindValue('id', $data['contact']);
        $profile = $statement1->executeQuery()->fetchAssociative();
        // dd();
        $contactid =$profile['id'];

        //$fieldid = $data['forms']['fieldId'];
       //dd($data,$contactforms, $contactid);
        foreach ($contactforms  as $i => $value) {
            //dd($benefit[$i]['title']);
            //dd($value['fieldId']);
            //dd($title);
            $ContactCustomFields = new ContactCustomFields();
            $ContactCustomFields->contactId = $contactid;
            $ContactCustomFields->formFieldId = $value['fieldId'];
            $ContactCustomFields->field_value = $value['value'];
           // $ContactCustomFields->save();
            $entityManagerInterface->persist($ContactCustomFields);
            $entityManagerInterface->flush();

            $sql = "SELECT c.field_name
            from `custom_fields` AS c  
            WHERE c.id = :id and c.status = 1";
            
            $statement = $entityManagerInterface->getConnection()->prepare($sql);
            $statement->bindValue('id', $value['fieldId']);
            $field = $statement->executeQuery()->fetchAssociative();
            //  dd($field);
            $contact = $contactsRepository->find($contactid);
            if($field['field_name'] == 'First Name'){
                $contact->firstname = $value['value'];
                $entityManagerInterface->persist($contact);
                $entityManagerInterface->flush();
            }else if($field['field_name'] == 'Last Name'){
                $contact->lastname = $value['value'];
                $entityManagerInterface->persist($contact);
                $entityManagerInterface->flush();
            }else if($field['field_name'] == 'E-mail'){
                $contact->email = $value['value'];
                $entityManagerInterface->persist($contact);
                $entityManagerInterface->flush();
            }else if($field['field_name'] == 'Phone'){
                $contact->phone = $value['value'];
                $entityManagerInterface->persist($contact);
                $entityManagerInterface->flush();
            }else if($field['field_name'] == 'Country'){
                $contact->country = $value['value'];
                $entityManagerInterface->persist($contact);
                $entityManagerInterface->flush();
            }else if($field['field_name'] == 'Birth date'){ 
                $dateOfBirth = \DateTimeImmutable::createFromFormat('Y-m-d', $value['value']);

            if ($dateOfBirth === false) {
                // Handle invalid date format if necessary
                throw new \InvalidArgumentException('Invalid date format provided.');
            }
                $contact->date_birth = $dateOfBirth;
                $entityManagerInterface->persist($contact);
                $entityManagerInterface->flush();
            }else if($field['field_name'] == 'Name'){
                $contact->name = $value['value'];
                $entityManagerInterface->persist($contact);
                $entityManagerInterface->flush();
            }else{

            }

        }

        return new JsonResponse([
            'success' => 'true',
            'data' => $ContactCustomFields
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
