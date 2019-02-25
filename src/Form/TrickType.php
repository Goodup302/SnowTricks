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
                'multiple' => false
            ])
            ->add('name')
/*            ->add('videos', CollectionType::class, [
                'entry_type' => ImageType::class,
                'entry_options' => ['label' => false],
            ])*/
/*            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
            ])*/
            ->add('images', EntityType::class, [
                'class'        => Image::class,
                'choice_label' => 'name',
                'multiple'     => true,
            ])
            ->add('tag', EntityType::class, [
                'choice_label' => 'name',
                'class' => Tag::class,
            ])
            ->add('description')
            ->add('tag', EntityType::class, [
                'choice_label' => 'name',
                'class' => Tag::class,
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