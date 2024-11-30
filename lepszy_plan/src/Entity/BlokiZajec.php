<?php

namespace App\Entity;

use App\Repository\BlokiZajecRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlokiZajecRepository::class)]
class BlokiZajec
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
    private ?Prowadzacy $prowadzacy = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sale $sala = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypyZajec $typZajec = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Przedmioty $przedmiot = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dataRozpoczecia = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dataZakonczenia = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getProwadzacy(): ?Prowadzacy
    {
        return $this->prowadzacy;
    }

    public function setProwadzacy(?Prowadzacy $prowadzacy): static
    {
        $this->prowadzacy = $prowadzacy;

        return $this;
    }

    public function getSala(): ?Sale
    {
        return $this->sala;
    }

    public function setSala(?Sale $sala): static
    {
        $this->sala = $sala;

        return $this;
    }

    public function getTypZajec(): ?TypyZajec
    {
        return $this->typZajec;
    }

    public function setTypZajec(?TypyZajec $typZajec): static
    {
        $this->typZajec = $typZajec;

        return $this;
    }

    public function getPrzedmiot(): ?Przedmioty
    {
        return $this->przedmiot;
    }

    public function setPrzedmiot(?Przedmioty $przedmiot): static
    {
        $this->przedmiot = $przedmiot;

        return $this;
    }

    public function getDataRozpoczecia(): ?\DateTimeInterface
    {
        return $this->dataRozpoczecia;
    }

    public function setDataRozpoczecia(\DateTimeInterface $dataRozpoczecia): static
    {
        $this->dataRozpoczecia = $dataRozpoczecia;

        return $this;
    }

    public function getDataZakonczenia(): ?\DateTimeInterface
    {
        return $this->dataZakonczenia;
    }

    public function setDataZakonczenia(\DateTimeInterface $dataZakonczenia): static
    {
        $this->dataZakonczenia = $dataZakonczenia;

        return $this;
    }
}
