<?php

namespace App\Controller;

use App\Form\RestPasswordFormType;
use App\Form\RestPassword2FormType;
use App\Repository\UsersRepository;
use App\service\JWTService;
use App\service\SendMailService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error]);
    }

    
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
  
    #[Route(path: '/forgot_pass', name: 'app_forgot')]

    public function forgottenPassword(Request $request,UsersRepository $usersRepository,
    SendMailService $mail,EntityManagerInterface $entityManager,
    TokenGeneratorInterface $tokenGeneratorInterface)
    {

        $form = $this->createForm(RestPasswordFormType::class);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
        //     // on cherche l'utilisateur
            $user= $usersRepository->findOneByEmail($form->get('email')->getData());
            // dd($user);
            if ($user){
               
              $token = $tokenGeneratorInterface->generateToken();  
              $user->setResetToken($token);
              $entityManager->persist($user);

              $entityManager->flush();
            //   on envoie un mail de réinitialisation
                $url = $this->generateUrl('app_verifyreset',['token'=>$token],
                 UrlGeneratorInterface::ABSOLUTE_URL);
                $context = [
                'user'=>$user,
                'url'=>$url
                   ];
                 $mail->send(
                'mikah@gmail.com',
                $user->getEmail(),
                'Réinitialisation de mot de passe',
                'resetpass',$context
               
            
                );
                $this->addFlash('success', 'Email envoyé avec succés');
                return $this->redirectToRoute('app_login');
            }
            $this->addFlash('','un probleme est survenu');
            return $this->redirectToRoute('app_login');
            
        //     $this->addFlash('success','vérifier votre boitte mail pour valider le lien');
        //     return $this->redirectToRoute('app_login');

        }
    
        return $this->render('security/rest_pass_request.html.twig',[
            'resetPasswordForm' => $form->createView()
        ]);
    }

    #[Route('/verifreset/{token}', name: 'app_verifyreset')]
    public function verify($token,
     Request $request,
     UsersRepository $usersRepository,
     EntityManagerInterface $entityManager,
     UserPasswordHasherInterface $passwordHasher )
    
    {
         $user= $usersRepository->findOneByresetToken($token);
        //  $user=$this->getUser();
       

         if($user){

                 $form = $this->createForm(RestPassword2FormType::class);
                 $form->handleRequest($request);

              if($form->isSubmitted() && $form->isValid()){
               
                $user->setResetToken('');
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                    );
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success','mot de passe modifier');
                    return  $this->redirectToRoute('app_login');
              }
                   

                 return $this->render('security/rest_pass_request2.html.twig',[
                            'resetPasswordForm' => $form->createView()
                        ]);
         }
         $this->addFlash('','token invalide');
        return  $this->redirectToRoute('app_login');
        //   if($user){
        //     $form = $this->createForm(RestPasswordFormType::class);
        //     $form->handleRequest($request);

        //     if ($form->isSubmitted() && $form->isValid()) {
        //         // encode the plain password
        //         $user->setPassword(
        //             $passwordHasher->hashPassword(
        //                 $user,
        //                 $form->get('plainPassword')->getData()
        //             )
        //         );
    
        //         $entityManager->persist($user);
        //         $entityManager->flush();
        //         $this->addFlash('success','mot de passe bien modifier');
        //     return $this->redirectToRoute('app_login');
        //     }
        //     return $this->render('security/rest_pass_request.html.twig',[
        //         'resetPasswordForm' => $form->createView()
        //     ]);
        //   }

        //   $this->addFlash('', 'le token n\'est pas bon');
        //   return $this->redirectToRoute('app_login');
    
    }
   
}
