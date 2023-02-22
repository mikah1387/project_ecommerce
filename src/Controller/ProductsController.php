<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[Route('/products', name: 'app_products_')]

class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProductsRepository $products): Response
    {
        return $this->render('products/index.html.twig', [
            'products'=> $products->findAll(),
        ]);
    }

    #[Route('/{slug}', name: 'details')]
    public function details(Products $product, CacheInterface $cache): Response
    {
         
          $cache->get('product_detail_'.$product->getSlug(),function() use($product){
         
           return $product;
             
          });
        // $text = $cache->get('text_long',function(ItemInterface $item){
             
        //     $item->expiresAfter(20);
        //     return $this->functionlongue();
        // });
        // $text = $this->functionlongue();
        return $this->render('products/details.html.twig',['product'=> $product]);
    }
  
    private function functionlongue(){

        sleep(3);
        return "textlong";
    }
    

}
