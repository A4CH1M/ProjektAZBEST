<?php

namespace App\Entity;

use App\Repository\StudenciRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudenciRepository::class)]
class Studenci
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $indeks = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIndeks(): ?int
    {
        return $this->indeks;
    }

    public function setIndeks(int $indeks): static
    {
        $this->indeks = $indeks;

        return $this;
    }
}
