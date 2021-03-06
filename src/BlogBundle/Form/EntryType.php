<?php

namespace BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EntryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class, array(
                "label"=>"Titulo", 
                "required"=>"required", 
                "attr"=>array("class"=>"form-control")))
                
            ->add('content', TextareaType::class,array(
                "label"=>"Contenido", 
                "required"=>"required", 
                "attr"=>array("class"=>"form-control")))
                
            ->add('status', ChoiceType::class,array(
                "label"=>"Estado", 
                "required"=>"required", 
                "choices"=> array("Publicado"=>"public","Privado"=>"private"),
                "attr"=>array("class"=>"form-control")))
                
            ->add('image', FileType::class,array(
                "label"=>"Imagen", 
//                "required"=>"required", 
                "attr"=>array("class"=>"form-control"),
                "data_class" => null,
                "required" => false ))
                
                
//            ->add('user', EntityType::class,array(
//                "label"=>"Descripción", 
//                "required"=>"required", 
//                "attr"=>array("class"=>"form-control")))
                
            ->add('category', EntityType::class,array(
                "class"=> "BlogBundle:Category",
                "label"=>"Categoría",                 
                "attr"=>array("class"=>"form-control")))
                
            ->add('tags', TextType::class,array(
                "mapped" => false,
                "label"=>"Tags", 
                "required"=>"required", 
                "attr"=>array("class"=>"form-control")))  
                
            ->add('Grabar', SubmitType::class, array(
                "attr"=>array("class"=>"btn btn-primary")))      
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BlogBundle\Entity\Entry'
        ));
    }
}
