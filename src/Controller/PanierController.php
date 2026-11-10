<?php

namespace App\Controller;

use App\Entity\ContenuPanier;
use App\Repository\PanierRepository;
use App\Service\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// Panier non payé de la personne connectée
#[IsGranted('ROLE_USER')]
class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(PanierRepository $panierRepository): Response
    {
        $panier = $panierRepository->findPanierEnCours($this->getUser());

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
        ]);
    }

    #[Route('/panier/ligne/{id}/supprimer', name: 'app_panier_remove', methods: ['POST'])]
    public function remove(ContenuPanier $ligne, Request $request, EntityManagerInterface $em): Response
    {
        $panier = $ligne->getPanier();
        if (!$panier || $panier->getUtilisateur() !== $this->getUser() || $panier->isEtat()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('remove'.$ligne->getId(), $request->request->get('_token'))) {
            $em->remove($ligne);
            $em->flush();
            $this->addFlash('success', 'flash.cart.removed');
        }

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/panier/acheter', name: 'app_panier_buy', methods: ['POST'])]
    public function acheter(Request $request, PanierRepository $panierRepository, PanierService $panierService): Response
    {
        $panier = $panierRepository->findPanierEnCours($this->getUser());
        if (!$panier) {
            $this->addFlash('error', 'flash.cart.empty');

            return $this->redirectToRoute('app_panier');
        }

        if (!$this->isCsrfTokenValid('buy'.$panier->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'flash.cart.error');

            return $this->redirectToRoute('app_panier');
        }

        $ok = $panierService->acheter($panier);
        $this->addFlash($ok ? 'success' : 'error', $ok ? 'flash.cart.bought' : 'flash.cart.stock');

        return $this->redirectToRoute($ok ? 'app_compte' : 'app_panier');
    }
}
