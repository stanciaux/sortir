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

    public function afficher($param){

//        $qb = $this->createQueryBuilder('s');
//        $qb->andWhere('s.site = :site')
//        ->setParameter('site',$param['site']);
//        $query = $qb->getQuery();
//        $result = $query->getResult();
//
//        return $result;

        $qb = $this->createQueryBuilder('s');
        $qb->andWhere("s.nom = :search")
            ->setParameter("search", 'sortie');
        dump($qb);
        $query = $qb->getQuery();
        $result = $query->getResult();
        dump($result);

        return $result;



//            $sorties = $em->getRepository(Sortie::class);
            $dql = "SELECT s
                    FROM App\Entity\Sortie s
//                  WHERE site_id LIKE ";

//            $siteRecherche = $em->getRepository(Site::class)->findOneBy(['nom' => $site])->getId();
//            dump($siteRecherche);
//            $sorties = $em->getRepository(Sortie::class)->findBy(['site_id' => $siteRecherche]);
//            $idSite = $siteRecherche->getId();

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
