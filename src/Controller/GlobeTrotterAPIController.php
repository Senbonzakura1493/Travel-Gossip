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
use App\Entity\CriticalArticle;
use App\Form\CriticalArticleType;
use App\Entity\Category;
use App\Form\CategoryType;
use APP\Repository\ArticleRepository;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;





class GlobeTrotterAPIController extends AbstractController
{
    //ok 
    /**
     * @Route("api/traveling", name="api_traveling",methods={"GET","HEAD"})
     */
    public function index()
    {
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

        $jsonContent = $serializer->serialize($criticalArticles, 'json');

        $response = new JsonResponse();
        $response->setContent($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode('302');

        return $response;
        
    }

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
    /**
     * @Route("api/Category", name="api_Category",methods={"GET", "HEAD"})
     */
    //ok 
    public function Category(Request $request , ObjectManager $manager)
    {   $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $encoders = array(new JsonEncoder());
        $normalizers = array($normalizer);
        
        $serializer = new Serializer($normalizers, $encoders);
        
        
        
        $categories = $this->getDoctrine()
                           ->getRepository(Category::class)
                           ->findAll();

        $jsonContent = $serializer->serialize($categories, 'json');

        $response = new JsonResponse();
        $response->setContent($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode('302');

        return $response;
    }

     
    /**
     * @Route("api/newCategory", name="api_newCategory",methods={"POST","OPTIONS"})
     * 
     */
    
    public function newCategory(Request $request)
    {  
        $response = new Response();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $response->headers->set('Content-Type', 'application/text');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type',true);

           
        }

        $data = json_decode($request->getContent(), true); // je decode mes données json reçues
        $category = new Category(); // je crée une catégorie
        $form = $this->createForm(CategoryType::class , $category); // je crée un formulaire lié à ça 
        
        
        $form->submit($data); // je valide et soumet mes données dans le formulaire
        $category->setName($request->get('name')); // je place  la valeur du champ nom dans le nom de ma catégorie

        if ($form->isValid()) { 
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush(); // j'envoie dans la base de donnée. 
            return  $this->render('globe_trotter/newCatAPI.html.twig', [ // ici j'éssaie de faire afficher le truc mais ça va pas pour le moment
                'controller_name' => 'FrontController','form'=>$form->createView(),
                'response'=>$response->setStatusCode('404')
            ]);
        } else {
           
            return $response->setStatusCode('404');
        }


    }

     /**
     * @Route("api/new", name="new",methods={"POST", "OPTIONS"})
     * @Route("api/{id}/edit", name="edit")
     */
    public function new(CriticalArticle $article = null ,Request $request , ObjectManager $manager)
    {
        if(!$article){
            $article = new CriticalArticle();
        }
        

        
        $form = $this->createForm(CriticalArticleType::class,$article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $article->setDateTime(new \DateTime());
            }
            
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('traveling');
        }


        return $this->render('globe_trotter/new.html.twig', [
            'controller_name' => 'FrontController','form'=>$form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("api/edit/{id}", name="api_edit")
     */
    public function edit(CriticalArticle $article ,Request $request , ObjectManager $manager)
    {
        if(!$article){
            $article = new CriticalArticle();
        }
        

        
        $form = $this->createForm(CriticalArticleType::class,$article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $article->setDateTime(new \DateTime());
            }
            
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('traveling');
        }


        return $this->render('globe_trotter/new.html.twig', [
            'controller_name' => 'FrontController','form'=>$form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }
}
