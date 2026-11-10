<?php

namespace App\Repository;

use App\Entity\Panier;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Panier>
 */
class PanierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Panier::class);
    }

    // uniquement le panier non payé de cet utilisateur
    public function findPanierEnCours(Utilisateur $utilisateur): ?Panier
    {
        return $this->findOneBy([
            'utilisateur' => $utilisateur,
            'etat' => false,
        ]);
    }

    /** @return Panier[] */
    public function findCommandesPayees(Utilisateur $utilisateur): array
    {
        return $this->findBy(
            ['utilisateur' => $utilisateur, 'etat' => true],
            ['dateAchat' => 'DESC'],
        );
    }

    /** @return Panier[] */
    public function findPaniersNonPayes(): array
    {
        return $this->findBy(['etat' => false], ['id' => 'DESC']);
    }
}
