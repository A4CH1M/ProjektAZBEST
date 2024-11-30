<?php

namespace App\Entity;

use App\Repository\GrupyStudenciRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GrupyStudenciRepository::class)]
class GrupyStudenci
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Grupy $grupa = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Studenci $student = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGrupa(): ?Grupy
    {
        return $this->grupa;
    }

    public function setGrupa(?Grupy $grupa): static
    {
        $this->grupa = $grupa;

        return $this;
    }

    public function getStudent(): ?Studenci
    {
        return $this->student;
    }

    public function setStudent(?Studenci $student): static
    {
        $this->student = $student;

        return $this;
    }
}
