<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MangasRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=MangasRepository::class)
 */
#[ApiResource(
    normalizationContext: [
        "groups" => ["timestampable", 'read_mangas']
    ],
    denormalizationContext: [
        "groups" => ["write_mangas" ]
    ],
)]
class Mangas
{
    use TimestampableEntity;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Scan::class, mappedBy="relation")
     */
    private $scans;

    /**
     * @ORM\OneToMany(targetEntity=Crawler::class, mappedBy="mangas")
     */
    private $crawlers;

    public function __construct()
    {
        $this->scans = new ArrayCollection();
        $this->crawlers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Scan[]
     */
    public function getScans(): Collection
    {
        return $this->scans;
    }

    public function addScan(Scan $scan): self
    {
        if (!$this->scans->contains($scan)) {
            $this->scans[] = $scan;
            $scan->setRelation($this);
        }

        return $this;
    }

    public function removeScan(Scan $scan): self
    {
        if ($this->scans->removeElement($scan)) {
            // set the owning side to null (unless already changed)
            if ($scan->getRelation() === $this) {
                $scan->setRelation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Crawler[]
     */
    public function getCrawlers(): Collection
    {
        return $this->crawlers;
    }

    public function addCrawler(Crawler $crawler): self
    {
        if (!$this->crawlers->contains($crawler)) {
            $this->crawlers[] = $crawler;
            $crawler->setMangas($this);
        }

        return $this;
    }

    public function removeCrawler(Crawler $crawler): self
    {
        if ($this->crawlers->removeElement($crawler)) {
            // set the owning side to null (unless already changed)
            if ($crawler->getMangas() === $this) {
                $crawler->setMangas(null);
            }
        }

        return $this;
    }
}