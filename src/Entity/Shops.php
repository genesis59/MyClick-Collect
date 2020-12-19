<?php

namespace App\Entity;

use App\Repository\ShopsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ShopsRepository::class)
 */
class Shops
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameShop;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $picture;

    /**
     * @ORM\Column(type="text")
     */
    private $presentation;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="shops")
     * @ORM\JoinColumn(nullable=true)
     */
    private $trader;

    /**
     * @ORM\ManyToOne(targetEntity=ShopCategories::class, inversedBy="shops")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Likes::class, mappedBy="shopLike")
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity=ShopSubCategories::class, mappedBy="shop", orphanRemoval=true)
     */
    private $shopSubCategories;

    /**
     * @ORM\OneToMany(targetEntity=Products::class, mappedBy="shop", orphanRemoval=true)
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity=Ordered::class, mappedBy="shop")
     */
    private $ordereds;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $streetNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $street;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity=Towns::class, inversedBy="shops")
     * @ORM\JoinColumn(nullable=true)
     */
    private $town;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->shopSubCategories = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->ordereds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameShop(): ?string
    {
        return $this->nameShop;
    }

    public function setNameShop(string $nameShop): self
    {
        $this->nameShop = $nameShop;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(string $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getTrader(): ?User
    {
        return $this->trader;
    }

    public function setTrader(?User $trader): self
    {
        $this->trader = $trader;

        return $this;
    }

    public function getCategory(): ?ShopCategories
    {
        return $this->category;
    }

    public function setCategory(?ShopCategories $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Likes[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Likes $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setShopLike($this);
        }

        return $this;
    }

    public function removeLike(Likes $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getShopLike() === $this) {
                $like->setShopLike(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ShopSubCategories[]
     */
    public function getShopSubCategories(): Collection
    {
        return $this->shopSubCategories;
    }

    public function addShopSubCategory(ShopSubCategories $shopSubCategory): self
    {
        if (!$this->shopSubCategories->contains($shopSubCategory)) {
            $this->shopSubCategories[] = $shopSubCategory;
            $shopSubCategory->setShop($this);
        }

        return $this;
    }

    public function removeShopSubCategory(ShopSubCategories $shopSubCategory): self
    {
        if ($this->shopSubCategories->removeElement($shopSubCategory)) {
            // set the owning side to null (unless already changed)
            if ($shopSubCategory->getShop() === $this) {
                $shopSubCategory->setShop(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Products[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Products $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setShop($this);
        }

        return $this;
    }

    public function removeProduct(Products $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getShop() === $this) {
                $product->setShop(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ordered[]
     */
    public function getOrdereds(): Collection
    {
        return $this->ordereds;
    }

    public function addOrdered(Ordered $ordered): self
    {
        if (!$this->ordereds->contains($ordered)) {
            $this->ordereds[] = $ordered;
            $ordered->setShop($this);
        }

        return $this;
    }

    public function removeOrdered(Ordered $ordered): self
    {
        if ($this->ordereds->removeElement($ordered)) {
            // set the owning side to null (unless already changed)
            if ($ordered->getShop() === $this) {
                $ordered->setShop(null);
            }
        }

        return $this;
    }

    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(string $streetNumber): self
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getTown(): ?Towns
    {
        return $this->town;
    }

    public function setTown(?Towns $town): self
    {
        $this->town = $town;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
