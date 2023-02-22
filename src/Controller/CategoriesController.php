<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Products;
use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories', name: 'app_categories_')]

class CategoriesController extends AbstractController
{
 

    #[Route('/{slug}', name: 'list')]
    public function list(Categories $categorie, ProductsRepository $productsRepository, Request $request):response
    { 

         $page = $request->query->getInt('page',1);
        //  $products = $categorie->getProducts();
         $products = $productsRepository->findProductsPaginated($page,$categorie->getSlug(),2);
        
        return $this->render('categories/list.html.twig', ['categorie' => $categorie,
       'products'=>$products]);
    }
}
