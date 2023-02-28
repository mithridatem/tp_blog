<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            
            TextField::new('title', 'titre'),
            TextareaField::new('content', 'contenu')->setNumOfRows(10),
            DateField::new('date')->setFormat('dd/MM/yyyy'),
            BooleanField::new('validated', 'valider')->renderAsSwitch(true),
            AssociationField::new('categories', 'Catégorie')->onlyOnForms(),
            AssociationField::new('commentaries', 'Commentaire')->onlyOnForms(),
            AssociationField::new('medias', 'Média')->onlyOnForms(),
            AssociationField::new('user', 'Utilisateur'),
        ];
    }
    
}
