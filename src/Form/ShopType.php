<?php

namespace App\Form;

use App\Entity\Departments;
use App\Entity\Regions;
use App\Entity\ShopCategories;
use App\Entity\Shops;
use App\Entity\Towns;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ShopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('nameShop', TextType::class, [
                'required' => false,
                'label' => 'Nom du magasin'
            ])
            ->add('picture', FileType::class,[
                'mapped' => false,
                'required' => false,
                'label' => 'Image du magasin',
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Seul les formats suivants sont autorisés (jpg,jpeg,png,webp)'
                    ])
                ],
            ])
            ->add('presentation',TextareaType::class,[
                'label' => 'Présentation',
                'attr' => [
                    'rows' => 6
                ]
            ])
            ->add('streetNumber',TextType::class,[
                'label' => 'Numéro de la voie (bis,ter,...)'
            ])
            ->add('street',TextType::class,[
                'label' => 'Rue'
            ])
            ->add('category', EntityType::class, [
                'class' => ShopCategories::class,
                'choice_label' => 'name_category',
                'label' => 'Type de magasin'
            ])
            ->add('region', EntityType::class, [
                'class' => Regions::class,
                'placeholder' => 'Sélectionnez votre région',
                'mapped' => false,
                'required' => false,
                'label' => 'Région'
            ])
            ->add('create', SubmitType::class,[
                'label' => 'Valider'
            ]);

        $builder->get('region')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $this->addDepartmentField($form->getParent(), $form->getData());
            }
        );

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();
                $town = $data->getTown();
                $form = $event->getForm();
                if ($town) {
                    $department = $town->getDepartment();
                    $region = $department->getRegion();
                    $this->addDepartmentField($form, $region);
                    $this->addTownsField($form, $department);
                    $form->get('region')->setData($region);
                    $form->get('department')->setData($department);
                } else {
                    $this->addDepartmentField($form, null);
                    $this->addTownsField($form, null);
                }
            }
        );
    }

    /**
     * add field department at form
     *
     * @param FormInterface $form
     * @param Regions $regions
     */
    private function addDepartmentField(FormInterface $form, ?Regions $regions)
    {
        $builder = $form->getConfig()->getFormFactory()
            ->createNamedBuilder(
                'department',
                EntityType::class,
                null,
                [
                    'class' => Departments::class,
                    'placeholder' => $regions ? 'Sélectionnez votre département' : 'Sélectionnez votre région',
                    'mapped' => false,
                    'required' => false,
                    'auto_initialize' => false,
                    'choices' => $regions ? $regions->getDepartments() : [],
                    'label' => 'Département'
                ]
            );
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();

                $this->addTownsField($form->getParent(), $form->getData());
            }
        );
        $form->add($builder->getForm());
    }

    /**
     * add field town at form
     *
     * @param FormInterface $form
     * @param Departments $departments
     */
    private function addTownsField(FormInterface $form, ?Departments $departments)
    {
        $form->add(
            'town',
            EntityType::class,
            [
                'class' => Towns::class,
                'placeholder' => $departments ? 'Sélectionnez votre ville' : 'Selectionnez votre département',
                'choices' => $departments ? $departments->getTowns() : [],
                'required' => false,
                'label' => 'Ville'
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Shops::class,
        ]);
    }
}
