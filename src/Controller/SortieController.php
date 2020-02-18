<?php

namespace App\Controller;

use App\Entity\Etat;
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
 * @Route("/sortie", name="sortie_")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function partyList(EntityManagerInterface $em)
    {
        $sorties = $em->getRepository(Sortie::class)->findIfNotArchived();
        $sites = $em->getRepository(Site::class)->findAll();
        $dateDuJour = new \DateTime();

        return $this->render(
            "sortie/listSorties.html.twig",
            [
                "sorties" => $sorties,
                "sites" => $sites,
                "dateJour" => $dateDuJour
            ]
        );
    }

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
     * @Route("/subscribe/{id}", name="subscribe", methods={"POST"})
     *
     */
    public function subscribe($id, EntityManagerInterface $em)
    {
        $dateDuJour = new \DateTime();
        $inscription = new Inscription();

        $sortie = $em->getRepository(Sortie::class)->find($id);
        $user = $this->getUser();
        $userId = $user->getId();

//        Si la sortie est ouverte, que le nb max d'inscriptions n'est pas atteint,
//          que je ne suis pas déjà inscrit,
//          et que la date de cloture des inscriptions n'est pas dépassée :
        if (
            $sortie->getEtat()->getLibelle() == Etat::OUVERTE &&
            $sortie->getInscriptions()->count() < $sortie->getNbInscriptionsMax() &&
            !$sortie->getInscriptions()->contains($userId) &&
            $sortie->getDateCloture() > $dateDuJour
        ) {
            $inscription->setSortie($sortie)
                ->setParticipant($user)
                ->setDateInscription(new \DateTime());
            $em->persist($inscription);
            $em->flush();

            $this->addFlash('success', "Inscription validée");
            return $this->redirectToRoute('sortie_list');
        }

        if ($sortie->getEtat()->getLibelle() == Etat::OUVERTE &&
            $sortie->getInscriptions()->count() == $sortie->getNbInscriptionsMax()
        ) {
            $this->addFlash('warning', "Le nombre maximal de participants est atteint");
        } elseif ($sortie->getEtat()->getLibelle() == Etat::OUVERTE &&
            $sortie->getInscriptions()->contains($user)
        ) {
            $this->addFlash('warning', "Vous êtes déjà inscrit à cette sortie");
        }

        return $this->redirectToRoute('sortie_list');
    }

    /**
     * @Route("/unsubscribe/{id}", name="unsubscribe", methods={"POST"})
     */
    public function unsubscribe($id, EntityManagerInterface $em)
    {
        $sortie = $em->getRepository(Sortie::class)->find($id);
        $user = $this->getUser();
        $userId = $user->getId();

        $inscription = $em->getRepository(Inscription::class)
            ->findOneBy(["sortie" => $id,
                "participant" => $userId]);

        if (
            $sortie->getEtat()->getLibelle() == Etat::OUVERTE ||
            $sortie->getEtat()->getLibelle() == Etat::CLOTUREE
        ) {
            $sortie->removeInscription($inscription);
            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', "Inscription annulée");
        }

        return $this->redirectToRoute('sortie_list');    }

}
