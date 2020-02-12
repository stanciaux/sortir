<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user", name="user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/update/{id}", name="update")
     */
    public function update($id, EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface  $pwdEncoder)
    {
        $user = $em->getRepository(User::class)->find($id);
        $userUpdateForm = $this->createForm(UserType::class, $user);
        $userUpdateForm->handleRequest($request);
        if ($userUpdateForm->isSubmitted() && $userUpdateForm->isValid())
        {
            if ($user->getPseudo())
            $hash = $pwdEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $em->flush();

            $this->addFlash('success', "Votre profil a été modifié");
            return $this->redirectToRoute('home');
        }

        return $this->render('user/updateprofile.html.twig', [
            "userUpdateForm" => $userUpdateForm->createView()
        ]);
    }

    /**
     * @Route("/profile/{id}", name="profile")
     */
    public function detail($id, EntityManagerInterface $em)
    {
        $user = $em->getRepository(User::class)->find($id);

        return $this->render('user/profile.html.twig', [
            "user" => $user
        ]);
    }

}