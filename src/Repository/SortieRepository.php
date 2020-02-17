<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function recherche($param)
    {
        dump($param);
        $qb = $this
            ->createQueryBuilder('s');
            // recherche par nom de site
            if ($param['site'])
            {
                $qb->join('s.site', 'site');
                $qb->andWhere('site.nom LIKE :site');
                $qb->setParameter(':site', '%'.$param['site'].'%');
            }
            // recherche de la sortie en fonction du champ recherche
            if ($param['search'])
            {
                $qb->andWhere('s.nom LIKE :search');
                $qb->setParameter(':search', '%' . $param['search'] . '%');
            }
            // Filtrage par dates de début et fin
            if ($param['dateDebut'] && $param['dateFin'])
            {
                $qb->andWhere('s.dateSortie > :dateDebut');
                $qb->setParameter('dateDebut', $param['dateDebut']);
                $qb->andWhere('s.dateSortie < :dateFin');
                $qb->setParameter('dateFin', $param['dateFin']);
            }
            // filtrage par la date de début
            if ($param['dateDebut'])
            {
                $qb->andWhere('s.dateSortie > :dateDebut');
                $qb->setParameter('dateDebut', $param['dateDebut']);
            }
            // filtrage par la date de fin
            if ($param['dateFin'])
            {
               $qb->andWhere('s.dateSortie < :dateFin');
               $qb->setParameter('dateFin', $param['dateFin']);
            }
            // Si l'utilisateur est organisateur. Récupération de l'idUser pour faire la recherche organisateur.
            if ($param['organisateur'])
            {
//                $qb->select('s');
//                $qb->from('Sortie', 's');
//                $qb->where('organisateur.id = :organisateur');
                $qb->join('s.organisateur', 'id');
                $qb->join('o.organisateur', 'id');
                $qb->andWhere('organisateur.id LIKE :organisateur');
                $qb->setParameter('organisateur', $param['idUser']);


// $qb instanceof QueryBuilder
//
//                $qb->select('u')
//                    ->from('User', 'u')
//                    ->where('u.id = :identifier')
//                    ->orderBy('u.name', 'ASC')
//                    ->setParameter('identifier', 100); // Sets :identifier to 100, and thus we will fetch a user with u.id = 100
            }
            // si l'utilisateur est inscrit. Récupération de l'idUser pour faire la recherche inscrit.
            if ($param['inscrit'])
            {
                $qb->andWhere('s.inscriptions.participant_id LIKE :inscrit');
                $qb->setParameter('inscrit', $param['inscrit']);
            }
            // Sorties où l'utilisateur n'est pas inscrit
            if ($param['inscrit'])
            {
                $qb->andWhere('s.inscriptions.participant_id NOT LIKE :inscrit');
                $qb->setParameter('inscrit', $param['inscrit']);
            }

            // sorties passées
            if ($param['sortiesPassees']) {
                $qb->andWhere('s.dateSortie < :date');
                $qb->setParameter('date', new \DateTime());
            }
        $query = $qb->getQuery();
        return $query->getResult();
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
