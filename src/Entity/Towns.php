<?php

namespace App\Entity;

use App\Repository\TownsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TownsRepository::class)
 */
class Towns
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
    private $name_town;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $zip_code;

    /**
     * @ORM\ManyToOne(targetEntity=Departments::class, inversedBy="towns")
     * @ORM\JoinColumn(nullable=false)
     */
    private $department;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="town")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Shops::class, mappedBy="town")
     */
    private $shops;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->shops = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameTown(): ?string
    {
        return $this->name_town;
    }

    public function setNameTown(string $name_town): self
    {
        $this->name_town = $name_town;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zip_code;
    }

    public function setZipCode(string $zip_code): self
    {
        $this->zip_code = $zip_code;

        return $this;
    }

    public function getDepartment(): ?Departments
    {
        return $this->department;
    }

    public function setDepartment(?Departments $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function __toString(){
        
        return $this->name_town . ' (' . $this->zip_code .')' ;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setTown($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getTown() === $this) {
                $user->setTown(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Shops[]
     */
    public function getShops(): Collection
    {
        return $this->shops;
    }

    public function addShop(Shops $shop): self
    {
        if (!$this->shops->contains($shop)) {
            $this->shops[] = $shop;
            $shop->setTown($this);
        }

        return $this;
    }

    public function removeShop(Shops $shop): self
    {
        if ($this->shops->removeElement($shop)) {
            // set the owning side to null (unless already changed)
            if ($shop->getTown() === $this) {
                $shop->setTown(null);
            }
        }

        return $this;
    }

}
