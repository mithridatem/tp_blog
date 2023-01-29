<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => true,
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'date'],
            ])
            ->add('validated')
            ->add('categories', EntityType::class, [
                // looks for choices from this entity
                'class' => Category::class,
                'attr' => ['class' => 'choice'],
                'label'=> 'Choisir la catégorie :              ',
                // uses the User.username property as the visible option string
                'choice_label' => 'name',
            
                // used to render a select box, check boxes or radios
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('Ajouter', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
