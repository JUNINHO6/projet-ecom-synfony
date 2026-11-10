<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'product.name',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'product.description',
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'product.price',
                'currency' => 'EUR',
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'product.stock',
            ])
            ->add('photoFichier', FileType::class, [
                'label' => 'product.photo',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image(maxSize: '2M'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
