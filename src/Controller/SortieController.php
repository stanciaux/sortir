<?php

namespace App\Controller;

use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie", name="sortie")
     */
    public function index()
    {
        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }
//    /**
//     * @Route("/home", name="partyListBySite")
//     *
//     * @param EntityManagerInterface $em
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
//     */
//    // Lister les sorties publiées sur chaque site
//    public function partyListBySite(EntityManagerInterface $em, Request $request)
//    {
//        // récupération du site dans le menu déroulant
//        $site = $request->get('site');
//        // Recherche des sorties en fonction du site
//        $sorties = $em->getRepository(Sortie::class)->search($site);
//
//        // Envoi de la liste vers la page concernée.
//        return $this->render(
//            "sortie/liste.html.twig",
//            [
//                "sorties" => $sorties
//            ]
//        );
//    }


    /**
     * @Route("/sortieslist", name="sortieslist")
     */
    public function partyList(EntityManagerInterface $em)
    {
        $sorties = $em->getRepository(Sortie::class)->findAll();

        return $this->render(
            "sortie/listSorties.html.twig",
            [
                "sorties" => $sorties
            ]
        );
    }
}
