<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
#[ORM\UniqueConstraint(name: 'student_student_index_unique', columns: ['student_index'])]
#[UniqueEntity('student_index')]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $studentIndex = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudentIndex(): ?int
    {
        return $this->studentIndex;
    }

    public function setStudentIndex(int $studentIndex): static
    {
        $this->studentIndex = $studentIndex;

        return $this;
    }
}
