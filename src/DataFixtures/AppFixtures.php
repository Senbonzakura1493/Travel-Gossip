<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\CriticalArticle;
use App\Entity\Category;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $faker = \Faker\Factory::create('fr_FR');


        //creer trois catégories. 
        for($j=1;$j<=10;$j++){
            $category = new Category();
            $category->setName("Detente");
            $manager->persist($category);

            for($i=1;$i<=mt_rand(4,6);$i++){
                
                $article = new CriticalArticle();
                $content = '<p>';
                $content .= join($faker->paragraphs(5),'</p><p>');
                $content = '</p>';
                
                $article = new CriticalArticle();
                $article->setCategory();
                $article->setTitre($faker->sentence());
                $article->setDestination($faker->sentence());
                $article->setResume($content);
                $article->setPlaceToVisit($faker->sentence());
                $article->setDateTime(new \DateTime());

                $category->addCriticalArticle($article);

                $manager->persist($article);
    
            }
        }
        
        

        $manager->flush();
    }
}
