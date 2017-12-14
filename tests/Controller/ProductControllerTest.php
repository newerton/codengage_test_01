<?php

namespace App\Tests\Controller;


use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProductControllerTest
 * @package App\Tests\Controller
 */
class ProductControllerTest extends WebTestCase
{

    public function testNewProduct()
    {
        $productCode = (string)rand(0, 999);
        $productName = 'Product ' . mt_rand();
        $productPrice = number_format(rand(10000, 99999), 2, '.', '');

        $client = static::createClient();

        $crawler = $client->request('GET', '/product/create');
        $form = $crawler->selectButton('Salvar')->form([
            'product[code]' => $productCode,
            'product[name]' => $productName,
            'product[price]' => $productPrice,
        ]);
        $client->submit($form);

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        /** @var Product $product */
        $product = $client->getContainer()->get('doctrine')->getRepository(Product::class)->findOneBy([
            'code' => $productCode,
        ]);

        $this->assertNotNull($product);
        $this->assertSame($productCode, $product->getCode());
        $this->assertSame($productName, $product->getName());
        $this->assertSame($productPrice, number_format($product->getPrice(), 2, '.', ''));
    }


    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/product');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertGreaterThanOrEqual(
            1,
            $crawler->filter('body#product_index .main table.table tbody tr')->count(),
            'The page displays all the available products.'
        );
    }


    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testUpdateProduct()
    {
        $productId = $this->getProduct()->getId()->toString();
        $newName = 'Product Updated ' . mt_rand();

        $client = static::createClient();
        $crawler = $client->request('GET', '/product/update/' . $productId);
        $form = $crawler->selectButton('Editar')->form([
            'product[name]' => $newName,
            'product[price]' => rand(1000, 2000),
        ]);
        $client->submit($form);
        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        /** @var Product $product */
        $product = $client->getContainer()->get('doctrine')->getRepository(Product::class)->find($productId);

        $this->assertSame($newName, $product->getName());
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testDeleteProduct()
    {
        $productId = $this->getProduct()->getId()->toString();
        $client = static::createClient();

        $client->request('GET', '/product/delete/' . $productId);
        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        /** @var Product $product */
        $product = $client->getContainer()->get('doctrine')->getRepository(Product::class)->find($productId);
        $this->assertNull($product);
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
}