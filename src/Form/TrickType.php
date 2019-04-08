<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\Tag;
use App\Entity\Trick;
use App\Repository\ImageRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    const TRICK = 'trick_id';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['attr'][self::TRICK];
        $builder
            ->add('thumbnail', EntityType::class, [
                'choice_label' => 'name',
                'class' => Image::class,
                'query_builder' => function (ImageRepository $repository) use ($id) {
                    return $repository->createQueryBuilder('i')
                        ->where('i.trick = :trick')
                        ->setParameter('trick', $id);
                },
                'multiple' => false,
                'required' => false,
                'empty_data' => '',
                'attr' => ['style' => 'display: none;']
            ])
            ->add('name')
            ->add('description')
            ->add('tag', EntityType::class, [
                'choice_label' => 'name',
                'label' => 'Group',
                'class' => Tag::class,
                'attr' => [
                    'class' => 'custom-select',
                    'data-size' => '4'
                ],
            ])
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
