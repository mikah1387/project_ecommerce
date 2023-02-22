<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotNull;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index( CategoriesRepository $categoriesRepository): Response
    {
         $categories=$categoriesRepository->findBy([],['categorieOrder'=>'asc']);
        return $this->render('main/index.html.twig',['categories'=>$categories
        
    ]
    );
    }
}
