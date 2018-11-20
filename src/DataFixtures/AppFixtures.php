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
        // $product = new Product();
        // $manager->persist($product);
        for($i=1;$i<=10;$i++){
            $PlaceToVisit="rialto";
            $article = new CriticalArticle();
            $article->setTitre("Au pays des raviolis");
            $article->setCategory("Culinaire");
            $article->setDestination("Venise");
            $article->setResume("les meilleurs spaghettis ever!!");
            $article->setPlaceToVisit($PlaceToVisit);
            $article->setDateTime(new \DateTime());
            
            $manager->persist($article);

        }
        for($i=11;$i<=20;$i++){
            $PlaceToVisit="rialto";
            $article = new CriticalArticle();
            $article->setTitre("Au pays des mille et une nuits");
            $article->setCategory("Historique");
            $article->setDestination("Inde");
            $article->setResume("Un monde tout en couleur et saveur");
            $article->setPlaceToVisit($PlaceToVisit);
            $article->setDateTime(new \DateTime());
            
            $manager->persist($article);

        }

        $category = new Category();
        $category->setName("Detente");
        $manager->persist($category);
        $category2 = new Category();
        $category2->setName("Sportif");
        $manager->persist($category2);

        $manager->flush();
    }
}
