<?php

namespace App\Tests\Controller;

use App\Entity\Client;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ProductControllerTest
 * @package App\Tests\Controller
 */
class OrderControllerTest extends WebTestCase
{

    public function testNewOrder()
    {
        $client = $this->getClient();

        $orderClient = $client->getId()->toString();

        $client = static::createClient();

        $crawler = $client->request('GET', '/order/create');
        $form = $crawler->selectButton('Salvar')->form([
            'order[client]' => $orderClient,
        ]);

        $client->submit($form);

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        /** @var Order $order */
        $order = $client->getContainer()->get('doctrine')->getRepository(Order::class)->findOneBy([
            'client' => $orderClient,
        ]);

        $this->assertNotNull($order);
        $this->assertSame($orderClient, $order->getClient()->getId()->toString());
    }

    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/order');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertGreaterThanOrEqual(
            1,
            $crawler->filter('body#order_index .main table.table tbody tr')->count(),
            'The page displays all the available orders.'
        );
    }

    /**
     * Campo UUID não tem valor único
     * Não é possivel setar um ID em /order/delete/{id}
     */

    public function testDeleteOrder()
    {
        $orderId = $this->getOrder()->getId()->toString();
        $client = static::createClient();

        $client->request('GET', '/order/delete/' . $orderId);
        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        /** @var Order $order */
        $order = $client->getContainer()->get('doctrine')->getRepository(Order::class)->find($orderId);
        $this->assertNull($order);
    }


    /**
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function getClient()
    {
        $kernel = static::bootKernel();
        /* @var EntityManager $entityManager */
        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $repository = $entityManager->getRepository(Client::class);
        $queryBuilder = $repository->createQueryBuilder('c')
            ->addSelect('RAND() as HIDDEN rand')
            ->addOrderBy('rand')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        return $queryBuilder;
    }

    /**
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function getProduct()
    {
        $kernel = static::bootKernel();
        /* @var EntityManager $entityManager */
        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $repository = $entityManager->getRepository(Product::class);
        $queryBuilder = $repository->createQueryBuilder('p')
            ->addSelect('RAND() as HIDDEN rand')
            ->addOrderBy('rand')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        return $queryBuilder;
    }

    /**
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function getOrder()
    {
        $kernel = static::bootKernel();
        /* @var EntityManager $entityManager */
        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $repository = $entityManager->getRepository(Order::class);
        $queryBuilder = $repository->createQueryBuilder('o')
            ->addSelect('RAND() as HIDDEN rand')
            ->addOrderBy('rand')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        return $queryBuilder;
    }
}