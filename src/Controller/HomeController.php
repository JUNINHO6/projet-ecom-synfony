<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use App\Service\PhotoUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Accueil : liste des produits, et ajout si on est admin
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        ProduitRepository $produitRepository,
        EntityManagerInterface $em,
        PhotoUploader $photoUploader,
    ): Response {
        $produits = $produitRepository->findBy([], ['nom' => 'ASC']);

        $form = null;
        // le formulaire n'existe que pour l'admin
        if ($this->isGranted('ROLE_ADMIN')) {
            $produit = new Produit();
            $form = $this->createForm(ProduitType::class, $produit);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $photoUploader->renseignerPhoto($produit, $form->get('photoFichier')->getData());
                $em->persist($produit);
                $em->flush();
                $this->addFlash('success', 'flash.product.created');

                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('home/index.html.twig', [
            'produits' => $produits,
            'form' => $form,
        ]);
    }

    #[Route('/langue/{locale}', name: 'app_locale', requirements: ['locale' => 'fr|en'])]
    public function langue(string $locale, Request $request): Response
    {
        $request->getSession()->set('_locale', $locale);

        return $this->redirect($request->headers->get('referer') ?: $this->generateUrl('app_home'));
    }
}
