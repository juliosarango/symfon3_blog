<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use BlogBundle\Entity\Category;
use BlogBundle\Form\CategoryType;

class CategoryController extends Controller {
    
    private $session;
    
    public function __construct() {
        $this->session = new Session();
    }
    
    public function indexAction(){
        
        $em = $this->getDoctrine()->getManager();
        $repo_category = $em->getRepository("BlogBundle:Category");
        $categories = $repo_category->findAll();
        
        return $this->render("BlogBundle:Category:index.html.twig", array("categories"=>$categories));
    }
    
    public function addAction(Request $request) {
        
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        $creacion = false;

        if ($form->isSubmitted()) 
        {            
            if ($form->isValid()) 
            {
                $em = $this->getDoctrine()->getManager();
                
                $category->setName($form->get("name")->getData());
                $category->setDescription($form->get("description")->getData());
                                
                $em->persist($category);
                $flush = $em->flush();
                
                if ($flush == null)
                {
                    $creacion = true;
                    $status["message"] = "Categoria creada correctamente";
                    $status["class"] = "alert alert-success";
                }
                else
                {
                    $status["message"] = "La Categoría no se ha creado";
                    $status["class"] = "alert alert-danger";
                }
            }
            else 
            {
                $status["message"] = "Error de validación de datos";
                $status["class"] = "alert alert-danger";
            }
            
            $this->session->getFlashBag()->add("status", $status);
            if ($creacion)
            {
                return $this->redirect ($this->generateUrl ("blog_index_category"));
            }
        }                

        return $this->render("BlogBundle:Category:add.html.twig", array("form" => $form->createView()));
    }
    
}
