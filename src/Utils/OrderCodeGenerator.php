<?php

namespace App\Utils;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Doctrine\ORM\Query;

class OrderCodeGenerator extends AbstractIdGenerator
{

    public function generate(EntityManager $em, $entity)
    {
        $query = 'SELECT MAX(code) FROM `order`';
        $id = $em->createNativeQuery($query, (new Query\ResultSetMapping()))
            ->getOneOrNullResult(Query::HYDRATE_SINGLE_SCALAR);
        $id++;
        //$em->getConnection()->executeUpdate('UPDATE `order` SET code = ?', [$id++]);
        return $id;
    }
}