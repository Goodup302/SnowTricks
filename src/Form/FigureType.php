<?php

namespace App\Form;

use App\Entity\Figure;
use App\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FigureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('thumbnail', FileType::class, array(
                'data_class' => null,
                'label' => 'Image principal'
            ))
            ->add('name')
            ->add('description')
            ->add('tag', ChoiceType::class, [
                'choices' => Group::LIST,
                'label' => "Type de figure"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
