<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FileUploader;
use App\Form\FileUploadType;
use Symfony\Component\Routing\Annotation\Route;
class UploadController extends AbstractController
{
  //fonction pour ajouter importer un fichier depuis un formulaire
  #[Route('/import/csv', name: 'app_import_csv')]
  public function excelCommunesAction(Request $request, FileUploader $file_uploader)
  {
    $form = $this->createForm(FileUploadType::class);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) 
    {
      $file = $form['upload_file']->getData();
      if ($file) 
      {
        $file_name = $file_uploader->upload($file);
        if (null !== $file_name) // for example
        {
          $directory = $file_uploader->getTargetDirectory();
          $full_path = $directory.'/'.$file_name;
          
          //Nom du fichier à ouvrir
          $fichier = file($full_path);
          for($i = 0; $i < count($fichier); $i++) {
            //on explode la ligne avec le séparateur
            $ligne = explode(",", $fichier[$i]);
            echo $ligne[0];
          }
          // Do what you want with the full path file...
          // Why not read the content or parse it !!!
        }
        else
        {
          // Oups, an error occured !!!
        }
      }
    }
    return $this->render('upload/test-upload.html.twig', [
      'form' => $form->createView(),
    ]);
  }
  // ...
}
?>