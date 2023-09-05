<?php

namespace App\Controller;

use App\Entity\Profiles;
use App\Entity\User;
use App\Entity\UserLogs;
use App\Repository\ProfilesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
#[AsController]
class UpdateUserPasswordController extends AbstractController
{
    /**
    * @var UserRepository
    * @var ProfilesRepository
    */
    private $userRepository;
    private $profilesRepository;
    public function __construct(UserRepository $userRepository, ProfilesRepository $profilesRepository)
    {
        $this->userRepository = $userRepository;
        $this->profilesRepository = $profilesRepository;
    }



    public function __invoke(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManagerInterface): User
    {
        $data = json_decode($request->getContent(), true);
        $profile = $this->profilesRepository->findProfileById($data['iduser']);
 
        $user = $this->userRepository->findOneById($data['iduser']);
        //   $user = new User();
        // dd($profile);
        $user->password = $userPasswordHasher->hashPassword($user,$data['password']);
       
        //$user->password = $userPasswordHasher->hashPassword($user,$request->get('password'));
        // $plainPassword = $request->get('password');
        // $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
        // $user->setPassword($hashedPassword);

       
        $entityManagerInterface->persist($user);
        $entityManagerInterface->flush();
        $time =  new \DateTimeImmutable();

        $UserLogs = new UserLogs();
        $UserLogs->user_id = $user->id;
        $UserLogs->action = 'Update Password User';
        $UserLogs->element = '2';
        $UserLogs->element_id = $user->id;
        $UserLogs->log_date = $time;
        $UserLogs->source = '1';
        $entityManagerInterface->persist($UserLogs);
        $entityManagerInterface->flush();

        // $sql3= "SELECT * FROM `profiles` as p WHERE p.u_id = :id and p.u_type = 1;";
        // $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        // $statement3->bindValue('id', $data['iduser']);
        // $profile = $statement3->executeQuery()->fetchAllAssociative();

       // $profile->password = $userPasswordHasher->hashPassword($profile,$data['password']);

        $profiles = new Profiles();
        $profiles->accountId = $profile->accountId;
        $profiles->username = $profile->username;
        $profiles->password = $userPasswordHasher->hashPassword($profiles,$data['password']);
        $profiles->login = $profile->login;
        $profiles->u_id = $profile->u_id;
        $profiles->u_type = '1';
        $entityManagerInterface->persist($profiles);
        $entityManagerInterface->flush();
        
        $UserLogs = new UserLogs();
        $UserLogs->user_id = $user->id;
        $UserLogs->action = 'Update Password Profile';
        $UserLogs->element = '30';
        $UserLogs->element_id = $profiles->id;
        $UserLogs->log_date = $time;
        $UserLogs->source = '1';
        $entityManagerInterface->persist($UserLogs);
        $entityManagerInterface->flush();

        return new JsonResponse([
            'success' => true,
            'data' => $profiles
        ]);
       
  

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
