<?php

namespace App\Entity;

use App\Repository\DepartmentsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DepartmentsRepository::class)
 */
class Departments
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
    private $nameDepartment;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codeDepartment;

    /**
     * @ORM\ManyToOne(targetEntity=Regions::class, inversedBy="departments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $region;

    /**
     * @ORM\OneToMany(targetEntity=Towns::class, mappedBy="department")
     */
    private $towns;

    public function __construct()
    {
        $this->towns = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameDepartment(): ?string
    {
        return $this->nameDepartment;
    }

    public function setNameDepartment(string $nameDepartment): self
    {
        $this->nameDepartment = $nameDepartment;

        return $this;
    }

    public function getCodeDepartment(): ?string
    {
        return $this->codeDepartment;
    }

    public function setCodeDepartment(string $codeDepartment): self
    {
        $this->codeDepartment = $codeDepartment;

        return $this;
    }

    public function getRegion(): ?Regions
    {
        return $this->region;
    }

    public function setRegion(?Regions $region): self
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return Collection|Towns[]
     */
    public function getTowns(): Collection
    {
        return $this->towns;
    }

    public function addTown(Towns $town): self
    {
        if (!$this->towns->contains($town)) {
            $this->towns[] = $town;
            $town->setDepartment($this);
        }

        return $this;
    }

    public function removeTown(Towns $town): self
    {
        if ($this->towns->removeElement($town)) {
            // set the owning side to null (unless already changed)
            if ($town->getDepartment() === $this) {
                $town->setDepartment(null);
            }
        }

        return $this;
    }
    public function __toString(){
        
        return $this->nameDepartment ;
    }
}
