<?php

namespace App\Entity;

use App\Repository\OrderedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderedRepository::class)
 */
class Ordered
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Shops::class, inversedBy="ordereds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $shop;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ordereds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=OrderedProducts::class, mappedBy="ordered", orphanRemoval=true)
     */
    private $orderedProducts;

    /**
     * @ORM\Column(type="boolean")
     */
    private $orderReady;

    public function __construct()
    {
        $this->orderedProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShop(): ?Shops
    {
        return $this->shop;
    }

    public function setShop(?Shops $shop): self
    {
        $this->shop = $shop;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|OrderedProducts[]
     */
    public function getOrderedProducts(): Collection
    {
        return $this->orderedProducts;
    }

    public function addOrderedProduct(OrderedProducts $orderedProduct): self
    {
        if (!$this->orderedProducts->contains($orderedProduct)) {
            $this->orderedProducts[] = $orderedProduct;
            $orderedProduct->setOrdered($this);
        }

        return $this;
    }

    public function removeOrderedProduct(OrderedProducts $orderedProduct): self
    {
        if ($this->orderedProducts->removeElement($orderedProduct)) {
            // set the owning side to null (unless already changed)
            if ($orderedProduct->getOrdered() === $this) {
                $orderedProduct->setOrdered(null);
            }
        }

        return $this;
    }

    public function getOrderReady(): ?bool
    {
        return $this->orderReady;
    }

    public function setOrderReady(bool $orderReady): self
    {
        $this->orderReady = $orderReady;

        return $this;
    }
}
