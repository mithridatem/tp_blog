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
use App\Service\FileUploader;
use App\Form\FileUploadType;
//use LDAP\Result;

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
        //récupération du formulaire
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
    //fonction pour afficher toutes les taches
    #[Route('/category/all', name: 'app_category_all')]
    public function getAllCategory(CategoryRepository $repo){
        //récupération de la liste des tâches
        $cats = $repo->findAll();
        //rendu du template twig
        return $this->render('category/allcategory.html.twig',[
                'categories' => $cats, 
        ]);
    }
    #[Route('/category/export', name: 'app_category_export')]
    public function exportCsv(CategoryRepository $repo):Response{
        //récupération de la liste des tâches
        $cats = $repo->findAll();
        foreach ($cats as $cat) {
            //récupération des valeurs
            $data = [$cat->getId(), $cat->getName()];
            //ajout du séparateur ,
            $rows[] = implode(',', $data);
        }
        //création de l'entête
        $head = "ID,Name \n";
        //création de la liste
        $content = $head.implode("\n", $rows);
        //instance de la Reponse
        $response = new Response($content);
        //ajout du header type text/csv
        $response->headers->set('Content-Type', 'text/csv');
        return $response;
    }
    //fonction pour importer des categories depuis un formulaire (fichier csv)
    #[Route('/category/import', name: 'app_category_import')]
    public function importCategoryCsv(Request $request, FileUploader $file_uploader
    , EntityManagerInterface $manager, CategoryRepository $repo)
    {
        //variables pour les messages en TWIG
        $error = "";
        $add = "";
        //formulaire en TWIG
        $form = $this->createForm(FileUploadType::class);
        //récupération de la requête
        $form->handleRequest($request);
        //test si le formulaire est submit et validé
        if($form->isSubmitted() && $form->isValid()){
            //récupération du fichier depuis le formulaire
            $file = $form['upload_file']->getData();
            //test si le fichier à été importé
            if($file){
                $file_name = $file_uploader->upload($file);
                //test si le nom du fichier existe (différent de null)
                if(null !== $file_name){
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
                        if($repo->findOneBy(["name"=>$ligne[1]])== $ligne[1]){
                            //incrémentation des erreurs
                            $error .= ' '.$ligne[1] .' ';
                        }
                        //test si elle n'existe pas 
                        else{
                            //instance d'un nouvel objet
                            $cat = new Category();
                            //set du nom de la catégorie
                            $cat->setName($ligne[1]);
                            //incrémentation des ajouts en BDD
                            $add .= $ligne[1].' ';
                            //on persist les données
                            $manager->persist($cat);
                        }
                    }
                    //on insére les nouvelles catégories en BDD
                    $manager->flush();
                }
                else{
                    return dd("error le fichier n'a pas été importé"); 
                }
            }
        }
        else{
            return $this->render('upload/test-upload.html.twig', [
                'form' => $form->createView(),
                'error' => $error,
                'add' => $add,
            ]);
        }
    }
}
