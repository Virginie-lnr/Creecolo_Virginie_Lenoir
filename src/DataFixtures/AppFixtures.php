<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        for($i = 0; $i <10; $i ++){
            $post = new Post(); 

            $post
                ->setTitle('Un exemple de post')
                ->setContent('avec son contenu')
                ->setCreatedAt(new \DateTime('now'))
            ;
            $manager->persist($post); 
        }
        $manager->flush();
    }
}
