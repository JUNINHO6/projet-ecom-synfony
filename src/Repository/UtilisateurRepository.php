<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    // inscrits du jour, du plus récent au plus ancien
    public function findInscritsAujourdhui(): array
    {
        $debut = new \DateTimeImmutable('today');
        $fin = $debut->modify('+1 day');

        return $this->createQueryBuilder('u')
            ->andWhere('u.dateInscription >= :debut')
            ->andWhere('u.dateInscription < :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->orderBy('u.dateInscription', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
