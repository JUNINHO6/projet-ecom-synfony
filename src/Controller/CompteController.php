<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Form\ProfilType;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// Profil et anciennes commandes
#[IsGranted('ROLE_USER')]
class CompteController extends AbstractController
{
    #[Route('/compte', name: 'app_compte')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        PanierRepository $panierRepository,
    ): Response {
        $utilisateur = $this->getUser();
        $form = $this->createForm(ProfilType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plain = $form->get('plainPassword')->getData();
            if ($plain) {
                $utilisateur->setPassword($hasher->hashPassword($utilisateur, $plain));
            }
            $em->flush();
            $this->addFlash('success', 'flash.account.updated');

            return $this->redirectToRoute('app_compte');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'flash.account.error');
        }

        $commandes = $panierRepository->findCommandesPayees($utilisateur);

        return $this->render('compte/index.html.twig', [
            'form' => $form,
            'commandes' => $commandes,
        ]);
    }

    #[Route('/commande/{id}', name: 'app_commande_show', requirements: ['id' => '\d+'])]
    public function commande(Panier $panier): Response
    {
        // on ne montre que ses propres commandes déjà payées
        if ($panier->getUtilisateur() !== $this->getUser() || !$panier->isEtat()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('compte/commande.html.twig', [
            'panier' => $panier,
        ]);
    }
}
