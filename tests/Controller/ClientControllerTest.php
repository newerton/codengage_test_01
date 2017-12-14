<?php

namespace App\Tests\Controller;


use App\Entity\Client;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ClientControllerTest
 * @package App\Tests\Controller
 */
class ClientControllerTest extends WebTestCase
{

    public function testNewClient()
    {
        $clientName = 'Client ' . mt_rand();
        $clientBirthday = date('d/m/Y');

        $client = static::createClient();

        $crawler = $client->request('GET', '/client/create');
        $form = $crawler->selectButton('Salvar')->form([
            'client[name]' => $clientName,
            'client[birthday]' => $clientBirthday,
        ]);
        $client->submit($form);

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        /** @var Client $client */
        $client = $client->getContainer()->get('doctrine')->getRepository(Client::class)->findOneBy([
            'name' => $clientName,
        ]);

        $this->assertNotNull($client);
        $this->assertSame($clientName, $client->getName());
        $this->assertSame($clientBirthday, $client->getBirthday()->format('d/m/Y'));
    }

    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/client');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertGreaterThanOrEqual(
            1,
            $crawler->filter('body#client_index .main table.table tbody tr')->count(),
            'The page displays all the available clients.'
        );
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testUpdateClient()
    {
        $clientId = $this->getClient()->getId()->toString();
        $newName = 'Client Updated ' . mt_rand();

        $client = static::createClient();
        $crawler = $client->request('GET', '/client/update/' . $clientId);
        $form = $crawler->selectButton('Editar')->form([
            'client[name]' => $newName,
        ]);
        $client->submit($form);
        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        /** @var Client $client */
        $client = $client->getContainer()->get('doctrine')->getRepository(Client::class)->find($clientId);
        $this->assertSame($newName, $client->getName());
    }


    public function testDeleteClient()
    {
        $clientId = $this->getClient()->getId()->toString();
        $client = static::createClient();

        $client->request('GET', '/client/delete/' . $clientId);
        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        /** @var Client $client */
        $client = $client->getContainer()->get('doctrine')->getRepository(Client::class)->find($clientId);
        $this->assertNull($client);
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

}