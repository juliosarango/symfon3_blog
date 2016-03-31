<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Curso;
use AppBundle\Form\CursoType;
use Symfony\Component\Validator\Constraints;

class PruebasController extends Controller 
{        
    public function indexAction(Request $request, $nombre,$page)
    {
        //formas de acceder a la base del url
        //$var = $this->container->get("router")->getContext()->getBaseURL();
//        $var = $request->getBaseUrl();
        //dump($var);
        
        $param = $request->query->get("hola");
//        dump($param);
//        dump($request->get("hola-post"));
//        die();
//        return $this->redirect($this->generateUrl("homepage"));
        
        return $this->render('AppBundle:Pruebas:index.html.twig',
                array("mensaje"=>"Enviado desde el controlador","nombre"=>$nombre,"apellido"=>$page));
    }
    
    public function createAction()
    {
        $curso = new Curso();
        $curso->setTitulo("curso de symfony 3");
        $curso->setDescripcion("curso completo de symfony3");
        $curso->setPrecio(80.50);
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($curso);
        $flush = $em->flush();
        
        if ($flush != null)
            echo "Error al isertar datos";
        else {
            echo "inserción correcta";
        }
        die();                
    }
    
    public function readAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $cursos_repo = $em->getRepository("AppBundle:Curso");
        $cursos = $cursos_repo->findAll();
        dump($cursos);
        foreach ($cursos as $curso)
        {
            echo $curso->getTitulo() . "<br/>";
            echo $curso->getDescripcion() . "<br/>";
            echo $curso->getPrecio() . "<br/><hr/>";
        }
        die();
    }
    
    public function updateAction($id,$titulo,$descripcion,$precio)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $cursos_repo = $em->getRepository("AppBundle:Curso");
        
        $curso = $cursos_repo->find($id);
        
        $curso->setTitulo($titulo);
        $curso->setDescripcion($descripcion);
        $curso->setPrecio($precio);
        
        $em->persist($curso);
        $flush = $em->flush();
        
        if ($flush != null)
            echo "Error al actualizar datos";
        else {
            echo "actualizacion correcta";
        }
        
        die();
    }
    
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $curso_repo = $em->getRepository("AppBundle:Curso");
        $curso = $curso_repo->find($id);
        
        $em->remove($curso);
        $flush = $em->flush();
        
        if ($flush != null)
            echo "Error al eliminar datos";
        else {
            echo "eliminacion correcta";
        }
        
        die();
        
    }
    
    public function nativeSqlAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $db = $em->getConnection();
        
        $query = "select * from curso";
        $stmt = $db->prepare($query);
        $params = array();
        $stmt->execute($params);
        
        $cursos = $stmt->fetchAll();
        foreach ($cursos as $curso)
        {
            echo $curso['titulo'] . "<br/>";
            echo $curso['descripcion'] . "<br/>";
            echo $curso['precio'] . "<br/><hr/>";
        }
        die();
    }
    
    public function dqlAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $query = $em->createQuery("select c from AppBundle:Curso c where c.precio > :precio");
        $query->setParameter("precio", 80);
        
        $cursos = $query->getResult();
        
        foreach ($cursos as $curso)
        {
            echo $curso->getTitulo() . "<br/>";
            echo $curso->getDescripcion() . "<br/>";
            echo $curso->getPrecio() . "<br/><hr/>";
        }
        die();
    }
    
    public function queryBuilderAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $cursos_repo = $em->getRepository("AppBundle:Curso");
        
//        $query = $cursos_repo->createQueryBuilder("c")
//                ->where("c.precio > :precio")
//                ->setParameter("precio", 120)
//                ->getQuery();
//        
//        $cursos = $query->getResult();
        //llamamos al metodo escrito en el repositorio
        
        $cursos =  $cursos_repo->getCursos();
        
        foreach ($cursos as $curso)
        {
            echo $curso->getTitulo() . "<br/>";
            echo $curso->getDescripcion() . "<br/>";
            echo $curso->getPrecio() . "<br/><hr/>";
        }
        die();                       
    }
    
    public function formAction(Request $request){
        
        $curso = new Curso();
        $form = $this->createForm(CursoType::class, $curso);
        
        $form->handleRequest($request);
        
        $status = null;
        $data = null;
        if ($form->isValid())
        {
            $status = "Formulario válido";
            $data = array( "titulo" => $form->get("titulo")->getData(),
                    "descripcion" => $form->get("descripcion")->getData(),
                    "precio" => $form->get("precio")->getData()
                    );
        }
                                                
        return $this->render('AppBundle:Pruebas:form.html.twig',
                array('form'=>$form->createView(),
                        'status' => $status,
                        'data' => $data));
        
    }
    
    public function validarEmailAction($email)
    {
        
        $emailConstraint = new Constraints\Email();
        $emailConstraint->message = "Ingrese correctamente el email";
        
        $error = $this->get("validator")->validate($email, $emailConstraint);                
        var_dump($error);
        
        if (count($error) == 0)
            echo "email correcto";
        else
            echo $error[0]->getMessage();
        die();
        
        
    }
    
            
    
}
