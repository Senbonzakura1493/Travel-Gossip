<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use App\Entity\CriticalArticle;
use App\Form\CriticalArticleType;
use App\Entity\Category;
use App\Form\CategoryType;
use APP\Repository\ArticleRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;



class GlobeTrotterAPIController extends AbstractController
{

    /**
     * @Route("api/home", name="api_home",methods={"GET","HEAD"})
     */
    //ok
    public function home()
    {
        return $this->render('globe_trotter/home.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }

    //return all travels
    /**
     * @Route("api/traveling", name="api_traveling",methods={"GET","HEAD"})
     */ 
    public function index()
    {
        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);

        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });

        $encoders = array(new JsonEncoder());
        $normalizers = array($normalizer);
        
        $serializer = new Serializer($normalizers, $encoders);
        

        $criticalArticles = $this->getDoctrine()
                           ->getRepository(CriticalArticle::class)
                           ->findAll();

        if ($criticalArticles != null) {
            $jsonContent = $serializer->serialize($criticalArticles, 'json');

        
            $response->setContent($jsonContent);
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->setStatusCode('200');

            return $response;
        }
        else {
            
            $response->setStatusCode('404');

        }
    }

    //
    /**
     * @Route("api/categories", name="api_Categories",methods={"GET", "HEAD"})
     */
    //ok 
    public function Categories(Request $request , ObjectManager $manager)

    {   
        $normalizer = new ObjectNormalizer();
        $response = new JsonResponse();
        $encoders = array(new JsonEncoder());
        
        $normalizer->setCircularReferenceLimit(1);
        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $normalizers = array($normalizer); 
        $serializer = new Serializer($normalizers, $encoders);
        
        $categories = $this->getDoctrine()
                           ->getRepository(Category::class)
                           ->findAll();
        if ($categories != null) {
            $jsonContent = $serializer->serialize($categories, 'json');

            $response = new JsonResponse();
            $response->setContent($jsonContent);
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->setStatusCode('200');

            return $response;
        }
        else {

            $response->setStatusCode('404');

        }
    }

     
     /**
     * @Route("api/newTravel", name="api_newTravel",methods={"POST", "OPTIONS"})
     
     */ 
    // ok working
    public function newTravel(Request $request , ObjectManager $manager)
    {
        
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $response = new Response();
        $response->headers->set('Access-Control-Allow-Origin', '*'); // pour permettre la redirection vers un path pas de la "same origin"
        $query = array();

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
        {
            $response->headers->set('Content-Type', 'application/text');
            
            $response->headers->set('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type',true);
            return $response;
        }

            $json = $request->getContent();  
            $content = json_decode($json, true); 
            
            if(isset($content['titre']) && isset($content['place_to_visit']) && isset($content['resume']) && isset($content['category']) && isset($content['destination']) && isset($content['date_time'])) 
                {
                    $criticalArticle = new CriticalArticle();
           
                    $category = $this->getDoctrine()
                                ->getRepository(Category::class)
                                ->findOneBy([
                                     'name' => $content["category"]
                                ]);
            
      
                    $criticalArticle->setTitre($content["titre"]);
                    $criticalArticle->setCategory($category);
                    $criticalArticle->setResume($content["resume"]);
                    $criticalArticle->setDestination($content["destination"]);
                    $criticalArticle->setPlaceToVisit($content["place_to_visit"]);
                    $criticalArticle->setDateTime($content["date_time"]);
            
           

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($criticalArticle);
                    $em->flush();
            
                    $query['valid'] = true;
                    $query['data'] = array('titre' => $content["titre"] , 'resume'=>$content["resume"], );
                    
                    
                    $response->setStatusCode('201');
                }
            else 
                {
                    $query['valid'] = false; 
                    $response->setStatusCode('404');
                }        

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($query));
            return $response;     
                
        
    }

    /**
     * @Route("api/editTravel/{id}", name="api_editTravel", methods={"PUT","OPTIONS"})
     */
    //ok working
    public function editTravel(Request $request ,$id)
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $response = new Response();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $query = array();
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
        {
            $response->headers->set('Content-Type', 'application/text');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type',true);
            return $response;
        }

        $json = $request->getContent();  
        $content = json_decode($json, true); 

       
        if($id != null && isset($content['titre']) && isset($content['place_to_visit']) && isset($content['resume']) && isset($content['category']) && isset($content['destination']) && isset($content['date_time'])) 
                {
                    
                    $criticalArticle = $this->getDoctrine()
                                ->getRepository(CriticalArticle::class)
                                ->find($id);
           
                    $category = $this->getDoctrine()
                                ->getRepository(Category::class)
                                ->findOneBy([
                                     'name' => $content["category"]
                                ]);
            
      
                    $criticalArticle->setTitre($content["titre"]);
                    $criticalArticle->setCategory($category);
                    $criticalArticle->setResume($content["resume"]);
                    $criticalArticle->setDestination($content["destination"]);
                    $criticalArticle->setPlaceToVisit($content["place_to_visit"]);
                    $criticalArticle->setDateTime($content["date_time"]);
            
           

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($criticalArticle);
                    $em->flush();
            
                    
                    $query['valid'] = true; 
                    
                    $response->setStatusCode('201');
                }
            else 
                {
                    
                    $query['valid'] = false; 
                    $response->setStatusCode('404');
                }        

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($query));
            return $response;
    }

    /**

     * @Route("/api/deleteTravel/{id}", name="api_deleteTravel", methods={"DELETE", "OPTIONS"})

     */

    public function deleteTravel($id=null)

    {
        $response = new Response();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
        {
            $response = new Response();
            $response->headers->set('Content-Type', 'application/text');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type',true);
            return $response;
        }
        if ($id != null) {

            $em = $this->getdoctrine()->getManager();
            $travel = $em->getRepository(CriticalArticle::class)->find($id);
            $em->remove($travel);
            $em->flush();
            $query['valid'] = true;
            $response->setStatusCode('200');
        }
        else
        {
            $query['valid'] = false;
            $response->setStatusCode('404');
        }
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($query));
        return $response;
    }

    /**
     * @Route("api/newCategory", name="api_newCategory",methods={"POST", "OPTIONS"})
     
     */ 
    // ok working
    public function newCategory(Request $request , ObjectManager $manager)
    {
        $normalizer = new ObjectNormalizer();
        $encoders = array(new JsonEncoder());
        $normalizers = array($normalizer);
        $normalizer->setCircularReferenceLimit(1);
        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer($normalizers, $encoders);
        $response = new Response();
        $response->headers->set('Access-Control-Allow-Origin', '*'); // pour permettre la redirection vers un path pas de la "same origin"
        $query = array();
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
        {
            $response->headers->set('Content-Type', 'application/text');           
            $response->headers->set('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type',true);
            return $response;
        }

            $json = $request->getContent();  
            $content = json_decode($json, true); 
            
            if(isset($content['name'])) 
                {
                    $category = new Category();
                            
                    $category->setName($content["name"]);
                  
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($category);
                    $em->flush();
                    
                    $query['data'] = array('name' => $content["name"] );
                    $query['valid'] = true; 
                    
                    $response->setStatusCode('201');
                }
            else 
                {
                    $query['valid'] = false; 
                    $query['data'] = null;
                    $response->setStatusCode('404');
                }        

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($query));
            return $response;     
                
        
    }

    /**
     * @Route("api/editCategory/{id}", name="api_editCategory", methods={"PUT","OPTIONS"})
     */
    //ok working
    public function editCategory(Request $request ,$id)
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $response = new Response();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $query = array();
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
        {
            $response->headers->set('Content-Type', 'application/text');
            
            $response->headers->set('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type',true);
            return $response;
        }

        $json = $request->getContent();  
        $content = json_decode($json, true); 

        
        if($id != null && isset($content['name'])) 
                {          
                    $category =$this->getDoctrine()
                                    ->getRepository(Category::class)
                                    ->find($id);

                    $category->setName($content['name']);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($category);
                    $em->flush();
            
                    $query['valid'] = true; 
                    
                    $response->setStatusCode('201');
                }
            else 
                {
                    echo('there is a problem here 2');
                    $query['valid'] = false; 
                    $response->setStatusCode('404');
                }        

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($query));
            return $response;
    }
    
        /**
     * @Route("/api/category/{id}", name="api_category", methods={"GET"})
     */
    public function getCategory($id)
    {
        $response = new Response();
        $normalizer = new ObjectNormalizer();
        $encoders = array(new JsonEncoder());
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $normalizer->setCircularReferenceLimit(1);
        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $normalizers = array($normalizer);

        $serializer = new Serializer($normalizers, $encoders);
        if ($id!= null) {
            $category = $this->getDoctrine()
                           ->getRepository(Category::class)
                           ->find($id);
            
            if ($category != null) {
                $jsonContent = $serializer->serialize($category, 'json');
    
                $response->setContent($jsonContent);
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode('200');
            }
            else {
                $response->setStatusCode('404');
            }
        }        
        else {
            $response->setStatusCode('404');
        }
        return $response;
    }

        /**
     * @Route("/api/travel/{id}", name="api_travel", methods={"GET"})
     */
    public function getTravel($id)
    {

        $response = new Response();
        $normalizer = new ObjectNormalizer();
        $encoders = array(new JsonEncoder());
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $normalizer->setCircularReferenceLimit(1);
        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $normalizers = array($normalizer);

        $serializer = new Serializer($normalizers, $encoders);

        $query = array();
        
        if ($id!= null) {
            $criticalArticle = $this->getDoctrine()
                           ->getRepository(CriticalArticle::class)
                           ->find($id);
            
            if ($criticalArticle != null) {
                $jsonContent = $serializer->serialize($criticalArticle, 'json');
    
                $response->setContent($jsonContent);
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode('200');
            }
            else {
                $response->setStatusCode('404');
            }
        }        
        else {
            $response->setStatusCode('404');
        }
        return $response;
    }
        


}
