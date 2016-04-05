<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BlogBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of User
 *
 * @author jsarangoq
 */
class UserController extends Controller {
    
    public function loginAction(Request $request){
        
        $authenticationUtils = $this->get("security.authentication_utils");
        
        $error = $authenticationUtils->getLastAuthenticationError();        
        $lastUsername = $authenticationUtils->getLastUsername();        
        return $this->render("BlogBundle:user:login.html.twig", array("error"=>$error, "last_username" => $lastUsername));
        
    }
    
    
}
