<?php

namespace App\Form;

use App\Entity\Fichiers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class FichiersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('formFichiers', FileType::class, [
                'mapped' => true,
                'label' => 'Sélectionnez votre document',
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Fichiers::class,
        ]);
    }
}
