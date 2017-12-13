<?php

namespace App\Entity;

use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="product")
 *
 * @UniqueEntity(
 *     fields = {"code"},
 *     errorPath = "code",
 *     message = "Este código já está sendo usado!"
 * )
 * @UniqueEntity(
 *     fields = {"name"},
 *     errorPath = "name",
 *     message = "Este nome já está sendo usado!"
 * )
 */
class Product
{
    const NUM_ITEMS = 10;

    /**
     * @var UuidInterface $id
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true, options={"comment":"ID"})
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var string $code
     *
     * @ORM\Column(type="string", unique=true, options={"comment":"Código do produto"})
     * @Assert\NotBlank
     */
    private $code;

    /**
     * @var string $name
     *
     * @ORM\Column(type="string", unique=true, options={"comment":"Nome do produto"})
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string $price
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, options={"comment":"Preço"})
     * @Assert\NotBlank
     */
    private $price;

    /**
     * @var \DateTime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", options={"comment":"Criado em"})
     * @Assert\DateTime
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->code . ' - ' . $this->name . ' - R$' . number_format($this->price, 2, ',', '.');
    }

    /**
     * @return UuidInterface
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param UuidInterface $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

}