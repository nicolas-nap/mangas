<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ScanRepository;
use App\DependencyInjection\EntityIdTrait;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=ScanRepository::class)
 */
class Scan
{
    use TimestampableEntity;
    use EntityIdTrait;

    /**
     * @ORM\Column(type="integer")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Mangas::class, inversedBy="scans")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Mangas;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?int
    {
        return $this->name;
    }

    public function setName(int $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMangas(): ?Mangas
    {
        return $this->Mangas;
    }

    public function setMangas(?Mangas $Mangas): self
    {
        $this->Mangas = $Mangas;

        return $this;
    }
}
