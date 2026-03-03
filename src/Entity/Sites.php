<?php

namespace App\Entity;

use App\Repository\SitesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: SitesRepository::class)]
class Sites
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $nomSite = null;

    //ajout de la relation participants pour vérifier si participants pour delete site et update site
    #[ORM\OneToMany(targetEntity: Participants::class, mappedBy: 'sites')]
    private Collection $participants;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomSite(): ?string
    {
        return $this->nomSite;
    }

    public function setNomSite(string $nomSite): static
    {
        $this->nomSite = $nomSite;

        return $this;
    }

    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

}
