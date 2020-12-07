<?php

namespace App\Entity;

use App\Repository\OrderedProductsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderedProductsRepository::class)
 */
class OrderedProducts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity=Products::class, inversedBy="orderedProducts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=Ordered::class, inversedBy="orderedProducts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ordered;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getProduct(): ?Products
    {
        return $this->product;
    }

    public function setProduct(?Products $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getOrdered(): ?Ordered
    {
        return $this->ordered;
    }

    public function setOrdered(?Ordered $ordered): self
    {
        $this->ordered = $ordered;

        return $this;
    }
}
