<?php

namespace App\Controller\public;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
class UserController extends AbstractController
{
    #[Route('/register', name: 'register_user')]
    public function registerUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        if ($request->getMethod() === "POST") {

            $user = new User();

            $email = $request->request->get('email');
            $password = $request->request->get('password');

            if (!$email || !$password) {
                $this->addFlash('error', 'Please fill in all fields');
                return $this->render('user/register.html.twig');
            }

            try {
                $hashedPassword = $userPasswordHasher->hashPassword(
                    $user,
                    $password
                );
                $user->setPassword($hashedPassword);
                $user->setEmail($email);
                $user->setRoles(['ROLE_USER']); // Default role for public users

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Account created successfully!');

            } catch (\Exception $exception) {
                return $this->render('error.html.twig', ['errorMessage' => $exception->getMessage()]);
            }
        }

        return $this->render('user/register.html.twig');
    }
}