<?php

namespace App\Controller;

use App\Repository\OrdersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/profile', name: 'app_profile_')]

class ProfileController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig');
    }

    #[Route('/orders', name: 'orders')]
    public function orders(UserInterface $user,OrdersRepository $ordersRepository): Response
    {

        if($user){
            $orders = $ordersRepository->findBy(['users'=>$user],['reference'=>'asc']);
      
            return $this->render('orders/index.html.twig', [
                'orders' => $orders,
            ]);
         }else{

            $this->addFlash('','vous n\'étes pas dans le bon compte');
            return $this->redirectToRoute('app_login');
         }
       
        return $this->render('profile/orders.html.twig', [
            'controller_name' => 'Vos commandes',
        ]);
    }
}
