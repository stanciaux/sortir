<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Site;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\OrganizerType;
use Symfony\Component\HttpFoundation\Request;


class OrganizerController extends AbstractController
{
    /**
     * @Route("/organizer", name="organizer")
     */
    public function newParty(Request $request)
    {
        // On crée un objet party
        $sortie = new Sortie();
        // On crée le formulaire grâce à l'OrganizerType
        $form = $this->createForm(OrganizerType::class, $sortie);
        // On récupère la requête
        $form->handleRequest($request);
        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') {
            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()) {
                // On enregistre notre objet $sortie dans la base de données
                $em = $this->getDoctrine()->getManager();
                $em->persist($sortie);
                $em->flush();
                // On définit un message flash
                $this->get('session')->getFlashBag()->add('info', 'Nouvelle sortie bien ajoutée');
                // On redirige vers la page de visualisation de la sortie nouvellement créée
                //TODO changer la route 'organizer' par la page affichant la récap de la saisie
                return $this->redirect($this->generateUrl('organizer', array('id' => $sortie->getId())));
            }
        }
        return $this->render('organizer/index.html.twig', array(
            'form' => $form->createView(),
        ));
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
