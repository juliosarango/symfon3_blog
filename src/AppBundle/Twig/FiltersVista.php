<?php

namespace AppBundle\Twig;

class FiltersVista extends \Twig_Extension {
    
    public function getName() {        
        return "filter_vista";        
    }
    
    public function getFilters() {
        return array(
            new \Twig_SimpleFilter("addText", array($this,"addText"))
        );
    }
    
    public function addText($string)
    {
        return $string . " TEXTO CONCATENADO";
        
    }


}
