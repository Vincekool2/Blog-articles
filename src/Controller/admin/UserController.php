<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    #[Route('/admin/users/insert', name: 'insert_admin')]
    public function insertAdmin ( Request $request , EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        if ($request->getMethod() === "POST") {

            $user= new User();

            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $role = $request->request->get('role');


            try {
                $hashedPassword = $userPasswordHasher->hashPassword(
                    $user,
                    $password
                );
                $user->setPassword($hashedPassword);
                $user->setEmail($email);
                $user->setRoles([$role]);

                $entityManager->persist($user);
                $entityManager->flush();


                $this->addFlash('success', 'user created !');

            } catch (\Exception $exception) {
                return $this->render('admin/error.html.twig', ['errorMessage' => $exception->getMessage()]);
            }
        }
        return $this->render('user/insert_user.html.twig');
    }
}