<?php

namespace App\Controller;

use App\Entity\Sortie;
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
                return $this->redirect($this->generateUrl('organizer', array('id' => $sortie->getId())));
            }
        }
        return $this->render('organizer/index.html.twig', array(
            'form' => $form->createView(),
        ));

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
