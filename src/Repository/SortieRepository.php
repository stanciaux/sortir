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

    public function findIfNotArchived(){
        $em = $this->getEntityManager();
        $dql = "SELECT s 
                FROM App\Entity\Sortie s 
                WHERE s.etat = 1
                OR s.etat = 2
                OR s.etat = 3
                OR s.etat = 4
                OR s.etat = 5
                OR s.etat = 6";
        $query = $em->createQuery($dql);
        $result = $query->getResult();
        return $result;
    }

    public function recherche($param)
    {
        dump($param);
        $qb = $this->createQueryBuilder('s');
        // recherche par nom de site
        if ($param['site']) {
            $qb->join('s.site', 'site');
            $qb->andWhere('site.nom LIKE :site');
            $qb->setParameter(':site', '%' . $param['site'] . '%');
        }
        // recherche de la sortie en fonction du champ recherche
        if ($param['search']) {
            $qb->andWhere('s.nom LIKE :search');
            $qb->setParameter(':search', '%' . $param['search'] . '%');
        }
        // Filtrage par dates de début et fin
        if ($param['dateDebut'] && $param['dateFin']) {
            $qb->andWhere('s.dateSortie BETWEEN :dateDebut AND :dateFin');
            $qb->setParameter('dateDebut', $param['dateDebut']);
            $qb->setParameter('dateFin', $param['dateFin']);
        }
        // filtrage par la date de début
        if ($param['dateDebut']) {
            $qb->andWhere('s.dateSortie > :dateDebut');
            $qb->setParameter('dateDebut', $param['dateDebut']);
        }
        // filtrage par la date de fin
        if ($param['dateFin']) {
            $qb->andWhere('s.dateSortie < :dateFin');
            $qb->setParameter('dateFin', $param['dateFin']);
        }
        // Si l'utilisateur est organisateur. Récupération de l'idUser pour faire la recherche organisateur.
        if ($param['organisateur']) {
            $qb->andWhere('s.organisateur = :organisateur');
            $qb->setParameter('organisateur', $param['user']);
        }
        // si l'utilisateur est inscrit. Récupération de l'idUser pour faire la recherche inscrit.
        if ($param['inscrit']) {
            // Jointure entre sorties et inscriptions
            $qb->join('s.inscriptions', 'i');
            // Jointure entre inscriptions et participant/user
            $qb->join('i.participant', 'p');
            // Conditionnelle où le participant est le user
            $qb->andWhere('p = :user');
            $qb->setParameter('user', $param['user']);
        }
        // Sorties où l'utilisateur n'est pas inscrit
        if ($param['nonInscrit']) {
            // On commence par rechercher les sorties auxquelles l'utilisateur est inscrit
            $qb2 = $this->createQueryBuilder('s2')
                ->select('s2.id')
                ->join('s2.inscriptions', 'i2')
                ->join('i2.participant', 'p2')
                ->andWhere('p2 = :user2')
                ;
            // Seconde requête pour chercher toutes les sorties moins celles auxquelles l'utilisateur est inscrit ($qb2).
            // La fonction notIn() permet de faire le tri.
            $qb->andWhere($qb->expr()->notIn('s.id', $qb2->getDQL()))
                // Attention le setParameter() du $qb2 doit se mettre à la fin.
                ->setParameter(':user2', $param['user']);
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
