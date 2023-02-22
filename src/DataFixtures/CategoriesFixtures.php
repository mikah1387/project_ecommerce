<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use phpDocumentor\Reflection\PseudoTypes\LowercaseString;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{
    private $count = 1;
    public function  __construct( private SluggerInterface $slugger)
    {
        
    }   

    public function load(ObjectManager $manager): void
    {
        $parent = $this->CreateCategory('Informatique',null, $manager);
       $this->CreateCategory('Ecrans', $parent,$manager);
       $this->CreateCategory('Souris', $parent,$manager);
       $this->CreateCategory('Claviers', $parent,$manager);
       $parent = $this->CreateCategory('Hightech',null, $manager);
       $this->CreateCategory('Smartphones', $parent,$manager);
       $this->CreateCategory('Tablettes', $parent,$manager);
       $this->CreateCategory('Consoles', $parent,$manager);

        $manager->flush();
    }
    public function CreateCategory(string $name,Categories $parent=null, ObjectManager $manager )
    {
        $category = new Categories;
        $category->setName($name)
                 ->setParent($parent)
                 ->setSlug($this->slugger->slug($category->getName())->lower())
                 ->setCategorieOrder(rand(1,8));
             $manager->persist($category);

             $this->addReference('cat-'.$this->count, $category);
             $this->count++;
             return $category;

    }
}
