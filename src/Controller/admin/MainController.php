<?php

namespace App\Controller\admin;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin', name: 'admin_')]

class MainController extends AbstractController
{
    #[Route('/', name: 'index')]
   public function index()
   {
    return $this->render('admin/index.html.twig',
    [ 'products'=>'products']);

   }

}