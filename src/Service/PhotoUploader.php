<?php

namespace App\Service;

use App\Entity\Produit;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;

// Copie la photo dans public/uploads/produits
class PhotoUploader
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/public/uploads/produits')]
        private readonly string $photosDirectory,
    ) {
    }

    public function renseignerPhoto(Produit $produit, ?UploadedFile $fichier): void
    {
        if (null === $fichier) {
            return;
        }

        $nom = uniqid().'.'.$fichier->guessExtension();
        $fichier->move($this->photosDirectory, $nom);
        $produit->setPhoto($nom);
    }
}
