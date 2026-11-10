<?php

namespace App\Service;

use App\Entity\ContenuPanier;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;

// Ajout au panier et paiement
class PanierService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PanierRepository $panierRepository,
    ) {
    }

    // panier pas encore payé, on le crée s'il n'existe pas
    public function getOuCreerPanierEnCours(Utilisateur $utilisateur): Panier
    {
        $panier = $this->panierRepository->findPanierEnCours($utilisateur);
        if ($panier) {
            return $panier;
        }

        $panier = new Panier();
        $panier->setUtilisateur($utilisateur);
        $this->em->persist($panier);

        return $panier;
    }

    // ajoute le produit, ou augmente la quantité s'il y est déjà
    public function ajouterProduit(Utilisateur $utilisateur, Produit $produit, int $quantite): bool
    {
        if ($quantite < 1 || $produit->getStock() < $quantite) {
            return false;
        }

        $panier = $this->getOuCreerPanierEnCours($utilisateur);

        foreach ($panier->getContenus() as $ligne) {
            if ($ligne->getProduit() === $produit) {
                $nouvelleQuantite = $ligne->getQuantite() + $quantite;
                if ($nouvelleQuantite > $produit->getStock()) {
                    return false;
                }
                $ligne->setQuantite($nouvelleQuantite);
                $this->em->flush();

                return true;
            }
        }

        $ligne = new ContenuPanier();
        $ligne->setProduit($produit);
        $ligne->setQuantite($quantite);
        $panier->addContenu($ligne);
        $this->em->flush();

        return true;
    }

    // marque le panier comme payé et retire les quantités du stock
    public function acheter(Panier $panier): bool
    {
        if ($panier->isEtat() || $panier->getContenus()->isEmpty()) {
            return false;
        }

        foreach ($panier->getContenus() as $ligne) {
            $produit = $ligne->getProduit();
            if (!$produit || $ligne->getQuantite() > $produit->getStock()) {
                return false;
            }
        }

        foreach ($panier->getContenus() as $ligne) {
            $produit = $ligne->getProduit();
            $produit->setStock($produit->getStock() - $ligne->getQuantite());
        }

        $panier->setEtat(true);
        $panier->setDateAchat(new \DateTimeImmutable());
        $this->em->flush();

        return true;
    }
}
