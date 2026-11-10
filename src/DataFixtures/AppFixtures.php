<?php

namespace App\DataFixtures;

use App\Entity\ContenuPanier;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// Comptes de test, mot de passe : password
class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $client = $this->creerUtilisateur($manager, 'Martin', 'Léa', 'client@boreal.test', ['ROLE_USER']);
        $this->creerUtilisateur($manager, 'Dubois', 'Hugo', 'admin@boreal.test', ['ROLE_ADMIN']);
        $this->creerUtilisateur($manager, 'Noir', 'Inès', 'super@boreal.test', ['ROLE_SUPER_ADMIN']);

        // Un compte inscrit hier pour montrer le filtre "aujourd'hui"
        $hier = $this->creerUtilisateur($manager, 'Leroy', 'Paul', 'hier@boreal.test', ['ROLE_USER']);
        $hier->setDateInscription(new \DateTimeImmutable('-1 day'));

        $produits = [];
        $donnees = [
            ['Sencha du jardin', 'Thé vert japonais aux notes herbacées, idéal le matin.', 12.90, 40],
            ['Earl Grey bergamote', 'Thé noir parfumé à la bergamote, grand classique britannique.', 9.50, 55],
            ['Rooibos vanille', 'Infusion rooibos sans théine, douce et ronde.', 8.90, 30],
            ['Jasmin perles', 'Perles de thé vert parfumées au jasmin, floral et raffiné.', 16.40, 18],
            ['Chaï masala', 'Mélange épicé au gingembre, cardamome et cannelle.', 11.20, 25],
            ['Menthe nana', 'Infusion de menthe nana, fraîche et digestive.', 6.80, 60],
        ];

        foreach ($donnees as [$nom, $description, $prix, $stock]) {
            $produit = new Produit();
            $produit->setNom($nom);
            $produit->setDescription($description);
            $produit->setPrix($prix);
            $produit->setStock($stock);
            $manager->persist($produit);
            $produits[] = $produit;
        }

        // Panier en cours du client
        $panierEnCours = new Panier();
        $panierEnCours->setUtilisateur($client);
        $ligne = new ContenuPanier();
        $ligne->setProduit($produits[0]);
        $ligne->setQuantite(2);
        $panierEnCours->addContenu($ligne);
        $manager->persist($panierEnCours);
        $manager->persist($ligne);

        // Commande déjà payée
        $commande = new Panier();
        $commande->setUtilisateur($client);
        $commande->setEtat(true);
        $commande->setDateAchat(new \DateTimeImmutable('-3 days'));
        $lignePayee = new ContenuPanier();
        $lignePayee->setProduit($produits[1]);
        $lignePayee->setQuantite(1);
        $commande->addContenu($lignePayee);
        $manager->persist($commande);
        $manager->persist($lignePayee);

        $manager->flush();
    }

    private function creerUtilisateur(ObjectManager $manager, string $nom, string $prenom, string $email, array $roles): Utilisateur
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setNom($nom);
        $utilisateur->setPrenom($prenom);
        $utilisateur->setEmail($email);
        $utilisateur->setRoles($roles);
        $utilisateur->setPassword($this->hasher->hashPassword($utilisateur, 'password'));
        $manager->persist($utilisateur);

        return $utilisateur;
    }
}
