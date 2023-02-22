<?php

namespace App\Controller\admin;

use App\Entity\Categories;
use App\Entity\Images;
use App\Entity\Products;
use App\Form\ProductsFormType;
use App\Repository\CategoriesRepository;
use App\Repository\ImagesRepository;
use App\Repository\ProductsRepository;
use App\service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Provider\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/products', name: 'admin_products_')]

class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProductsRepository $productsRepository, ProductsRepository $product,CategoriesRepository $categoriesRepository )
    {   
        
        $products=$productsRepository->findAll();
        
        return $this->render('admin/products/index.html.twig',
        [ 'products'=>$products]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request, EntityManagerInterface $entityManager,
    SluggerInterface $slugger, PictureService $pictureService ): Response
    {
        $product = new Products;
        $form = $this->createForm(ProductsFormType::class,$product);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
             
             $images = $form->get('images')->getData();
           
             foreach ($images as $image) {
                   
                   $folder = 'products';
                   $fichier = $pictureService->add($image,$folder,300,300);
                    $img = new Images;
                    $img->setName($fichier);
                    $product->addImage($img);


             }
             $slug= $slugger->slug($product->getName());
               $product->setSlug($slug);
             
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', 'le produit '.$product->getName().' est bien ajouté');
                        return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/products/add.html.twig',['addproductform' => $form->createView()]);
    }

    #[Route('/update/{id}', name: 'update')]
    public function update(Request $request, EntityManagerInterface $entityManager,SluggerInterface $slugger, Products $product, PictureService $pictureService)
    {

        
        $form = $this->createForm(ProductsFormType::class,$product);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
             

            $images = $form->get('images')->getData();
           
            foreach ($images as $image) {
                  
                  $folder = 'products';
                  $fichier = $pictureService->add($image,$folder,300,300);
                   $img = new Images;
                   $img->setName($fichier);
                   $product->addImage($img);


            }
            
             $slug= $slugger->slug($product->getName());
             $product->setSlug($slug);

            //   dd($product);
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', 'le produit '.$product->getName().' est bien modifié');
                        return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/products/update.html.twig',[
            'addproductform' => $form->createView(),
            'product'=>$product
    ]);

    }

    #[Route('/delate/{id}', name: 'delate')]
    public function delate( EntityManagerInterface $em, Products $product)
    {

        // ManagerRegistry $doctrine:
        // $em = $doctrine->getManager();
        $images = $product->getImages();
        if ($images) {
            foreach ($images as $image) {
                $nomImage = $this->getParameter('images_directory') . '/products/' . $image->getName();

                $nomImageMin = $this->getParameter('images_directory') . '/products/min/300x300-' . $image->getName();
                if (file_exists($nomImage)) {
                    unlink($nomImage);
                }
                if (file_exists($nomImageMin)) {
                    unlink($nomImageMin);
                }
            }
        }
       
        $em->remove($product);
        $em->flush();
        $this->addFlash('success', 'le produit '.$product->getName().' est bien suprimer');
        return $this->redirectToRoute('admin_products_index');
    }
    
    #[Route('/delate/image/{id}', name: 'delate_image',methods:['DELETE']) ]
    public function delateImage( Images $image,
     EntityManagerInterface $em, Request $request)
    
     {
      
      $data = json_decode($request->getContent(),true);
      
      if($this->isCsrfTokenValid('delate'. $image->getId(),$data['token'])){
           //on recupere le nom de l'image
           
           $em->remove($image);
           $em->flush();
        return new JsonResponse(['success'=>true],200);

      };
        return new JsonResponse(['error'=>'Erreur de suppression']);


    }
}
