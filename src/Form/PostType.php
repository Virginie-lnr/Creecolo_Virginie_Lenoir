<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Twig\Node\Expression\Binary\SubBinary;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('content', TextareaType::class, [
                'attr' => ['rows' => 10]
            ])
            ->add('imageFile', FileType::class, [
                'required' => false,
                // 'multiple' => true
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class, 
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('a')
                            ->orderBy('a.label', 'ASC');
                }, 
                'label' => 'Choose categories', 
                'multiple'   => true, 
                'expanded' => true
            ])
            ->add('Save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
