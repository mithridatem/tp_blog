<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CategoryType;
use App\Entity\Category;
use App\Repository\CategoryRepository;

use Doctrine\ORM\EntityManagerInterface;

class CategoryController extends AbstractController
{
    //fonction pour ajouter une catégorie depuis le formulaire
    #[Route('/category/add', name: 'app_category_add')]
    public function addCategory(Request $request, 
    EntityManagerInterface $manager, CategoryRepository $repo): Response
    {
        //variable pour stocker mon objet Category
        $cat = new Category();
        //variable pour stocker une instance de mon formulaire
        $form = $this->createForm(CategoryType::class, $cat);
        $form->handleRequest($request);
        //tester si le formulaire à été submit
        if($form->isSubmitted()){
            //on stocke l'enregistement de la catégorie si elle existe
            $test = $repo->findOneBy(['name'=> $cat->getName()]);
            //test si elle n'existe pas 
            if($test == null){
                //on sauvegarde
                $manager->persist($cat);
                //ajouter en BDD
                $manager->flush();
                //redirection vers la page de création
                return $this->render('category/index.html.twig', [
                    'form' => $form->createView(),
                    'resultat' => 'ok'
                ]);
            }
            //si elle existe on redirige vers la page de création
            else{
                return $this->render('category/index.html.twig', [
                    'form' => $form->createView(),
                    'resultat' => 'erreur'
                ]);
            }
        }
        //au chargement de la page
        return $this->render('category/index.html.twig', [
            'form' => $form->createView(),
            'resultat' => ''
        ]);
    }
}
