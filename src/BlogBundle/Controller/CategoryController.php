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
    
    public function deleteAction($id){
        
        $em = $this->getDoctrine()->getManager();
        $category_repo = $em->getRepository("BlogBundle:Category");
        $categoies = $category_repo->find($id);
        
        if ($categoies != null)
        {            
            if (count($categoies->getEntries()) == 0)
            {
                $em->remove($categoies);
                $flush = $em->flush();

                if ($flush == null)
                {                
                    $status["message"] = "Categoría eliminada correctamente";
                    $status["class"] = "alert alert-success";
                }
                else
                {
                    $status["message"] = "La Categoría no se ha eliminado";
                    $status["class"] = "alert alert-danger";
                }
            }
            else
            {
                $status["message"] = "La Categoría está relacionada con una entrada existente, no se puede eliminar";
                $status["class"] = "alert alert-danger";
            }
        }
        else
        {
            $status["message"] = "La Categoría no se ha encontrado";
            $status["class"] = "alert alert-danger";
        }
        $this->session->getFlashBag()->add("status", $status);
        
        return $this->redirect($this->generateUrl("blog_index_category"))                                     ;
    }
    
    public function editAction(Request $request, $id){
        
        $em = $this->getDoctrine()->getManager();
        $category_repo = $em->getRepository("BlogBundle:Category");
        $categoies = $category_repo->find($id);
        
        $form = $this->createForm(CategoryType::class, $categoies);
        $form->handleRequest($request);
        if ($form->isSubmitted())
        {
            if ($form->isValid())
            {                                
                $categoies->setName($form->get("name")->getData());
                $categoies->setDescription($form->get("description")->getData());
                
                $em->persist($categoies);
                $flush = $em->flush();
                if ($flush == null)
                {                
                    $status["message"] = "Categoría editada correctamente";
                    $status["class"] = "alert alert-success";
                }
                else
                {
                    $status["message"] = "La Categoría no se ha editado";
                    $status["class"] = "alert alert-danger";
                }                
            }
            else
            {
                $status["message"] = "Error en las validaciones del formulario";
                $status["class"] = "alert alert-danger";                
            }
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirect($this->generateUrl("blog_index_category"));
        }
        
        return $this->render("BlogBundle:Category:edit.html.twig",array("form"=>$form->createView()));
    }
    
    public function categoryAction($id, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $category_repo = $em->getRepository("BlogBundle:Category");
        $category = $category_repo->find($id);
        
        $categories = $category_repo->findAll();
        
        $entry_repo = $em->getRepository("BlogBundle:Entry");
        $pageSize = 5;
        $entries = $entry_repo->getCategoryEntries($category,$pageSize, $page);       
        
        $totalItems = count($entries);       
        $pageCount = ceil($totalItems/$pageSize);
        
        return $this->render("BlogBundle:Category:category.html.twig",
                array(
                    "category" => $category,
                    "categories" => $categories,
                    "entries" => $entries,
                    "totalItems" => $totalItems,
                    "pagesCount" => $pageCount,
                    "page" => $page,
                    "page_m" => $page
                    ));
        
    }
    
}
