<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    //fonction pour ajouter un compte utilisateur
    #[Route('/register', name: 'app_register')]
    public function addUser(Request $request, 
    EntityManagerInterface $manager,UserPasswordHasherInterface $hash): Response
    {   
        //variable qui contient un objet User
        $user = new User();
        //variable qui contient un objet UserType (formulaire)
        $form = $this->createForm(UserType::class, $user);
        //stocker le résultat du formulaire
        $form->handleRequest($request);
        //condition validation du formulaire
        if($form->isSubmitted()){
            //récupére le mot de passe en clair
            $pass = $_POST['user']['password'];
            //hasher le mot de passe
            $hassPassword = $hash->hashPassword($user, $pass);
            //setter les valeurs (mot de passe activation et le role)
            $user->setPassword($hassPassword);
            $user->setRoles(['ROLE_USER']);
            $user->setActivated(1);
            //sauvegarder les données
            $manager->persist($user);
            //ajout en BDD
            $manager->flush();
            //rediriger vers la meme page
            return $this->redirectToRoute('app_register');
        }
        //génération du formulaire
        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
