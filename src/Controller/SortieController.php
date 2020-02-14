<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Site;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

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

    /**
     * @Route("/subscribe/{id}", name="subscribe")
     *
     */
    public function subscribe($id, EntityManagerInterface $em)
    {
        $sorties = $em->getRepository(Sortie::class)->findAll();
        $sites = $em->getRepository(Site::class)->findAll();

        $inscription = new Inscription();

        $sortie = $em->getRepository(Sortie::class)->find($id);
        $user = $this->getUser();
        $userId = $user->getId();

        if ($sortie->getEtat()->getId() == 2
            and $sortie->getInscriptions()->count() < $sortie->getNbInscriptionsMax()
            and !$sortie->getInscriptions()->contains($userId)
        ){
            $inscription->setSortie($sortie)
                        ->setParticipant($user)
                        ->setDateInscription(new \DateTime());
            $em->persist($inscription);
            $em->flush();

            $this->addFlash('success', "Inscription validée");
            return $this->redirectToRoute('sortiesortieslist',
                [
                    "sorties" => $sorties,
                    "sites" => $sites
                ]
                );
        }

//        if ($sortie->getEtat()->getId() == 2
//            and $sortie->getParticipants()->count() == $sortie->getNbInscriptionsMax()
//        ){
//            $this->addFlash('warning', "Le nombre maximal de participants est atteint");
//            return $this->redirectToRoute('sortiesortieslist');
//        }
//
//        if ($sortie->getEtat()->getId() == 2
//            and $sortie->getParticipants()->contains($userId)
//        ){
//            $this->addFlash('warning', "Vous êtes déjà inscrit à cette sortie");
//            return $this->redirectToRoute('sortiesortieslist');
//        }

        return $this->render('sortie/listSorties.html.twig',
            [
               "sorties" => $sorties,
               "sites" => $sites
            ]);
    }

    /**
     * @Route("/sortieslist", name="sortieslist")
     */
    public function partyList(EntityManagerInterface $em)
    {
        $sorties = $em->getRepository(Sortie::class)->findAll();
        $sites = $em->getRepository(Site::class)->findAll();

        return $this->render(
            "sortie/listSorties.html.twig",
            [
                "sorties" => $sorties,
                "sites" => $sites
            ]
        );
    }
}
