<?php

namespace App\Subscriber;

/* https://symfony.com/doc/current/doctrine/events.html#doctrine-lifecycle-subscribers */


use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Sortie;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class SortieSubscriber implements EventSubscriber
{
    // this method can only return the event names; you cannot define a
    // custom method name to execute when each event triggers
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postRemove,
            Events::postUpdate,
        ];
    }

    // callback methods must be called exactly like the events they listen to;
    // they receive an argument of type LifecycleEventArgs, which gives you access
    // to both the entity object of the event and the entity manager itself
    public function postPersist(LifecycleEventArgs $args)
    {
        // tester si Sortie persistée
        if ($args->getObject() instanceof Inscription) {
            /** @var Inscription $inscription */
            $inscription = $args->getObject();
            $sortie = $inscription->getSortie();
            $this->setEtat($sortie, $args);
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof Inscription) {
            /** @var Inscription $inscription */
            $inscription = $args->getObject();
            $sortie = $inscription->getSortie();
            $this->setEtat($sortie, $args);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
    }

    private function setEtat(Sortie $sortie, LifecycleEventArgs $args) {
        $em = $args->getObjectManager();
        if (
            $sortie->getInscriptions()->count() == $sortie->getNbInscriptionsMax() &&
            $sortie->getEtat()->getLibelle() != Etat::CLOTUREE
        ) {
            $etatCloturee = $em->getRepository(Etat::class)->findOneBy(['libelle' => Etat::CLOTUREE]);
            $sortie->setEtat($etatCloturee);
            $em->persist($sortie);
            $em->flush();
        } elseif (
            $sortie->getInscriptions()->count() < $sortie->getNbInscriptionsMax() &&
            $sortie->isDesinscrirePossible() &&
            $sortie->getEtat()->getLibelle() != Etat::OUVERTE
        ) {
            $etatOuverte = $em->getRepository(Etat::class)->findOneBy(['libelle' => Etat::OUVERTE]);
            $sortie->setEtat($etatOuverte);
            $em->persist($sortie);
            $em->flush();
        }
    }
}