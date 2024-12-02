<?php

namespace App\Entity;

use App\Repository\ClassPeriodRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClassPeriodRepository::class)]
#[ORM\Table(name: "bloki_zajec")]
class ClassPeriod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Group $group = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Teacher $teacher = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Room $room = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClassType $classType = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subject $subject = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $data_rozpoczecia = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $data_zakonczenia = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): static
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getClassType(): ?ClassType
    {
        return $this->classType;
    }

    public function setClassType(?ClassType $classType): static
    {
        $this->classType = $classType;

        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getData_rozpoczecia(): ?\DateTimeInterface
    {
        return $this->data_rozpoczecia;
    }

    public function setData_rozpoczecia(\DateTimeInterface $data_rozpoczecia): static
    {
        $this->data_rozpoczecia = $data_rozpoczecia;

        return $this;
    }

    public function getData_zakonczenia(): ?\DateTimeInterface
    {
        return $this->data_zakonczenia;
    }

    public function setData_zakonczenia(\DateTimeInterface $data_zakonczenia): static
    {
        $this->data_zakonczenia = $data_zakonczenia;

        return $this;
    }
}
