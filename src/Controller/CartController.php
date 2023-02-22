<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/cart', name: 'cart_')]

class CartController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, ProductsRepository $productsRepository): Response
    {
          $panier= $session->get('panier',[]);
          $dataproduct=[];
           $total = 0;
           $totalquantiy=0;
          foreach ($panier as $id => $quantity) {

            $product= $productsRepository->find($id);
            $quantity = $quantity;
            $dataproduct[]=[
                'product'=>$product,
                'quantity'=>$quantity
            ];
            $total += $product->getPrix() * $quantity;
            $totalquantiy +=$quantity; 
          }
         
        return $this->render('cart/index.html.twig', [
           'dataproduct'=>$dataproduct,
           'total'=>$total,
           'totalquantity'=>$totalquantiy
        ]);
    }
    #[Route('/add/{id}', name: 'add')]

    public function add(Products $product, SessionInterface $session)
    {
        $id= $product->getId();
       $panier= $session->get('panier',[]);
       if(!empty($panier[$id]))
       {
        $panier[$id]++;
      
       }else{
        $panier[$id]=1;

       }
       $session->set('panier',$panier);
       
       $this->addFlash('success', 'le produit '. $product->getName().' est bien ajouter au panier');
        return $this->redirectToRoute('cart_index');    
    }
    #[Route('/remove/{id}', name: 'remove')]

    public function remove(Products $product, SessionInterface $session)
    {
        $id= $product->getId();
       $panier= $session->get('panier',[]);
       if(!empty($panier[$id]))
       {
        if(($panier[$id])>1)
        {
            $panier[$id]--;

        }else{
            unset($panier[$id]);

        }
      
       }
       
       $session->set('panier',$panier);
      
       $this->addFlash('success', 'le produit '. $product->getName().' est bien supprimer de panier');
        return $this->redirectToRoute('cart_index');    
    }
    #[Route('/delete/{id}', name: 'delete')]

    public function delete(Products $product, SessionInterface $session)
    {
        $id= $product->getId();
        $panier= $session->get('panier',[]);
        if(!empty($panier[$id]))
        {
            unset($panier[$id]);
        }
       
       
        $session->set('panier',$panier);
       $this->addFlash('success', 'le produit '. $product->getName().' est bien supprimer de panier');
        return $this->redirectToRoute('cart_index');    
    }
    #[Route('/delete', name: 'delete_All')]

    public function delete_All( SessionInterface $session)
    {    
        $session->remove('panier');
       $this->addFlash('success', 'le produit  est bien supprimer de panier');
        return $this->redirectToRoute('cart_index');    
    }
    #[Route('/delevry', name: 'delevry')]

    public function delevry( UserInterface $user)
    {    
        
      return $this->render('cart/delevry.html.twig', ['user'=>$user]) ;   
    }
}
