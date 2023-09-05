<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GetcustomfieldsNameController extends AbstractController
{

    #[Route('/getcustomfieldsname')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
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
        $user = $tokenData->getUser();

        $sql3 = "SELECT c.id , c.field_name FROM `custom_fields` as c WHERE c.account_id = :id and c.status = 1";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $user->accountId);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }



    #[Route('/getplanuserdatabyid/{id}')]
    public function getplanuserid($id, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $sql3 = "SELECT pu.user_id FROM `plan_users` as pu WHERE pu.plan_id = :id and pu.status = 1";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $id);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }

    #[Route('/getlinkuserid/{id}/users/{user_id}')]
    public function getlinkuserid($id, $user_id, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $sql3 = "SELECT * FROM `clickable_links_users` as cl WHERE cl.link_id = :id and cl.user_id = :user_id and cl.status = 1";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $id);
        $statement3->bindValue('user_id', $user_id);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }

    #[Route('/getcustomfieldData/{id}/field/{field_id}')]
    public function getcustomfieldData($id, $field_id, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $sql3 = "SELECT * FROM `contact_form_fields` as c WHERE c.form_id = :id and c.field_id = :field_id and c.status = 1;";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $id);
        $statement3->bindValue('field_id', $field_id);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }



    #[Route('/getcustomfieldLandingpageData/{id}/field/{field_id}')]
    public function getcustomfieldLandingpageData($id, $field_id, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $sql3 = "SELECT * FROM `landing_page_fields` as l WHERE l.page_id = :id and l.field_id = :field_id and l.status = 1;";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $id);
        $statement3->bindValue('field_id', $field_id);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }

    #[Route('/gettextuserid/{id}/users/{user_id}')]
    public function gettextuserid($id, $user_id, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $sql3 = "SELECT * FROM `predefined_text_users` as t WHERE t.text_id = :id and t.user_id = :user_id and t.status = 1";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $id);
        $statement3->bindValue('user_id', $user_id);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }

    #[Route('/getplansuserid/{id}/users/{user_id}')]
    public function getplansuserid($id, $user_id, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $sql3 = "SELECT * FROM `plan_users` as p WHERE p.plan_id = :id and p.user_id = :user_id and p.status = 1;";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $id);
        $statement3->bindValue('user_id', $user_id);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }

    #[Route('/getplansdiscountuserid/{id}/users/{user_id}')]
    public function getplansdiscountuserid($id, $user_id, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $sql3 = "SELECT * FROM `plan_discount_users` as d WHERE d.discount_id = :id and d.user_id = :user_id and d.status = 1;";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('id', $id);
        $statement3->bindValue('user_id', $user_id);
        $results3 = $statement3->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'status' => true,
            'data' => $results3,
        ]);
    }
}
