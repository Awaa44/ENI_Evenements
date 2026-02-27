<?php

namespace App\Form;

use App\Entity\Lieux;
use App\Entity\Villes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomLieu', TextType::class, [
                'label' => 'Nom du lieu : ',
                'required' => false,
            ])
            ->add('rue', TextType::class, [
                'label' => 'Rue : ',
                'required' => false,
            ])
           /* ->add('latitude')
            ->add('longitude')*/
            ->add('villes', EntityType::class, [
                'class' => Villes::class,
                'choice_label' => 'nomVille',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieux::class,
        ]);
    }
}
