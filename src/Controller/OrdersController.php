<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Entity\Products;
use App\Entity\Users;
use App\Repository\OrdersDetailsRepository;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/orders', name: 'orders_')]

class OrdersController extends AbstractController
{
   

   

    #[Route('/addorder', name: 'addorder')]
    public function addorder(UserInterface $user,SessionInterface $session, ProductsRepository $productsRepository, EntityManagerInterface $em  ): Response
    { 
        

        if($user){

         
         $order= new Orders;
         $order->setReference(strval(rand(10,1000)))
               ->setCoupons(null)
               ->setUsers($user);

           $panier= $session->get('panier');
           foreach ($panier as $idproduct => $quantity) {
                 $orderdetail = new OrdersDetails;

                 $order->addOrdersDetail($orderdetail);
                 $product = $productsRepository->find($idproduct);
                 $product->addOrdersDetail($orderdetail);
                  $orderdetail->setQuantity($quantity)
                              ->setPrix($product->getPrix()) ;   
            
           }
           $em->persist($order);
           $em->flush();
           $this->addFlash('success', 'votre commande N°'.$order->getReference().' est bien  validé');
           $session->remove('panier');
            return $this->redirectToRoute('app_profile_orders');

       
        }else{
         $this->addFlash('alert', 'vous devez connecter pour valider votre panier');
          return $this->redirectToRoute('app_login');

       }

    }  
    
}
