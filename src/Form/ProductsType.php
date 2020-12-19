<?php

namespace App\Form;

use App\Entity\Products;
use App\Entity\ShopSubCategories;
use App\Repository\ShopSubCategoriesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductsType extends AbstractType
{

    private $request;
    private $shop;

    public function __construct(RequestStack $request )
    {
        $this->request = $request->getCurrentRequest();
        $this->shop = $this->recupCurrentShop();
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,[
                'label' => 'Nom du produit'
            ])
            ->add('stock',NumberType::class)
            ->add('designation', TextareaType::class, [
                'label' => 'Déscription du produit',
                'attr' => [
                    'rows' => 6
                ]
            ])
            ->add('picture', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Image du produit',
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
            ->add('price',NumberType::class,[
                'label' => 'prix du produit'
            ])
            ->add('subCategory',EntityType::class,[
                'class' => ShopSubCategories::class,
                'required' => false,
                'choice_label' => 'name_sub_category',
                'placeholder' => 'sans catégorie',
                'label' => 'Choix de la catégorie',
                'query_builder' => function(ShopSubCategoriesRepository $er){
                    return $er->findSubCategoriesByShopForBuilder($this->shop);
                    
                }
            ])
            ->add('create', SubmitType::class,[
                'label' => 'Valider'
            ])
            ->add('redirect_url',TextType::class,[
            'mapped' => false,
            'data'=>$this->request->headers->get('referer'),
            'label' => false,
            'attr' => [
                'hidden' => true
            ]
            ])
        ;
    }

    private function recupCurrentShop(){
        foreach($this->request->attributes as $key => $value){
            if($key == 'shop'){
                return $value;
            }
            
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
