<?php

namespace App\Entity;

use App\Repository\SortiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Expression("
this.getDateLimiteInscription()
and this.getDateHeureDebut()
and this.getDateLimiteInscription() <= this.getDateHeureDebutInfCinqJours()",
    message:'La date limite d\'inscription doit être inférieure de 5 jours par rapport à la date de sortie')]
#[Assert\Expression(
    "this.getLieux()", message: 'Un lieu de sortie doit être sélectionné'
)]
#[ORM\Entity(repositoryClass: SortiesRepository::class)]
class Sorties
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'La sortie doit avoir un nom')]
    #[Assert\Length(min:3, max: 255, minMessage: 'La sortie doit avoir au minimum {{ limit }} caractères',
    maxMessage: 'Le nom de la sortie ne peux pas excéder {{ limit }} caractères')]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:'La sortie doit avoir une date de début')]
    private ?\DateTime $dateHeureDebut = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message:'La sortie doit avoir une durée')]
    #[Assert\GreaterThanOrEqual(value: 15, message: 'La durée de la sortie doit être supérieure ou égal à 15 min')]
    private ?int $duree = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:'La sortie doit avoir une date limite d\'inscription')]
    #[Assert\GreaterThanOrEqual(
        value: new \DateTime('now'),
        message: 'La date limite doit être supérieure à la date du jour'
    )]
    private ?\DateTime $dateLimiteInscription = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:'La sortie doit avoir un nombre max d\'inscription')]
    #[Assert\GreaterThanOrEqual(value: 5, message: 'Le nombre maximum d\'inscrit doit être supérieur ou égal à 5')]
    private ?int $nbInscriptionMax = null;

    #[ORM\Column(length: 500, nullable: true)]
    #[Assert\NotBlank(message:'La sortie doit avoir une description')]
    #[Assert\Length(min:50, max:500, minMessage:'La sortie doit avoir une description d\'au moins 50 caractères',
        maxMessage:'La sortie doit avoir une description d\'au maximum 500 caractères')]
    private ?string $infosSortie = null;

    #[ORM\Column(length: 350, nullable: true)]
    #[Assert\NotBlank(message:'L\'annulation doit avoir un motif',
    groups: ['cancel'])]
    private ?string $motifAnnulation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $urlPhoto = null;

    /**
     * @var Collection<int, Inscriptions>
     */
    #[ORM\OneToMany(targetEntity: Inscriptions::class, mappedBy: 'sortie', orphanRemoval: true)]
    private Collection $inscriptions;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participants $organisateur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etats $etats = null;

    #[ORM\Column(nullable: true)]
    private ?int $etatSortie = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lieux $lieux = null;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateHeureDebut(): ?\DateTime
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(?\DateTime $dateHeureDebut): static
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTime
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(?\DateTime $dateLimiteInscription): static
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionMax(): ?int
    {
        return $this->nbInscriptionMax;
    }

    public function setNbInscriptionMax(int $nbInscriptionMax): static
    {
        $this->nbInscriptionMax = $nbInscriptionMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(?string $infosSortie): static
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getUrlPhoto(): ?string
    {
        return $this->urlPhoto;
    }

    public function setUrlPhoto(?string $urlPhoto): static
    {
        $this->urlPhoto = $urlPhoto;

        return $this;
    }

    /**
     * @return Collection<int, Inscriptions>
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscriptions $inscription): static
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions->add($inscription);
            $inscription->setSortie($this);
        }

        return $this;
    }

    public function removeInscription(Inscriptions $inscription): static
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getSortie() === $this) {
                $inscription->setSortie(null);
            }
        }

        return $this;
    }

    public function getOrganisateur(): ?Participants
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Participants $organisateur): static
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getEtats(): ?Etats
    {
        return $this->etats;
    }

    public function setEtats(?Etats $etats): static
    {
        $this->etats = $etats;

        return $this;
    }

    public function getEtatSortie(): ?int
    {
        return $this->etatSortie;
    }

    public function setEtatSortie(?int $etatSortie): static
    {
        $this->etatSortie = $etatSortie;

        return $this;
    }

    public function getLieux(): ?Lieux
    {
        return $this->lieux;
    }

    public function setLieux(?Lieux $lieux): static
    {
        $this->lieux = $lieux;

        return $this;
    }

    public function getDateHeureDebutInfCinqJours(): ?\DateTime
    {
        if($this->dateHeureDebut === null) {
            return null;
        }
        $date = clone $this->dateHeureDebut;
        return $date->modify('-5 days');
    }

    public function getMotifAnnulation(): ?string
    {
        return $this->motifAnnulation;
    }

    public function setMotifAnnulation(string $motifAnnulation): static
    {
        $this->motifAnnulation = $motifAnnulation;

        return $this;
    }
}
