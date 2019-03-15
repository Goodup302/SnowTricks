<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\Tag;
use App\Entity\Trick;
use App\Entity\Video;
use App\Repository\ImageRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('thumbnail', EntityType::class, [
                'choice_label' => 'name',
                'class' => Image::class,
                'multiple' => false,
                'required' => false,
                'attr' => [
                    'style' => 'display: none;'
                ]
            ])
            ->add('name')
            ->add('images', EntityType::class, [
                'class'        => Image::class,
                'choice_label' => 'name',
                'multiple'     => true,
                'required' => false,
            ])
            ->add('description')
            ->add('tag', EntityType::class, [
                'choice_label' => 'name',
                'class' => Tag::class,
                'attr' => [
                    'class' => 'custom-select',
                    'data-size' => '4',
                ],
            ])
            /*->add('Modifier', SubmitType::class)*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            'csrf_protection' => false,
        ]);
    }
}
