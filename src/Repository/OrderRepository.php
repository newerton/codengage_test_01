<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Class OrderRepository
 * @package App\Repository
 */
class OrderRepository extends ServiceEntityRepository
{
    /**
     * OrderRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @param int $page
     * @param $query
     * @return Pagerfanta
     */
    public function latestOrder($page = 1, $query)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder()
            ->select('o')
            ->from('App:Order', 'o');

        if ($query) {
            if ($query['client']) {
                $queryBuilder->leftJoin('App:Client', 'c','WITH','o.client = c.id')
                    ->andWhere('c.name LIKE :name')
                    ->setParameter('name', "%{$query['client']}%");
            }
            if ($query['code']) {
                $queryBuilder->andWhere("o.code LIKE :code")
                    ->setParameter('code', $query['code']);
            }
        }

        $queryBuilder->orderBy('o.createdAt', 'DESC');

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
        $paginator->setMaxPerPage(Order::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
}