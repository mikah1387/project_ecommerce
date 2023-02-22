<?php

namespace App\Controller\admin;

use App\Entity\Categories;
use App\Form\CategorieFormType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/categories', name: 'admin_categories_')]

class CategoriesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categoriesRepository)
    {
        return $this->render(
            'admin/categories/index.html.twig',
            ['categories' => $categoriesRepository->findBy([],['categorieOrder'=>'asc'])]
        );
    }
    #[Route('/add', name: 'add')]
    public function add(Request $request, EntityManagerInterface $entityManager,
    SluggerInterface $slugger,  ):Response
       {
        $categorie = new Categories;
        $form = $this->createForm(CategorieFormType::class,$categorie);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
             
        
             $slug= $slugger->slug($categorie->getName());
               $categorie->setSlug($slug);
             
            $entityManager->persist($categorie);
            $entityManager->flush();
            $this->addFlash('success', 'la categorie '.$categorie->getName().' est bien ajouté');
                        return $this->redirectToRoute('admin_categories_index');
        }

        return $this->render('admin/categories/add.html.twig',['add_categorieform' => $form->createView()]);
    }
    #[Route('/update/{id}', name: 'update')]
    public function update(Request $request, EntityManagerInterface $entityManager,
    SluggerInterface $slugger,Categories $categorie  ):Response
       {
        
        $form = $this->createForm(CategorieFormType::class,$categorie);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
             
        
             $slug= $slugger->slug($categorie->getName());
               $categorie->setSlug($slug);
             
            $entityManager->persist($categorie);
            $entityManager->flush();
            $this->addFlash('success', 'la categorie '.$categorie->getName().' est bien modifier');
                        return $this->redirectToRoute('admin_categories_index');
        }

        return $this->render('admin/categories/add.html.twig',['add_categorieform' => $form->createView()]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delate( EntityManagerInterface $em, Categories $categorie)
    {

        // ManagerRegistry $doctrine:
        // $em = $doctrine->getManager();
        
        $em->remove($categorie);
        $em->flush();
        $this->addFlash('success', 'la categorie '.$categorie->getName().' est bien suprimer');
        return $this->redirectToRoute('admin_categories_index');
    }
}
