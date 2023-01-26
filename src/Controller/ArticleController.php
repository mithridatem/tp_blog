<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ArticleType;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\FileUploader;
use App\Form\FileUploadType;

class ArticleController extends AbstractController
{

    //fonction pour afficher toutes les taches
    #[Route('/article/all', name: 'app_article_all')]
    public function getAllCategory(ArticleRepository $repo){
        //récupération de la liste des tâches
        $cats = $repo->findAll();
        //rendu du template twig
        return $this->render('category/allcategory.html.twig',[
                'categories' => $cats, 
        ]);
    }
    //fonction pour ajouter importer des categories depuis un formulaire (fichier csv)
    #[Route('/articcle/import', name: 'app_article_import')]
    public function importCategoryCsv(Request $request, FileUploader $file_uploader
    , EntityManagerInterface $manager, ArticleRepository $repo)
    {
        //variables pour les messages en TWIG
        $error = "";
        $add = "";
        //formulaire en TWIG
        $form = $this->createForm(FileUploadType::class);
        //récupération de la requête
        $form->handleRequest($request);
        //test si le formulaire est submit et validé
        if ($form->isSubmitted() && $form->isValid()) 
        {
            //récupération du fichier depuis le formulaire
            $file = $form['upload_file']->getData();
            //test si le fichier à été importé
            if ($file) 
            {
                $file_name = $file_uploader->upload($file);
                //test si le nom du fichier existe (différent de null)
                if (null !== $file_name) // for example
                {
                    //récupération du répertoire pour sauvegarder le fichier
                    $directory = $file_uploader->getTargetDirectory();
                    //récupération du chemin du fichier
                    $full_path = $directory.'/'.$file_name;
                    //ouverture du fichier importé
                    $fichier = file($full_path);
                    //parcour du fichier ligne par ligne
                    for($i = 0; $i < count($fichier); $i++) {
                        //on explode la ligne avec le séparateur ,
                        $ligne = explode(",", $fichier[$i]);
                        //test si la categorie existe déja
                        if($repo->findOneBy(["name"=>$ligne[1]])){
                            //incrémentation des erreurs
                            $error .= ' '.$ligne[1] .' ';
                        }
                        //test si elle n'existe pas 
                        else{
                            //instance d'un nouvel objet
                            $art = new Article();
                            //set du titre de l'article
                            $art->setTitle($ligne[1]);
                            $art->setContent($ligne[2]);
                            //$art->setDate(date($ligne[2]));
                            

                            //incrémentation des ajouts en BDD
                            $add .= $ligne[1].' ';
                            //on persist les données
                            $manager->persist($art);
                        }
                    }
                    //on insére les nouvelles catégories en BDD
                    $manager->flush();
                }
                else
                {
                // Oups, an error occured !!!
                }
            }
        }
        return $this->render('upload/test-upload.html.twig', [
        'form' => $form->createView(),
        'error' => $error,
        'add' => $add,
        ]);
    }
}
