<?php

namespace App\Entity;

use App\Repository\InscriptionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscriptionsRepository::class)]
class Inscriptions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participants $participant = null;

    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sorties $sortie = null;

    #[ORM\Column]
    private ?\DateTime $dateInscription = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParticipant(): ?Participants
    {
        return $this->participant;
    }

    public function setParticipant(?Participants $participant): static
    {
        $this->participant = $participant;

        return $this;
    }

    public function getSortie(): ?Sorties
    {
        return $this->sortie;
    }

    public function setSortie(?Sorties $sortie): static
    {
        $this->sortie = $sortie;

        return $this;
    }

    public function getDateInscription(): ?\DateTime
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTime $dateInscription): static
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }
}
