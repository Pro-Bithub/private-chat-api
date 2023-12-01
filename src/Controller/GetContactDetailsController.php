<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetContactDetailsController extends AbstractController
{
    protected $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    #[Route('/getProfileByContactId/{id}', name: 'app_get_Profile_By_ContactId_details')]
    public function getProfileByContactId(EntityManagerInterface $entityManagerInterface, $id): Response
    {
        $sql = "SELECT p.*
    FROM `contacts` AS c
    LEFT JOIN `profiles` AS p ON p.u_id = c.id
    WHERE c.id = :id and c.status = 1";

        $statement = $entityManagerInterface->getConnection()->prepare($sql);
        $statement->bindValue('id', $id);
        $profiles = $statement->executeQuery()->fetchAssociative();


        return new JsonResponse([
            'success' => true,
            'data' => $profiles,
        ]);
    }

    #[Route('/getContactByProfileId/{id}', name: 'app_get_Contact_By_Profile_Id_details')]
    public function getContactByProfileId(EntityManagerInterface $entityManagerInterface, $id): Response
    {
        $sql = "SELECT c.* , l.source
    FROM `contacts` AS c
    LEFT JOIN `profiles` AS p ON p.u_id = c.id
    LEFT JOIN `user_logs` AS l on l.element_id = c.id and l.element = 27
    WHERE c.id = :id and c.status = 1";

        $statement = $entityManagerInterface->getConnection()->prepare($sql);
        $statement->bindValue('id', $id);
        $profiles = $statement->executeQuery()->fetchAssociative();



        return new JsonResponse([
            'success' => true,
            'data' => $profiles,

        ]);
    }


    #[Route('/getContactInfoByProfileId/{id}', name: 'app_get_Contact_info_By_Profile_Id_details')]
    public function getContactInfoByProfileId($id, EntityManagerInterface $entityManagerInterface): Response
    {
        $sql = "SELECT c.id as contact_id, p.id as profil_id, c.country ,c.firstname , c.lastname
                    FROM `contacts` AS c
                    LEFT JOIN `profiles` AS p ON p.u_id = c.id and p.u_type=2
                    WHERE p.id  = :id and c.status = 1  ";
        $statement = $entityManagerInterface->getConnection()->prepare($sql);
        $statement->bindValue('id', $id);
        $profiles = $statement->executeQuery()->fetchAssociative();
        return new JsonResponse([
            'success' => true,
            'data' => $profiles,
        ]);
    }


    #[Route('/getContactsInfoByProfileId', name: 'app_get_Contacts_info_By_Profile_Id_details')]
    public function getContactsInfoByProfileId(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {

        $data = json_decode($request->getContent(), true);
        $ids = $data;
        $idsinsql = "";
        foreach ($ids as $key => $id) {
            if ($key > 0) {
                $idsinsql .= ", ";
            }
            $idsinsql .= $id;
        }

        $sql = "SELECT c.id as contact_id, p.id as profil_id, c.country ,c.firstname , c.lastname
                    FROM `contacts` AS c
                    LEFT JOIN `profiles` AS p ON p.u_id = c.id and p.u_type=2
                    WHERE p.id  IN ( " . $idsinsql . " )  and c.status = 1";

        $statement = $entityManagerInterface->getConnection()->prepare($sql);

        $profiles = $statement->executeQuery()->fetchAllAssociative();

        return new JsonResponse([
            'success' => true,
            'data' => $profiles,
        ]);
    }


    #[Route('/getAgentsByPresentationId', name: 'app_get_Agents_info_By_Presentation_Id_details')]
    public function getAgentsByPresentationId(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {

        function addTrailingSlashIfMissing($str)
        {
            if (!in_array(substr($str, -1), ['/', '\\'])) {
                $str .= '/';
            }
            return $str;
        }

        $uploads_directory = addTrailingSlashIfMissing($this->parameterBag->get('APP_URL'))."uploads/";

        $data = json_decode($request->getContent(), true);
        $ids = $data;
        $idsinsql = "";
        foreach ($ids as $key => $id) {
            if ($key > 0) {
                $idsinsql .= ", ";
            }
            $idsinsql .= $id;
        }

        $sql = "SELECT   CASE
        WHEN  up.picture  is not null
          THEN  concat( '$uploads_directory' , up.picture )  
            ELSE null
       END as avatar,  up.id as profile_id,u.id as id , up.nickname as nickname, u.firstname ,u.lastname
                    FROM `user_presentations` AS up
                    LEFT JOIN `user` AS u ON u.id = up.user_id 
                    WHERE up.id  IN ( " . $idsinsql . " ) ";

        $statement = $entityManagerInterface->getConnection()->prepare($sql);

        $profiles = $statement->executeQuery()->fetchAllAssociative();

        return new JsonResponse([
            'success' => true,
            'data' => $profiles,
        ]);
    }

    
    #[Route('/getAgentByPresentationId/{id}', name: 'app_get_Agent_By_Presentation_Id_details')]
    public function getAgentByPresentationId($id, EntityManagerInterface $entityManagerInterface): Response
    {
        function addTrailingSlashIfMissing2($str)
        {
            if (!in_array(substr($str, -1), ['/', '\\'])) {
                $str .= '/';
            }
            return $str;
        }

        $uploads_directory = addTrailingSlashIfMissing2($this->parameterBag->get('APP_URL'))."uploads/";

        $sql = "SELECT   CASE
        WHEN  up.picture  is not null
          THEN  concat( '$uploads_directory' , up.picture )  
            ELSE null
       END as avatar,  up.id as profile_id,u.id as id , up.nickname as nickname, u.firstname ,u.lastname
                    FROM `user_presentations` AS up
                    LEFT JOIN `user` AS u ON u.id = up.user_id 
                    WHERE up.id  = :id ";

        $statement = $entityManagerInterface->getConnection()->prepare($sql);
        $statement->bindValue('id', $id);
        $profiles = $statement->executeQuery()->fetchAssociative();
        return new JsonResponse([
            'success' => true,
            'data' => $profiles,
        ]);
    }

    #[Route('/getContactInfoForMonitoring/{profile_id}', name: 'app_getContactInfoForMonitoring')]
    public function getContactInfoForMonitoring(EntityManagerInterface $entityManagerInterface, $profile_id): Response
    {
        
        $sql = "SELECT c.id as contact_id , c.gender,SUBSTRING_INDEX(GROUP_CONCAT(uagents.firstname ORDER BY s.id desc), ',', 1) as agents_firstname , SUBSTRING_INDEX(GROUP_CONCAT(s.date_end ORDER BY s.id desc), ',', 1) as last_payment,  SUBSTRING_INDEX(GROUP_CONCAT(pl.currency ORDER BY s.id desc), ',', 1) as currency, SUBSTRING_INDEX(GROUP_CONCAT(pl.tariff ORDER BY s.id desc), ',', 1) as paid_amount, count(s.id) as purchases_numbe , SUBSTRING_INDEX(GROUP_CONCAT(pl.name ORDER BY s.id desc), ',', 1) as plan_name, count(s.id) as purchases_numbe , p.ip_address , p.browser_data , c.date_start , c.phone , c.email , c.country ,c.status , c.firstname , c.lastname
    FROM `profiles` AS p
    LEFT JOIN `contacts` AS c ON c.id = p.u_id
    LEFT JOIN `sales` AS s ON s.contact_id = c.id and s.status = 1 
    LEFT JOIN `plans` AS pl ON s.plan_id = pl.id 
    LEFT JOIN `profiles` AS pagents ON s.user_id = pagents.id  and pagents.u_type= 1
    LEFT JOIN `user` AS uagents ON pagents.u_id = uagents.id 
    WHERE p.id = :id and p.u_type = 2 
    group by p.id,c.id";

        $statement = $entityManagerInterface->getConnection()->prepare($sql);
        $statement->bindValue('id', $profile_id);
        $profiles = $statement->executeQuery()->fetchAssociative();


        return new JsonResponse([
            'success' => true,
            'data' => $profiles,
        ]);
    }
    




    #[Route('/getProfileAvatarByContactId', name: 'app_get_Profile_avatar_By_ContactId_details')]
    public function getProfileAvatarByContactId(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $data = $request->query->all('items') ?? [];
        $sql = "SELECT up.picture , p.id as profile_id 
        FROM `user_presentations` AS up
        LEFT JOIN `user` AS u ON u.id = up.user_id
        LEFT JOIN `profiles` AS p ON p.u_id = u.id
        WHERE p.id regexp :id and u.status = 1 and  up.status =1";

        $statement = $entityManagerInterface->getConnection()->prepare($sql);

        $statement->bindValue('id', implode('|', $data));
        $profiles = $statement->executeQuery()->fetchAllAssociative();


        return new JsonResponse([
            'success' => true,
            'data' => $profiles,
        ]);
    }

    #[Route('/getProfileByAgentId/{id}', name: 'app_get_Profile_By_AgentId_details')]
    public function getProfileByAgentId(EntityManagerInterface $entityManagerInterface, $id): Response
    {
        $sql = "SELECT up.picture, u.firstname, u.lastname , u.id
    FROM `user_presentations` AS up
    LEFT JOIN `user` AS u ON u.id = up.user_id
    LEFT JOIN `profiles` AS p ON p.u_id = u.id
    WHERE p.id = :id and u.status = 1 and  up.status =1";

        $statement = $entityManagerInterface->getConnection()->prepare($sql);
        $statement->bindValue('id', $id);
        $profiles = $statement->executeQuery()->fetchAssociative();


        return new JsonResponse([
            'success' => true,
            'data' => $profiles,
        ]);
    }
}
