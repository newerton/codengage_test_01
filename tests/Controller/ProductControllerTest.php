<?php

namespace App\Tests\Controller;


use App\Entity\Client;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProductControllerTest
 * @package App\Tests\Controller
 */
class ProductControllerTest extends WebTestCase
{

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

    public function testNewProduct()
    {
        $productCode = 'Product ' . mt_rand();
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
            'name' => $productName,
        ]);
        $this->assertNotNull($product);
        $this->assertSame($productCode, $product->getCode());
        $this->assertSame($productName, $product->getName());
        $this->assertSame($productPrice, $product->getPrice());
    }

    /**
     * Campo UUID não tem valor único
     * Não é possivel setar um ID em /client/update/{id}
     */
    public function testUpdateProduct()
    {

    }

    /**
     * Campo UUID não tem valor único
     * Não é possivel setar um ID em /client/delete/{id}
     */
    public function testDeleteProduct()
    {

    }

}