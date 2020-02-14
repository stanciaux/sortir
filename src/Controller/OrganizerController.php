<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\OrganizerType;
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
//        // On crée un objet party
//        $sortie = new Sortie();
//        // On crée le formulaire grâce à l'OrganizerType
//        $form = $this->createForm(OrganizerType::class, $sortie);
//        // On récupère la requête
//        $form->handleRequest($request);
//        // On vérifie qu'elle est de type POST
//        if ($request->getMethod() == 'POST') {
//            // On vérifie que les valeurs entrées sont correctes
//            if ($form->isValid()) {
//                // On enregistre notre objet $sortie dans la base de données
//                $em = $this->getDoctrine()->getManager();
//                $em->persist($sortie);
//                $em->flush();
//                // On définit un message flash
//                $this->get('session')->getFlashBag()->add('info', 'Nouvelle sortie bien ajoutée');
//                // On redirige vers la page de visualisation de la sortie nouvellement créée
//                //TODO changer la route 'organizer' par la page affichant la récap de la saisie
//                return $this->redirect($this->generateUrl('organizer', array('id' => $sortie->getId())));
//            }
//        }
//        return $this->render('organizer/index.html.twig', array(
//            'form' => $form->createView(),
//        ));
//
//    }

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
}
