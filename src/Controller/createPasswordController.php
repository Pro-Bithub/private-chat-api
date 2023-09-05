<?php
namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class createPasswordController extends AbstractController
{
    /**
     * @Route("/hashAction", name="app_hashAction_controller", methods={"POST"})
     */
    public function hashAction(Request $request, UserPasswordHasherInterface $userPasswordHasher)
    {
        // Controller logic to hash the password
        
        $user = new User();
        $plainPassword = $request->get('password');
        $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);

        return new JsonResponse([
            'success' => true,
            'password' => $hashedPassword,
        ]);
    }
}
