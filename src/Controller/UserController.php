<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\service\callApiservice;
use callApiservice as GlobalCallApiservice;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
   

    //fonction d'inscription  users
    /**
     * @Route("register",name="registration")
     */
    public function registration(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $encoder){
        $entityManager = $doctrine->getManager();
        $user = new User();
        //création du formulaire du type RegistrationType lié à la classe user
       $form = $this->createForm(RegistrationType::class, $user);
       //le HTTPFOUNDATION va gérer la requéte
       $form->handleRequest($request);
       //vérification validité du formulaire
       if($form->isSubmitted() && $form->isValid()){
        // si le form valider on va hasher le password
        $passwordHash = $encoder->hashPassword($user, $user->getPassword());
        $user->setPassword($passwordHash); // l'affecter à le user
        $entityManager->persist($user); //persister le user à ajouter
        $entityManager->flush();
        return $this->redirectToRoute('login'); // redirection vers login page

       }
       return $this->render('user/registration.html.twig',[
        'form'=> $form->createView() // passation du view du form 
       ]);
    }

    // route et fonction d'authentification
    /**
     * @Route("/login",name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils){
         // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
      
        return $this->render('user/login.html.twig',[
            'error'         => $error,
        ]);
    }

    //route et fonction de déconnexion

    /**
     * @Route("/logout",name="logout")
     */
    public function logout()
    {
    }

}
