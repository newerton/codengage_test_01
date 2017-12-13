<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Class ClientRepository
 * @package App\Repository
 */
class ClientRepository extends ServiceEntityRepository
{
    /**
     * ClientRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * @param int $page
     * @param $query
     * @return Pagerfanta
     */
    public function latestClient($page = 1, $query)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder()
            ->select('c')
            ->from('App:Client', 'c');

        if ($query) {
            if ($query['name']) {
                $queryBuilder->andWhere("c.name LIKE :name")
                    ->setParameter('name', $query['name']);
            }
            if ($query['birthday']) {
                $birthday = date('Y-m-d', strtotime(str_replace('/', '-', $query['birthday'])));
                $queryBuilder->andWhere("c.birthday = :birthday")
                    ->setParameter('birthday', $birthday);
            }
        }

        $queryBuilder->orderBy('c.name', 'ASC');

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
        $paginator->setMaxPerPage(Client::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
}