<?php

// src/Form/RecipeType.php
namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        	->add('name', TextType::class, array('label' => 'Name', 'required' => true))
        	->add('method', TextareaType::class, array('label' => 'Method', 'required' => false, 'attr' => array('style' => 'height: 180px')))
        	->add('ingredients', TextareaType::class, array('label' => 'Ingredients', 'required' => false, 'attr' => array('style' => 'height: 180px')))
            ->add('recipeFile', FileType::class, array('label' => 'Recipe (PDF or image file)', 'required' => false))
            ->add('recipeImages', CollectionType::class, array('entry_type' => RecipeImageType::class, 
            'entry_options' => array('label' => false),
            'allow_add' => true, 'by_reference' => false, 'allow_delete' => true));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Recipe::class,
        ));
    }
}