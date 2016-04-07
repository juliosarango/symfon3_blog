<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use BlogBundle\Entity\Entry;
use BlogBundle\Form\EntryType;
use BlogBundle\Repository\EntryRepository;

class EntryController extends Controller {
    
    private $session;
    
    public function __construct() {
        $this->session = new Session();
    }
    
    public function indexAction(){
        
        $em = $this->getDoctrine()->getManager();
        $repo_entry = $em->getRepository("BlogBundle:Entry");
        $repo_category = $em->getRepository("BlogBundle:Category");
        
        $entries = $repo_entry->findAll();
        $categories = $repo_category->findAll();
        
        return $this->render("BlogBundle:Entry:index.html.twig", array("entries"=>$entries, "categories"=>$categories));
    }
    
    public function addAction(Request $request) {
        
        $entry = new Entry();
        $form = $this->createForm(EntryType::class, $entry);

        $form->handleRequest($request);
        $creacion = false;

        if ($form->isSubmitted()) 
        {            
            if ($form->isValid()) 
            {
                $em = $this->getDoctrine()->getManager();
                
                $entry->setTitle($form->get("title")->getData());
                $entry->setContent($form->get("content")->getData());
                
                $file = $form["image"]->getData();
                $ext = $file->guessExtension();
                $file_name = time().".".$ext;
                $file->move("uploads",$file_name);
                
                $entry->setImage($file_name);
                
                $category_repo = $em->getRepository("BlogBundle:Category");
                $entry_repo = $em->getRepository("BlogBundle:Entry");
                $category = $category_repo->find($form->get("category")->getData());
                                
                $entry->setCategory($category);
                
                $user = $this->getUser();
                
                $entry->setUser($user);
                
                $em->persist($entry);                                                                
                $flush = $em->flush();
                
                $entry_repo->saveEntryTags(
                        $form->get("tags")->getData(),
                        $form->get("title")->getData(),
                        $category,
                        $user);
                
                if ($flush == null)
                {
                    $creacion = true;
                    $status["message"] = "Entrada creada correctamente";
                    $status["class"] = "alert alert-success";
                }
                else
                {
                    $status["message"] = "La entrada no se ha creado";
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
                return $this->redirect ($this->generateUrl ("blog_homepage"));
            }
        }                

        return $this->render("BlogBundle:Entry:add.html.twig", array("form" => $form->createView()));
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
                $categoies->setName($form->get("description")->getData());
                
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
    
}
