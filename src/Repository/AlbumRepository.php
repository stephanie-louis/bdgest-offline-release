<?php

namespace App\Repository;

use App\Entity\Album;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Album|null find($id, $lockMode = null, $lockVersion = null)
 * @method Album|null findOneBy(array $criteria, array $orderBy = null)
 * @method Album[]    findAll()
 * @method Album[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Album::class);
    }



    public function findByLetter($whishlist, $letter){
        $conn = $this->getEntityManager()
            ->getConnection();

        $query = 'SELECT a.*, CAST(a.tome AS UNSIGNED) AS num, s.titre as titreSerie
            FROM album a
            INNER JOIN serie s ON s.id = a.serie_id AND s.titre LIKE :l
            WHERE a.whislist = :w
            ORDER BY s.titre, num';
        $stmt = $conn->prepare($query);
        $stmt->execute(['w' => $whishlist, 'l' => $letter . '%']);

        $results = $stmt->fetchAll();
        return $results;
    }

    /**
     * @param $id
     * @return Album|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findAlbum($id): ?Album
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    // /**
    //  * @return Album[] Returns an array of Album objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Album
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
