<?php

namespace App\Controller;

use App\Repository\PanierRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// Réservé au super admin
#[IsGranted('ROLE_SUPER_ADMIN')]
#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/paniers', name: 'app_admin_paniers')]
    public function paniers(PanierRepository $panierRepository): Response
    {
        return $this->render('admin/paniers.html.twig', [
            'paniers' => $panierRepository->findPaniersNonPayes(),
        ]);
    }

    #[Route('/inscrits', name: 'app_admin_inscrits')]
    public function inscrits(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('admin/inscrits.html.twig', [
            'utilisateurs' => $utilisateurRepository->findInscritsAujourdhui(),
        ]);
    }
}
