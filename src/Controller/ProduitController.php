<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\AjoutPanierType;
use App\Form\ProduitType;
use App\Service\PanierService;
use App\Service\PhotoUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// Fiche produit. Modifier et supprimer : admin seulement
class ProduitController extends AbstractController
{
    #[Route('/produit/{id}', name: 'app_produit_show', requirements: ['id' => '\d+'])]
    public function show(Produit $produit, Request $request, PanierService $panierService): Response
    {
        $formPanier = $this->createForm(AjoutPanierType::class);
        $formPanier->handleRequest($request);

        if ($formPanier->isSubmitted() && $formPanier->isValid()) {
            if (!$this->getUser()) {
                $this->addFlash('error', 'flash.cart.login');

                return $this->redirectToRoute('app_login');
            }

            $quantite = (int) $formPanier->get('quantite')->getData();
            $ok = $panierService->ajouterProduit($this->getUser(), $produit, $quantite);
            $this->addFlash($ok ? 'success' : 'error', $ok ? 'flash.cart.added' : 'flash.cart.stock');

            return $this->redirectToRoute('app_produit_show', ['id' => $produit->getId()]);
        }

        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'formPanier' => $formPanier,
        ]);
    }

    #[Route('/produit/{id}/modifier', name: 'app_produit_edit', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Produit $produit,
        Request $request,
        EntityManagerInterface $em,
        PhotoUploader $photoUploader,
    ): Response {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoUploader->renseignerPhoto($produit, $form->get('photoFichier')->getData());
            $em->flush();
            $this->addFlash('success', 'flash.product.updated');

            return $this->redirectToRoute('app_produit_show', ['id' => $produit->getId()]);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'flash.product.error');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/produit/{id}/supprimer', name: 'app_produit_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Produit $produit, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $em->remove($produit);
            $em->flush();
            $this->addFlash('success', 'flash.product.deleted');
        } else {
            $this->addFlash('error', 'flash.product.error');
        }

        return $this->redirectToRoute('app_home');
    }
}
