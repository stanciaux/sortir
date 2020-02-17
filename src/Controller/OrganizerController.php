<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\OrganizerType;
use App\Entity\Site;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\SortieType;
use Doctrine\ORM\Query\AST\Functions\DateDiffFunction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
            $sortie->setDateSortie(\DateTime::createFromFormat('Y/m/d H:i', $dateSortie));

            $dateCloture = $form['dateCloture']->getData();
            $sortie->setDateCloture(\DateTime::createFromFormat('Y/m/d', $dateCloture));

            $etatCree = $em->getRepository(Etat::class)->find(1);
            $etatOuver = $em->getRepository(Etat::class)->find(1);
            if ($form->get('Enregistrer')->isClicked()) {
                $sortie->setEtat($etatCree);
            } elseif ($form->get('publish')->isClicked()) {
                $sortie->setEtat($etatOuver);
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


        return $this->render('organizer/index.html.twig'
            , [
            'page_name' => 'Ajouter une sortie',
            'form' => $form->createView(),
            'formLieu' => $formLieu->createView(),
            'listVille' => $listVille

        ]);

    }

    /**
     * @Route("/updateParty/{id}", name="updateParty")
     */
    public function updateParty(Sortie $sortie, Request $request, EntityManagerInterface $em)
    {
//        $form = $this->createForm(SortieType::class, $sortie);
//        $form->handleRequest($request);
//
//        $etatSortie = $em->getRepository(Etat::class)->find(1);
//        if($form->isSubmitted() && $form->isValid()){
//            $sortie = $form->getData();
//
//            if( $form->get('save')->isClicked()){
//                $sortie->setEtatSortie("En création");
//            }elseif( $form->get('publish')->isClicked()){
//                $sortie->setEtatSortie("Ouvert");
//            }else{
//                return $this->redirectToRoute('sorties');
//            }
//
//            $em->persist($sortie);
//            $em->flush();
//            $this->addFlash('success', 'La sortie a été modifiée !');
//
//            $this->sortiesListe = $em->getRepository(Sortie::class)->findAll();
//
//            return $this->redirectToRoute('sortieslist');
//        }
//
//        return $this->render('sortie/listSorties.html.twig', [
//            'page_name' => 'Sortie mise à jour',
//            'sortie' => $sortie,
//            'form' => $form->createView()
//        ]);
    }

    /**
     * @Route("/archiveParty/{id}", name="archive_party")
     * @IsGranted("ROLE_ADMIN")
     */
    public function archive($id, EntityManagerInterface $em)
    {
        $etatArchive = $em->getRepository(Etat::class)->find(7);
        $sortieAarchiver = $em->getRepository(Sortie::class)->find($id);
        $dateSortie = $sortieAarchiver->getDateSortie();
        $dateJour = new \DateTime();
        $interval = $dateJour->diff($dateSortie);

        if ($interval->days > 30)
        {
            $sortieAarchiver->setEtat($etatArchive);
            $em->flush();
            $this->addFlash('success', 'La sortie a été archivée');
            return $this->redirectToRoute('sortie_list');
        }
        else
        {
            $this->addFlash('warning', "Le délai d'archivage n'est pas respecté");
            return $this->redirectToRoute('sortie_list');
        }

        return $this->render('sortie_list');
    }

    /**
     * @Route("/publishParty/{id}", name="publish_party")
     */
    public function publish($id, EntityManagerInterface $em)
    {
        $etatCree = $em->getRepository(Etat::class)->find(1);
        $etatOuvert = $em->getRepository(Etat::class)->find(2);
        $sortieAouvrir = $em->getRepository(Sortie::class)->find($id);

        if ($sortieAouvrir->getEtat() == $etatCree)
        {
            $sortieAouvrir->setEtat($etatOuvert);
            $em->flush();
            $this->addFlash('success', "La sortie est désormais ouverte aux inscriptions");
            return $this->redirectToRoute('sortie_list');
        }
        else {
            $this->addFlash('warning', "Cette sortie est déjà ouverte");
            return $this->redirectToRoute('sortie_list');
        }

        return $this->render('sortie_list');
    }

    /**
     * @Route("/cancelParty/{id}", name="cancel_party")
     */
    public function cancel($id, EntityManagerInterface $em, Request $request)
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
        $formAnnulation = $this->createForm(SortieType::class, $sortie);
        $formAnnulation->handleRequest($request);

        if ($formAnnulation->isSubmitted() && $formAnnulation->isValid()
            and $sortie->getEtat()->getId() == 2
            and $orgId == $userId
            and $dateJour <= $sortie->getDateSortie() )
        {
            $sortie->setEtat($etatAnnule);
            $em->flush();

            $this->addFlash('success', "Sortie annulée");
            return $this->redirectToRoute('sortie_list',
                [
                    "sorties" => $sorties,
                    "sites" => $sites
                ]);
        }

        return $this->render('sortie/cancelParty.html.twig',
            [
                "sortie" => $sortie,
                "formAnnulation" => $formAnnulation->createView()
            ]);
    }

    /**
     * @Route("/deleteParty/{id}", name="delete_party")
     */
    public function delete($id, EntityManagerInterface $em)
    {
        $sortieAsupprimer = $em->getRepository(Sortie::class)->find($id);
        $etatCree = $em->getRepository(Etat::class)->find(1);

        if ($sortieAsupprimer->getEtat() == $etatCree)
        {
            $em->remove($sortieAsupprimer);
            $em->flush();
            $this->addFlash('success', "La sortie a été supprimée");
            return $this->redirectToRoute('sortie_list');
        }
        else{
            $this->addFlash('warning', "Cette sortie n'existe pas");
            return $this->redirectToRoute('sortie_list');
        }
        return $this->render('sortie_list');
    }

}
