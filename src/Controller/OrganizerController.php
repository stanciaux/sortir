<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\OrganizerType;
use App\Entity\Etat;
use App\Entity\Site;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class OrganizerController extends AbstractController
{
    /**
     * @Route("/organizer", name="organizer")
     */
    public function newParty(Request $request, EntityManagerInterface $em)
    {

        // On crée 2 objets : sortie et lieu
        $sortie = new Sortie();
        $lieu = new Lieu();
        // On crée les formulaire grâce à OrganizerType et LieuType
        // Et on récupère les requêtes avec le handleRequest
        $formLieu = $this->createForm(LieuType::class, $lieu);
        $formLieu->handleRequest($request);
        $form = $this->createForm(OrganizerType::class, $sortie);
        $form->handleRequest($request);

        //On récupére toute la liste de la class Ville
        $listVille = $em->getRepository(Ville::class)->findAll();

        // On vérifie que les valeurs entrées sont correctes
        if ($formLieu->isSubmitted() && $formLieu->isValid()) {
            $lieu = $formLieu->getData();
            $sortie = $form->getData();
            $formResend = $this->createForm(OrganizerType::class, $sortie);
            $formResend->handleRequest($request);

            // On enregistre notre objet $lieu dans la base de données
            $em->persist($lieu);
            $em->flush();
            $this->addFlash('success', 'Le lieu a été ajouté !');

        }

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie = $form->getData();

            $dateSortie = $form['dateSortie']->getData();
            $sortie->setDatedebut(\DateTime::createFromFormat('Y/m/d H:i', $dateSortie));

            $dateCloture = $form['dateCloture']->getData();
            $sortie->setDatecloture(\DateTime::createFromFormat('Y/m/d', $dateCloture));

            if ($form->get('save')->isClicked()) {
                $sortie->setEtatSortie("créée");
            } elseif ($form->get('publish')->isClicked()) {
                $sortie->setEtatSortie("ouvert");
            } else {
                //TODO changer la route 'organizer' par la page affichant la récap de la saisie
                return $this->redirectToRoute('organizer');
            }

            $sortie->setOrganisateur($this->getUser());

            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', 'La sortie a été ajoutée !');
            //TODO changer la route 'organizer' par la page affichant la récap de la saisie
            return $this->redirectToRoute('organizer');
        }


        return $this->render('organizer/index.html.twig', [
            'page_name' => 'Ajouter une sortie',
            'form' => $form->createView(),
            'formLieu' => $formLieu->createView(),
            'listVille' => $listVille
        ]);

    }

    /**
     * @Route("/cancelorg/{id}", name="cancelorg")
     */
    public function cancel($id, EntityManagerInterface $em)
    {
       $sorties = $em->getRepository(Sortie::class)->findAll();
       $sites = $em->getRepository(Site::class)->findAll();

       $sortie = $em->getRepository(Sortie::class)->find($id);
       $organisateur = $sortie->getOrganisateur();
       $orgId = $organisateur->getId();
       $user = $this->getUser();
       $userId = $user->getId();
       $etatAnnule = $em->getRepository(Etat::class)->find(6);
       $dateJour = new \DateTime();

       if ($sortie->getEtat()->getId() == 2
           and $orgId == $userId
           and $dateJour <= $sortie->getDateSortie() )
       {
           $sortie->setEtat($etatAnnule);
           $em->flush();

           $this->addFlash('success', "Sortie annulée");
           return $this->redirectToRoute('sortiesortieslist',
               [
                   "sorties" => $sorties,
                   "sites" => $sites
               ]);
       }

        return $this->render('sortie/listSorties.html.twig',
           [
               "sorties" => $sorties,
               "sites" => $sites
           ]);

    }

}
