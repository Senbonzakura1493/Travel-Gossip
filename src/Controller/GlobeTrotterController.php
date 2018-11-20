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


class GlobeTrotterController extends AbstractController
{
    /**
     * @Route("/traveling", name="traveling")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(CriticalArticle::class);

        $articles = $repo->findAll();
        return $this->render('globe_trotter/index.html.twig', [
            'controller_name' => 'FrontController',
            'articles'=>$articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('globe_trotter/home.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }
    /**
     * @Route("/newCategory", name="newCategory")
     */
    public function newCategory(Category $category = null  ,Request $request , ObjectManager $manager)
    {   if(!$category){
        $category = new Category();
    }
        $repo = $this->getDoctrine()->getRepository(Category::class);

        $categories = $repo->findAll();
        $form = $this->createForm(CategoryType::class,$category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            
            
            $manager->persist($category);
            $manager->flush();

            return $this->redirectToRoute('newCategory');
        }

        return $this->render('globe_trotter/newCategory.html.twig', [
            'controller_name' => 'FrontController','form'=>$form->createView(),
            'categories'=>$categories
        ]);
    }
     /**
     * @Route("/new", name="new")
     * @Route("/{id}/edit", name="edit")
     */
    public function new(CriticalArticle $article = null ,Request $request , ObjectManager $manager)
    {
        if(!$article){
            $article = new CriticalArticle();
        }
        

        //$form = $this->createFormBuilder($article)
        //             ->add('Category')
        //             ->add('Destination')
        //             ->add('PlaceToVisit',CollectionType::class)
        //             ->add('Resume',TextareaType::class)
        //             ->getForm();

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
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(CriticalArticle $article ,Request $request , ObjectManager $manager)
    {
        if(!$article){
            $article = new CriticalArticle();
        }
        

        //$form = $this->createFormBuilder($article)
        //             ->add('Category')
        //             ->add('Destination')
        //             ->add('PlaceToVisit',CollectionType::class)
        //             ->add('Resume',TextareaType::class)
        //             ->getForm();

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
