<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

// Le mot de passe reste vide si on ne veut pas le changer
class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'account.lastname',
            ])
            ->add('prenom', TextType::class, [
                'label' => 'account.firstname',
            ])
            ->add('email', EmailType::class, [
                'label' => 'account.email',
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'account.password_new',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Length(min: 6, max: 4096),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
