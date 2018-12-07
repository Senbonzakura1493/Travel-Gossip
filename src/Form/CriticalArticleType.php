<?php

namespace App\Form;

use App\Entity\CriticalArticle;

use App\Entity\Category;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CriticalArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Titre')
            ->add('Category', EntityType::class ,[
                'class'=> Category::class,
                'choice_label' => 'name'
            ])
            ->add('DateTime')
            ->add('Destination')
            ->add('PlaceToVisit')
            ->add('Resume')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CriticalArticle::class,
        ]);
    }
}
