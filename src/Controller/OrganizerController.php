<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\AnnulationType;
use App\Form\LieuType;
use App\Form\OrganizerType;
use App\Entity\Site;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\SortieType;
use Doctrine\ORM\Query\AST\Functions\DateDiffFunction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class OrganizerController extends AbstractController
{
    /**
     * @Route("/organizer", name="organizer")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function newParty(Request $request, EntityManagerInterface $em)
    {

        // On crée 2 objets : sortie et lieu
        $sortie = new Sortie();
        $lieu = new Lieu();
        // On crée les formulaire grâce à OrganizerType et LieuType
        // Et on récupère les requêtes avec le handleRequest
        $formLieu = $this->createForm(LieuType::class, $lieu);
//        $formLieu->handleRequest($request);
        $form = $this->createForm(OrganizerType::class, $sortie);
        $form->handleRequest($request);
        dump($sortie);


        //On récupére toute la liste de la class Ville
        $listVille = $em->getRepository(Ville::class)->findAll();

        // On vérifie que les valeurs entrées sont correctes
        if ($formLieu->isSubmitted() && $formLieu->isValid()) {
            $lieu = $formLieu->getData();
            $sortie = $form->getData();
            $formResend = $this->createForm(OrganizerType::class, $sortie);
            // Soumettre les informations au formulaire
            $formResend->handleRequest($request);

            // On enregistre notre objet $lieu dans la base de données
            $em->persist($lieu);
            $em->flush();
            $this->addFlash('success', 'Le lieu a été ajouté !');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie = $form->getData();

            $dateSortie = $form['dateSortie']->getData();
//            $sortie->setDatedebut(\DateTime::createFromFormat('Y/m/d H:i', $dateSortie));
            $dateCloture = $form['dateCloture']->getData();
//            $sortie->setDatecloture(\DateTime::createFromFormat('Y/m/d', $datecloture));

            $etatCree = $em->getRepository(Etat::class)->find(1);
            $etatOuvert = $em->getRepository(Etat::class)->find(1);
            if ($form->get('Enregistrer')->isClicked()) {
                $sortie->setEtat($etatCree);
            } elseif ($form->get('PublierLaSortie')->isClicked()) {
                $sortie->setEtat($etatOuvert);
//                return $this->redirectToRoute('publish_party', ['id' => $sortie->getId()]);
            } else {
                return $this->redirectToRoute('sortie_list');
            }

            $sortie->setOrganisateur($this->getUser());

            // On enregistre notre objet $sortie dans la base de données
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', 'La sortie a été ajoutée !');
            return $this->redirectToRoute('sortie_list');
        }


        return $this->render('organizer/newParty.html.twig'
            , [
                'page_name' => 'Créer une sortie',
                'form' => $form->createView(),
                'formLieu' => $formLieu->createView(),
                'listVille' => $listVille

            ]);
    }

    /**
     * @Route("/updateParty/{id}", name="update_party")
     */
    public function updateParty($id, Request $request, EntityManagerInterface $em)
    {

        $sortie = $em->getRepository(Sortie::class)->find($id);
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', "La sortie a été modifiée");
            return $this->redirectToRoute('sortie_detail', ['id'=>$id]);
        }
        return $this->render('sortie/updateParty.html.twig',
            [
                "form" => $form->createView()
            ]);
    }

    /**
     * @Route("/archiveParty/{id}", name="archive_party", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function archive($id, EntityManagerInterface $em)
    {
        $etatArchive = $em->getRepository(Etat::class)->find(7);
        $sortieAarchiver = $em->getRepository(Sortie::class)->find($id);

        if ($sortieAarchiver->isArchivagePossible($this->getUser())) {
            $sortieAarchiver->setEtat($etatArchive);
            $em->flush();
            $this->addFlash('success', 'La sortie a été archivée');
            return $this->redirectToRoute('sortie_list');
        } else {
            $this->addFlash('warning', "Le délai d'archivage n'est pas respecté");
            return $this->redirectToRoute('sortie_list');
        }

        return $this->redirectToRoute('sortie_list');
    }

    /**
     * @Route("/publishParty/{id}", name="publish_party", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function publish($id, EntityManagerInterface $em)
    {
        $etatOuvert = $em->getRepository(Etat::class)->find(2);
        $sortieAouvrir = $em->getRepository(Sortie::class)->find($id);

        if ($sortieAouvrir->isPubliable($this->getUser())) {
            $sortieAouvrir->setEtat($etatOuvert);
            $em->flush();
            $this->addFlash('success', "La sortie est désormais ouverte aux inscriptions");
            return $this->redirectToRoute('sortie_list');
        } else {
            $this->addFlash('warning', "Cette sortie est déjà ouverte");
            return $this->redirectToRoute('sortie_list');
        }

        return $this->redirectToRoute('sortie_list');
    }

    /**
     * @Route("/cancelParty/{id}", name="cancel_party", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function cancel($id, EntityManagerInterface $em, Request $request)
    {

        $sortie = $em->getRepository(Sortie::class)->find($id);
        $etatAnnule = $em->getRepository(Etat::class)->find(6);

        $formAnnulation = $this->createForm(AnnulationType::class, $sortie);
        $formAnnulation->handleRequest($request);

        if ($formAnnulation->isSubmitted() && $formAnnulation->isValid()
            && $sortie->isAnnulable($this->getUser()) ) {
            $sortie->setEtat($etatAnnule);
            $em->flush();

            $this->addFlash('success', "Sortie annulée");
            return $this->redirectToRoute('sortie_list');
        }

        return $this->render('sortie/cancelParty.html.twig',
            [
                "sortie" => $sortie,
                "formAnnulation" => $formAnnulation->createView()
            ]);
    }

    /**
     * @Route("/deleteParty/{id}", name="delete_party", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function delete($id, EntityManagerInterface $em)
    {
        $sortieAsupprimer = $em->getRepository(Sortie::class)->find($id);

        if ($sortieAsupprimer->isSupprimable($this->getUser())) {
            $em->remove($sortieAsupprimer);
            $em->flush();
            $this->addFlash('success', "La sortie a été supprimée");
            return $this->redirectToRoute('sortie_list');
        } else {
            $this->addFlash('warning', "Cette sortie n'existe pas");
            return $this->redirectToRoute('sortie_list');
        }
        return $this->redirectToRoute('sortie_list');
    }

}