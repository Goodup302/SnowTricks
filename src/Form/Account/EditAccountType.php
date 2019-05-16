<?php

namespace App\Form\Account;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('files', FileType::class, array(
                //'data_class' => null,
                'label' => 'Image du profile',
                'attr' => [
                    'accept' => 'image/JPEG',
                    'multiple' => 'false'
                ],
                'label_attr' => ['class' => 'btn btn-dark'],
                'multiple' => true
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
            'csrf_protection' => false,
        ]);
    }
}