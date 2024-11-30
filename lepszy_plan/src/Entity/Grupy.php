<?php

namespace App\Entity;

use App\Repository\GrupyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GrupyRepository::class)]
class Grupy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $numer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNumer(): ?string
    {
        return $this->numer;
    }

    public function setNumer(string $numer): static
    {
        $this->numer = $numer;

        return $this;
    }
}
