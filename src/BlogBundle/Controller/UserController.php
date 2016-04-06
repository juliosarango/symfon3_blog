<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BlogBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BlogBundle\Entity\Users;
use BlogBundle\Form\UsersType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Description of User
 *
 * @author jsarangoq
 */
class UserController extends Controller {
    
    private $session;
    
    public function __construct() {
        $this->session = new Session();
    }

        public function loginAction(Request $request){
        
        $authenticationUtils = $this->get("security.authentication_utils");        
        $error = $authenticationUtils->getLastAuthenticationError();        
        $lastUsername = $authenticationUtils->getLastUsername();        
        
        $user = new Users();
        
        $form = $this->createForm(UsersType::class, $user);
        
        $form->handleRequest($request);
        if ($form->isSubmitted())
        {
            if ($form->isValid()){
                
                $em = $this->getDoctrine()->getManager();
                $repo_user = $em->getRepository("BlogBundle:Users");
                $users = $repo_user->findOneBy(array("email"=>$form->get("email")->getData()));
                
                if (count($users) == 0)
                {                    
                    $user->setName($form->get("name")->getData());
                    $user->setSurname($form->get("surname")->getData());
                    $user->setEmail($form->get("email")->getData());

                    $factory = $this->get("security.encoder_factory");

                    $enconder = $factory->getEncoder($user);                
                    $password = $enconder->encodePassword($form->get("password")->getData(),$user->getSalt());                

                    $user->setPassword($password);
                    $user->setRole("ROLE_USER");
                    $user->setImagen(null);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $flush = $em->flush();
                    if ($flush == null)
                        $status = "El usuario se ha creado correctamente";
                    else
                        $status = "El usuario NO se ha creado correctamente";
                }
                else
                {
                    $status = "El usuario ya existe";
                }
            }
            else{
                $status = "No te has registrado corectamente";
            }

            $this->session->getFlashBag()->add("status", $status);
        }
                                               
        return $this->render("BlogBundle:user:login.html.twig", 
                array(
                    "error"=>$error, 
                    "last_username" => $lastUsername,                   
                    "form"=>$form->createView()
                ));
        
    }
    
    
}
