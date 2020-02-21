<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function update($id, EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $pwdEncoder)
    {
        $user = $em->getRepository(User::class)->find($id);
        $users = $em->getRepository(User::class)->findAll();
        $userUpdateForm = $this->createForm(UserType::class, $user);
        $userUpdateForm->handleRequest($request);
        if ($userUpdateForm->isSubmitted() && $userUpdateForm->isValid()) {

            $pseudo = $userUpdateForm->get('pseudo')->getData();
            if ($pseudo){
                foreach ($users as $utilisateur){
                    $pseudoExist = $utilisateur->getPseudo();
                    if ($pseudo == $pseudoExist){
                        $this->addFlash('warning', "Ce pseudo est déjà pris, veuillez en choisir un autre");
                        return $this->redirectToRoute('userupdate', ['id'=>$id]);
                    }
                }
            }

            // récupération d'un nouveau mot de passe s'il y en a un de passé dans le champ "nouveau mdp"
            $password = $userUpdateForm->get('password')->getData();
            // S'il y a un nouveau mdp, on le hash et le charge en bdd
            if ($password) {
//                dump($user->getPassword());
//                dd($userUpdateForm->get('password')->getData());
                $hash = $pwdEncoder->encodePassword($user, $password);
                $user->setPassword($hash);

//            $em->flush();
            }
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "Votre profil a été modifié");
            return $this->redirectToRoute('sortie_list');
        }

        return $this->render('user/updateprofile.html.twig', [
            "userUpdateForm" => $userUpdateForm->createView()
        ]);
    }

    /**
     * @Route("/profile/{id}", name="profile")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function detail($id, EntityManagerInterface $em)
    {
        $user = $em->getRepository(User::class)->find($id);

        return $this->render('user/profile.html.twig', [
            "user" => $user
        ]);
    }

}
