<?php

namespace App\Tests\Controller;


use App\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ClientControllerTest
 * @package App\Tests\Controller
 */
class ClientControllerTest extends WebTestCase
{

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

    public function testNewClient()
    {
        $clientName = 'Client ' . mt_rand();
        $clientBirthday = date('Y-m-d');

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
        $this->assertSame($clientBirthday, $client->getBirthday());
    }

    /**
     * Campo UUID não tem valor único
     * Não é possivel setar um ID em /client/update/{id}
     */
    public function testUpdateClient()
    {

    }

    /**
     * Campo UUID não tem valor único
     * Não é possivel setar um ID em /client/delete/{id}
     */
    public function testDeleteClient()
    {

    }

}