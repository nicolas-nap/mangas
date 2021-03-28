<?php

namespace App\Entity;

use App\Repository\CrawlerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CrawlerRepository::class)
 */
class Crawler
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Mangas::class, inversedBy="crawlers")
     */
    private $mangas;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMangas(): ?Mangas
    {
        return $this->mangas;
    }

    public function setMangas(?Mangas $mangas): self
    {
        $this->mangas = $mangas;

        return $this;
    }
}
