<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('platform', ChoiceType::class, [
                'choices'  => [
                    Video::DAILYMOTION => Video::DAILYMOTION_TYPE,
                    Video::YOUTUBE => Video::YOUTUBE_TYPE,
                ],
                'label' => 'Platforme',
            ])
            ->add('videoId', null, [
                'label' => 'Identifiant',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
