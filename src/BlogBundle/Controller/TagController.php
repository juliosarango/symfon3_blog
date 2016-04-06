<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use BlogBundle\Form\TagType;
use BlogBundle\Entity\Tag;

class TagController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }
    
    public function indexAction(){
        
        $em = $this->getDoctrine()->getManager();
        $repo_tag = $em->getRepository("BlogBundle:Tag");
        $tags = $repo_tag->findAll();
        return $this->render("BlogBundle:Tag:index.html.twig", array("tags"=>$tags));
    }

    public function addAction(Request $request) {
        $tag = new Tag();

        $form = $this->createForm(TagType::class, $tag);

        $form->handleRequest($request);
        $creacion = false;

        if ($form->isSubmitted()) 
        {            
            if ($form->isValid()) 
            {
                $em = $this->getDoctrine()->getManager();
                
                $tag->setName($form->get("name")->getData());
                $tag->setDescription($form->get("description")->getData());
                                
                $em->persist($tag);
                $flush = $em->flush();
                
                if ($flush == null)
                {
                    $creacion = true;
                    $status["message"] = "Etiqueta creada correctamente";
                    $status["class"] = "alert alert-success";
                }
                else
                {
                    $status["message"] = "La Etiqueta no se ha creado";
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
                return $this->redirect ($this->generateUrl ("blog_index_tag"));
            }
        }                

        return $this->render("BlogBundle:Tag:add.html.twig", array("form" => $form->createView()));
    }
    
    public function deleteAction($id){
        
        $em = $this->getDoctrine()->getManager();
        $tag_repo = $em->getRepository("BlogBundle:Tag");
        $tag = $tag_repo->find($id);
        
        if ($tag != null)
        {            
            if (count($tag->getEntryTag()) == 0)
            {
                $em->remove($tag);
                $flush = $em->flush();

                if ($flush == null)
                {                
                    $status["message"] = "Etiqueta eliminada correctamente";
                    $status["class"] = "alert alert-success";
                }
                else
                {
                    $status["message"] = "La Etiqueta no se ha eliminado";
                    $status["class"] = "alert alert-danger";
                }
            }
            else
            {
                $status["message"] = "La Etiqueta está relacionada con una entrada existente, no se puede eliminar";
                $status["class"] = "alert alert-danger";
            }
        }
        else
        {
            $status["message"] = "La Etiqueta no se ha encontrado";
            $status["class"] = "alert alert-danger";
        }
        $this->session->getFlashBag()->add("status", $status);
        
        return $this->redirect($this->generateUrl("blog_index_tag"))                                     ;
    }
            

}
