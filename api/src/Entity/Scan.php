<?php

namespace App\Entity;

use App\Repository\ScanRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=ScanRepository::class)
 */
#[ApiResource]
class Scan
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $number;

    /**
     * @ORM\ManyToOne(targetEntity=Mangas::class, inversedBy="scans")
     * @ORM\JoinColumn(nullable=false)
     */
    private $relation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?float
    {
        return $this->number;
    }

    public function setNumber(float $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getRelation(): ?Mangas
    {
        return $this->relation;
    }

    public function setRelation(?Mangas $relation): self
    {
        $this->relation = $relation;

        return $this;
    }
}