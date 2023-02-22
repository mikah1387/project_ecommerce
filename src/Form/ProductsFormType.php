<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Products;
use App\Repository\CategoriesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Positive;

class ProductsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',options : [
                'label'=>'nom'
            ])
            ->add('description',TextareaType::class,[
                'label'=>'Description SEO',

            ])
            ->add('prix',MoneyType::class, options:[
                'label'=>'Prix',
                'divisor'=>1,
                'constraints'=>[
                    new Positive(
                        message: 'le prix ne peut etre négative'
                    )
                ]
                
            ])
            ->add('stock',options:[
                 'label'=>'Unités en stock'
            ])
            ->add('categories',EntityType::class,[
                'class'=>Categories::class,
                'choice_label'=>'name',
                'label'=>'catégorie',
                'group_by'=>'parent.name',
                'query_builder'=>function (CategoriesRepository $cr){

                    return $cr->createQueryBuilder('c')
                              -> where('c.parent IS NOT NULL')
                              ->orderBy('c.name','ASC');
                }
            ] )
            ->add('images', FileType::class,[
                'multiple'=>true,
                'mapped'=>false,
                'required'=>false,
                'constraints'=>[
                    new All(new Image([
                        'maxWidth'=>1300,
                        'maxWidthMessage'=> 'l\'image doit faire {{ max_width }} pixels de large au max '
                                ] ))
                    
                ]
            ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
