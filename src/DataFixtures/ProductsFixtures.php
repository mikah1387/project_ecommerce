<?php

namespace App\DataFixtures;

use App\Entity\products;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;
class ProductsFixtures extends Fixture
{
    public function __construct( private SluggerInterface $slugger)
        {
            
        }
    public function load(ObjectManager $manager): void
    {
       
        $faker = Faker\Factory::create('fr_FR');

           for ($prod=1; $prod <=20 ; $prod++) 
           { 
         $category= $this->getReference('cat-'. rand(1,8));      
        $product = new Products;
        $product->setName($faker->text(5))
        ->setDescription($faker->text())
        ->setPrix($faker->numberBetween(100,900))
        ->setStock($faker->numberbetween(0,10))
        ->setSlug($this->slugger->slug($product->getName())->lower())
        

        ->setCategories($category);
        $this->addReference('prod-'.$prod, $product);
        
           $manager->persist($product);
             
           }



           $manager->flush();
    }
}
