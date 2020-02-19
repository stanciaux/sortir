<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PhotoType;
use App\Form\UserType;
use App\Service\FileUploader;
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

    /**
     * @Route("/update_Photo", name="update_Photo")
     */
    public function updateAvatar(EntityManagerInterface $em, Request $request, FileUploader $fileUploader)
    {
        $user = $this->getUser();
        $avatarForm = $this->createForm(PhotoType::class,$user);
        $avatarForm->handleRequest($request);

        if ($avatarForm->isSubmitted() && $avatarForm->isValid())
        {
            // on place l'URL du fichier upload dans une variable $file
            $file = $user->getPhoto();

            // On renomme le fichier dans un langage utilisable et
            // on upload le fichier dans public/profile_directory,
            // grace au App/Service/FileUploader; puis on l'attribue à une variable.
            // (Sans ça l'URL du fichier sera écrite en BDD dans un répértoire Wamp non accessible)
            $fileName = $fileUploader->upload($file);

            // On remplis l'user avec la variable
            $user->setPhoto($fileName);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Photo modifiée avec succès.');
            return $this->render('home', [
                'user' => $user
            ]);
        }

        return $this->render('user/updateprofile.html.twig', [
            'avatarForm' => $avatarForm->createView()
        ]);
    }

}
