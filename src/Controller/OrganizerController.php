<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\OrganizerType;
use App\Form\SortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
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
        ]
        );
    }


//    /**
//     * @Route("/view_party", name="view_party")
//     * afficher détail dans la page twig
//     */
//    public function listParty(EntityManagerInterface $em)
//    {
//
//        $species = $this->getDoctrine()
//            ->getRepository(Sortie::class)
//            ->findAll();
//
//        return $this->render("specie/list.html.twig",
//            [
//                'sorties' => $sorties,
//            ]);
//
//    }
//    public function index()
//    {
//        return $this->render('organizer/index.html.twig', [
//            'controller_name' => 'OrganizerController',
//        ]);
//    }

    /**
     * @Route("/removeParty/{id}", name="removeParty")
     */
    public function removeParty(Request $request, EntityManagerInterface $em, Sortie $sortie)
    {

        $user = $this->getUser();

        $form = $this->createForm(RemovePartyType::class, $sortie);
        $form->handleRequest($request);

        $etatAnnule = $em->getRepository(Etat::class)->find(1);
        if ($form->isSubmitted() && $form->isValid()) {

            $sortie->setDescriptioninfos($form['descriptionInfos']->getData());
            $sortie->setEtat("Annulé");

            $em->flush();
            $this->addFlash('success', 'La sortie a été annulée !');

            $this->partyList = $em->getRepository(Sortie::class)->findAll();

            return $this->redirectToRoute('sortieslist');

        }
    }

    /**
     * @Route("/updateParty/{id}", name="updateParty")
     */
    public function updateParty(Sortie $sortie, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        $etatSortie = $em->getRepository(Etat::class)->find(1);
        if($form->isSubmitted() && $form->isValid()){
            $sortie = $form->getData();

            if( $form->get('save')->isClicked()){
                $sortie->setEtatSortie("En création");
            }elseif( $form->get('publish')->isClicked()){
                $sortie->setEtatSortie("Ouvert");
            }else{
                return $this->redirectToRoute('sorties');
            }

            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', 'La sortie a été modifiée !');

            $this->sortiesListe = $em->getRepository(Sortie::class)->findAll();

            return $this->redirectToRoute('sortieslist');
        }

        return $this->render('sortie/listSorties.html.twig', [
            'page_name' => 'Sortie mise à jour',
            'sortie' => $sortie,
            'form' => $form->createView()
        ]);
    }

}
