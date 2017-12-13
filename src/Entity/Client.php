<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="client")
 * @UniqueEntity(
 *     fields = {"name"},
 *     errorPath = "name",
 *     message = "Este nome já está sendo usado!"
 * )
 * Ref: http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html
 */
class Client
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
     * @var string $name
     *
     * @ORM\Column(type="string", unique=true, options={"comment":"Nome completo"})
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string $birthday
     *
     * @ORM\Column(type="date", options={"comment":"Data de nascimento"})
     * @Assert\NotBlank
     */
    private $birthday;

    /**
     * @var \DateTime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", options={"comment":"Criado em"})
     * @Assert\DateTime
     */
    private $createdAt;

    /**
     * One Client has Many Order.
     * @ORM\OneToMany(targetEntity="Order", mappedBy="client")
     */
    private $orders;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->orders = new ArrayCollection();
    }

    function __toString()
    {
        return $this->name;
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
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param string $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
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

    /**
     * @return mixed
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param mixed $orders
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

}