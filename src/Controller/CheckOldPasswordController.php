<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CheckOldPasswordController extends AbstractController
{
        /**
    * @var UserRepository
    */
    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    #[Route('/check/old/password')]
    /**
     * Undocumented function
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function check(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManagerInterface)
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
        // $user = $tokenData->getUser();
        $data = json_decode($request->getContent(), true);
        $user = $this->userRepository->findOneById($data['iduser']);
     //   $user = new User();
     return new JsonResponse([
        'success' => 'true',
        'data' => $passwordHasher->isPasswordValid($user, $data['password'])
    ]);
   // return $passwordHasher->isPasswordValid($user, $data['password']);
    //  if (!$passwordHasher->isPasswordValid($user, $data['password'])) {
    //     // error, you can't change your password 
    //     // throw exception or return, etc.
    //     return false;
    //  }else{
    //     return true;
    //  }
        
       // openssl_decrypt($user->password);
       // $user->password = $userPasswordHasher->hashPassword($user,$data['password']);
       
        //$user->password = $userPasswordHasher->hashPassword($user,$request->get('password'));
        // $plainPassword = $request->get('password');
        // $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
        // $user->setPassword($hashedPassword);

       
     //   $entityManagerInterface->persist($user);
      //  $entityManagerInterface->flush();
      

        //return $user;
  

        //dd($_REQUEST['gender']);
       
        // $userPresentations->setGender($request->request->get('gender'));
        // $userPresentations->setWebsite($request->request->get('website'));
        // $userPresentations->setRole($request->request->get('role'));
        // $userPresentations->setNickname($request->request->get('nickname'));
        // $userPresentations->setCountry($request->request->get('country'));
        // $userPresentations->setLanguages($request->request->get('languages'));
        // $userPresentations->setExpertise($request->request->get('expertise'));
        // $userPresentations->setDiploma($request->request->get('diploma'));
        // $userPresentations->setStatus($request->request->get('status'));
        // $userPresentations->setBrandName($request->request->get('brandName'));
        // $userPresentations->setContactPhone($request->request->get('contactPhone'));
        // $userPresentations->setContactPhoneComment($request->request->get('contactPhoneComment'));
        // $userPresentations->setContactMail($request->request->get('contactMail'));
        // $userPresentations->setAtrologicalSign($request->request->get('atrologicalSign'));
        // $userPresentations->setFile($request->files->get('file'));
        // $userPresentations->setDateStart(new \DateTime('@'.strtotime('now')));




        



       
    }
}
