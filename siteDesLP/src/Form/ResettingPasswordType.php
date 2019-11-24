<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;
class ResettingPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
              'type' => PasswordType::class,
              'first_options' => ['label' =>'Nouveau mot de passe'],
              'second_options' => ['label' => 'Confirmer le mot de passe'],
              'invalid_message' => 'Les deux mots de passes ne sont pas identiques',
              'constraints' => [new Length(['max' => 64, 'maxMessage' => 'Mot de passe trop long, veuillez en saisir un de moins de 64 caractères svp'])]
              
            ]);


    }


}
