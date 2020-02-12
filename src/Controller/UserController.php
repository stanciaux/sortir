<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user", name="user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/update/{id}", name="update")
     */
    public function update($id, EntityManagerInterface $em)
    {
        $user = $em->getRepository()->find($id);

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
