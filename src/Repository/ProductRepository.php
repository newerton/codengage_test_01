<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Class ProductRepository
 * @package App\Repository
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * ProductRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param int $page
     * @param $query
     * @return Pagerfanta
     */
    public function latestProduct($page = 1, $query)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder()
            ->select('p')
            ->from('App:Product', 'p');

        if ($query) {
            if ($query['code']) {
                $queryBuilder->andWhere("p.code LIKE :code")
                    ->setParameter('code', $query['code']);
            }
            if ($query['name']) {
                $queryBuilder->andWhere("p.name LIKE :name")
                    ->setParameter('name', $query['name']);
            }
            if ($query['price']) {
                $queryBuilder->andWhere("p.price LIKE :price")
                    ->setParameter('price', $query['price']);
            }
        }

        $queryBuilder->orderBy('p.name', 'ASC');

        return $this->createPaginator($queryBuilder, $page);
    }

    /**
     * @param QueryBuilder $query
     * @param $page
     * @return Pagerfanta
     */
    private function createPaginator(QueryBuilder $query, $page)
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(Product::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
}