<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexOld()
    {
//        $em = $this->getDoctrine()->getManager();
//        $entry_repo = $em->getRepository("BlogBundle:Entry");
//        $entries = $entry_repo->findAll();
//        foreach ($entries as $entry)
//        {
//            echo $entry->getTitle() ." === " . $entry->getCategory()->getName() ." === ". $entry->getUser()->getName().  "<br/>";
//            
//            $tags = $entry->getEntryTag();
//            foreach ($tags as $tag)
//            {
//                echo $tag->getTag()->getName() ." ";
//                
//            } 
//            
//            echo "<hr/>";
//        }        
//        die();
        
       /* $em = $this->getDoctrine()->getManager();
        $category_repo = $em->getRepository("BlogBundle:Category");
        $categories = $category_repo->findAll();       
                        
        foreach ($categories as $category)
        {
            echo $category->getName() . "<br/>";                       
            
            $entries = $category->getEntries();            
            
            foreach ($entries as $entry)
            {                
                echo $entry->getTitle() . "<br/>";
                
                
            }
            echo "<hr/>";
        }
        
//        dump($categories);
        
        die();*/
        
        $em = $this->getDoctrine()->getManager();
        $tag_repo = $em->getRepository("BlogBundle:Tag");
        $tags = $tag_repo->findAll();
        
        foreach ($tags as $tag)
        {
            echo $tag->getName() . "<br/>";
            
            $entryTag = $tag->getEntryTag();
            
            foreach ($entryTag as $entry)
            {
                echo $entry->getEntry()->getTitle() . "<br/>";
            }
            //dump($entryTag);
            
            echo "<hr/>";
        }
        
        die();
        
        return $this->render('BlogBundle:Default:index.html.twig');
    }
    
    public function indexAction()
    {
        return $this->render('BlogBundle:Default:index.html.twig');
        
    }
}
