<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie", name="sortie")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/detail/{id}", name="detail")
     */
    public function detail($id, EntityManagerInterface $em)
    {
        $sortie = $em->getRepository(Sortie::class)->find($id);

        return $this->render('sortie/detailSortie.html.twig', [
            "sortie" => $sortie,
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
    public function partyList(EntityManagerInterface $em, Request $request)
    {
        $sorties = $em->getRepository(Sortie::class)->findAll();
        dump($sorties);
        $sites = $em->getRepository(Site::class)->findAll();

        // Récupération des critères de recherche

        $parametres = [
            'site' => $request->get('site'),
            'search' => $request->get('search'),
            'dateDebut' => $request->get('dateDebut'),
            'dateFin' => $request->get('dateFin'),
            'organisateur' => $request->get('organisateur'),
            'inscrit' => $request->get('inscrit'),
            'nonInscrit' => $request->get('nonInscrit'),
            'sortiesPassees' => $request->get('sortiesPassees'),
        ];
        dump($parametres);

        if ($parametres){
            $sortiess = $em->getRepository(Sortie::class)->afficher($parametres);
            dump($sorties);

            return $this->render('sortie/listSorties.html.twig',
                [
                   "sorties" => $sorties,
                   "sites" => $sites
                ]);
        }

//        $sites = $em->getRepository(Site::class)->afficher(tableau);
//
//        if($site)
//        {
//
//            $sorties = $em->getRepository(Sortie::class)->findAll();
//
//
////
//        }
//
//        if ($search)
//        {
//            $sorties = $em->getRepository(Sortie::class)->search($search);
//        }

        return $this->render(
            "sortie/listSorties.html.twig",
            [
                "sorties" => $sorties,
                "sites" => $sites
            ]
        );
    }
}
