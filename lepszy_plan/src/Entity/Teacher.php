<?php

namespace App\Entity;

use App\Repository\TeacherRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeacherRepository::class)]
#[ORM\Table(name: "prowadzacy")]
class Teacher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $imienazwisko = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getImienazwisko(): ?string
    {
        return $this->imienazwisko;
    }

    public function setImienazwisko(string $imienazwisko): static
    {
        $this->imienazwisko = $imienazwisko;

        return $this;
    }
}
