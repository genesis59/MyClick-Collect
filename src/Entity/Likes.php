<?php

namespace App\Entity;

use App\Repository\LikesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LikesRepository::class)
 */
class Likes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Shops::class, inversedBy="likes")
     */
    private $shopLike;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="likes")
     */
    private $userLike;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShopLike(): ?Shops
    {
        return $this->shopLike;
    }

    public function setShopLike(?Shops $shopLike): self
    {
        $this->shopLike = $shopLike;

        return $this;
    }

    public function getUserLike(): ?User
    {
        return $this->userLike;
    }

    public function setUserLike(?User $userLike): self
    {
        $this->userLike = $userLike;

        return $this;
    }
}
