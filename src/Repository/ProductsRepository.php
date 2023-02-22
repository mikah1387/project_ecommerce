<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Products>
 *
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    public function save(Products $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Products $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

     public function findProductsPaginated(int $page, $slug, $limit=4 )
     {

          $result =[];
          $query = $this->createQueryBuilder('p')
                  ->join('p.categories','c')
                  ->Where("c.slug = '$slug'")
                  ->setMaxResults($limit)
                  ->setFirstResult(($page*$limit)-$limit);

               $paginator= new Paginator($query);
               $data = $paginator->getQuery()->getResult();

               
                  
                 if (empty($data)){
                    $result['data'] = '';
                    $result['pages'] = '';
                    $result['page'] = '';
                    $result['limit'] = '';
                    return $result ;
                 }
                //  on calcule nombre de pages

                $pages = ceil($paginator->count() / $limit);
                $result['data'] = $data;
                $result['pages'] = $pages;
                $result['page'] = $page;
                $result['limit'] = $limit;

             
        //   $result = $query->getQuery()->getResult();
     
        
          return $result;



     }
     public function findProductbycategorie( $name )
     {
      

         return  $this->createQueryBuilder('p')
                  ->join('p.categories','c')
                  ->Where("c.name = '$name'")
                  ->getQuery()->getResult();
     }

//    /**
//     * @return Products[] Returns an array of Products objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Products
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
