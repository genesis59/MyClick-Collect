<?php

namespace App\Form;

use App\Entity\Departments;
use App\Entity\Towns;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TownsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name_town',TextType::class,[
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom de la ville'
                ]
            ])
            ->add('zip_code',TextType::class,[
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Code postal'
                ]
            ])
            ->add('valider',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Towns::class,
        ]);
    }
}
