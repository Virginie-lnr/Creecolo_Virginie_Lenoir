<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Vich\UploaderBundle\VichUploaderBundle;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class EditProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name')
            ->add(
                'name',
                TextType::class,
                ['label' => 'Last name']
            )
            ->add('email', EmailType::class)
            ->add('imageFile', FileType::class, [
                'mapped' => true,
                'required' => false,
                'label' => 'Profile picture',
                'attr' => array(
                    'placeholder' => 'Choose a profile picture'
                )
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['rows' => 5],
                'required' => false,
            ])
            ->add('Save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
