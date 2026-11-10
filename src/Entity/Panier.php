<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

// Panier en cours ou déjà payé
#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    // remplie au paiement, pas dans un formulaire
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateAchat = null;

    // false = pas encore payé
    #[ORM\Column]
    private bool $etat = false;

    /** @var Collection<int, ContenuPanier> */
    #[ORM\OneToMany(mappedBy: 'panier', targetEntity: ContenuPanier::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $contenus;

    public function __construct()
    {
        $this->contenus = new ArrayCollection();
        $this->etat = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getDateAchat(): ?\DateTimeImmutable
    {
        return $this->dateAchat;
    }

    public function setDateAchat(?\DateTimeImmutable $dateAchat): static
    {
        $this->dateAchat = $dateAchat;

        return $this;
    }

    public function isEtat(): bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    /** @return Collection<int, ContenuPanier> */
    public function getContenus(): Collection
    {
        return $this->contenus;
    }

    public function addContenu(ContenuPanier $contenu): static
    {
        if (!$this->contenus->contains($contenu)) {
            $this->contenus->add($contenu);
            $contenu->setPanier($this);
        }

        return $this;
    }

    // prix x quantité de chaque ligne
    public function getMontantTotal(): float
    {
        $total = 0;
        foreach ($this->contenus as $contenu) {
            $total += $contenu->getMontantLigne();
        }

        return $total;
    }
}
