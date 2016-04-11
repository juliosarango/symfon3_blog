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
    
    public function indexAction(Request $request,  $page){
                        
        $em = $this->getDoctrine()->getManager();
        $repo_entry = $em->getRepository("BlogBundle:Entry");
        $repo_category = $em->getRepository("BlogBundle:Category");
        
        $categories = $repo_category->findAll();
        
        $pageSize = 5;
        $entries = $repo_entry->getPaginateEntries($pageSize, $page);
        
        $totalItems = count($entries);
        $pageCount = ceil($totalItems/$pageSize);
        
        
        
        return $this->render("BlogBundle:Entry:index.html.twig", 
                array(
                    "entries" => $entries, 
                    "categories" => $categories,
                    "totalItems" => $totalItems,
                    "pagesCount" => $pageCount,
                    "page" => $page,
                    "page_m" => $page
                
                    ));
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
                if (!empty($file) && $file != null){
                    $ext = $file->guessExtension();
                    $file_name = time().".".$ext;
                    $file->move("uploads",$file_name);

                    $entry->setImage($file_name);
                }
                else{
                    $entry->setImage(null);
                }
                
                
                
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
        $category_repo = $em->getRepository("BlogBundle:Entry");
        $entry_tag_repo = $em->getRepository("BlogBundle:EntryTag");
        
        $entry = $category_repo->find($id);
                        
        if ($entry != null && is_object($entry))
        {
            $entry_tags = $entry_tag_repo->findBy(array("entry"=>$entry));
        
            foreach ($entry_tags as $et){
                if (is_object($et)){                    
                    $em->remove($et);
                    $em->flush();
                }
            }
            
            $em->remove($entry);
            $flush = $em->flush();

            if ($flush == null)
            {                
                $status["message"] = "Entrada eliminada correctamente";
                $status["class"] = "alert alert-success";
            }
            else
            {
                $status["message"] = "La entrada no se ha eliminado";
                $status["class"] = "alert alert-danger";
            }
            
        }
        else
        {
            $status["message"] = "La Entrada no se ha encontrado";
            $status["class"] = "alert alert-danger";
        }
        $this->session->getFlashBag()->add("status", $status);
        
        return $this->redirect($this->generateUrl("blog_homepage"))                                     ;
    }
    
    public function editAction(Request $request, $id){
        
        $em = $this->getDoctrine()->getManager();
        $entry_repo = $em->getRepository("BlogBundle:Entry");
        $entry = $entry_repo->find($id);
        
        $entry_image = $entry->getImage();
        
        $tags = "";
        foreach ($entry->getEntryTag() as $entryTag){
            $tags .= $entryTag->getTag()->getName() . ",";
        }
            
        
        $form = $this->createForm(EntryType::class, $entry);
        $form->handleRequest($request);
        if ($form->isSubmitted())
        {
            if ($form->isValid())
            {
                $entry->setTitle($form->get("title")->getData());
                $entry->setContent($form->get("content")->getData());
                
                $file = $form["image"]->getData();
                
                if (!empty($file) && $file != null){
                    $ext = $file->guessExtension();
                    $file_name = time().".".$ext;
                    $file->move("uploads",$file_name);

                    $entry->setImage($file_name);
                }
                else {
                    $entry->setImage($entry_image);
                }
                
                $category_repo = $em->getRepository("BlogBundle:Category");
                $entry_repo = $em->getRepository("BlogBundle:Entry");
                $category = $category_repo->find($form->get("category")->getData());
                                
                $entry->setCategory($category);
                
                $user = $this->getUser();
                
                $entry->setUser($user);
                
                $em->persist($entry);                                                                
                $flush = $em->flush();
                
                $entry_tag_repo = $em->getRepository("BlogBundle:EntryTag");
                $entry_tags = $entry_tag_repo->findBy(array("entry"=>$entry));
        
                foreach ($entry_tags as $et){
                    if (is_object($et)){                    
                        $em->remove($et);
                        $em->flush();
                    }
                }
                
                $entry_repo->saveEntryTags(
                        $form->get("tags")->getData(),
                        $form->get("title")->getData(),
                        $category,
                        $user);
                
                if ($flush == null)
                {
                    $creacion = true;
                    $status["message"] = "Entrada editada correctamente";
                    $status["class"] = "alert alert-success";
                }
                else
                {
                    $status["message"] = "La entrada no se ha editado";
                    $status["class"] = "alert alert-danger";
                }                
            }
            else
            {
                $status["message"] = "Error en las validaciones del formulario";
                $status["class"] = "alert alert-danger";                
            }
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirect($this->generateUrl("blog_homepage"));
        }
        
        return $this->render("BlogBundle:Entry:edit.html.twig",array("form"=>$form->createView(), "entry"=>$entry, "tags"=>$tags));
    }
    
}
