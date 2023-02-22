<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\UsersAuthenticator;
use App\service\JWTService;
use App\service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher,   UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator, EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            // on genere le jwt de l'utilisateur
            $header= [

                'typ'=>'jwt',
                'alg'=>'HS256'
            ];
            $payload = [
              'user_id'=> $user->getId()

            ];

            $token = $jwt->generate($header,$payload,$this->getParameter('app.jwtsecret'));
            
           

            
            $mail->send(
                'mikah@gmail.com',
                $user->getEmail(),
                'activation de votre Email',
                'register',
                [
                    'user'=>$user,
                    'token'=>$token
                ]
            
                );
                
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verif/{token}', name: 'app_verify')]
    public function verify($token, JWTService $jwt,EntityManagerInterface $entityManager,UsersRepository $usersRepository )
    {
          if (($jwt->check($token,$this->getParameter('app.jwtsecret')))&& !($jwt->isExpired($token))&&$jwt->isValid($token) )
          {
            
          
            $payload= $jwt->getPayload($token);
            $user= $usersRepository->find( $payload['user_id']);
             
                if($user && !$user->getIsVerified() )
                {
                       $user->setIsVerified(1);
                        $entityManager->persist($user);
                        $entityManager->flush();
                        $this->addFlash('success', 'votre compte est activé maintenant');
                        return $this->redirectToRoute('app_profile_index');
                }


            // // $entityManager->persist($user);
            // // $entityManager->flush();
            // return $this->render('registration/compteactivate.html.twig');

          }

          $this->addFlash('', 'le token n\'est pas bon');
          return $this->redirectToRoute('app_login');
    
    }
    
    #[Route('/resendverif', name: 'app_reverify')]
    public function resendverif(JWTService $jwt, SendMailService $mail,UserInterface $user)
    {
          $user = $this->getUser();
          
          if(!$user){

            $this->addFlash('', 'vous devez etre connecter pour acceder a cette page');
            return $this->redirectToRoute('app_login');
          }

          if ( $user && $user->getIsVerified()) {
            
            $this->addFlash('success', 'votre compte est dejas activé');
            return $this->redirectToRoute('app_profile_index');
          }

          $header= [

            'typ'=>'jwt',
            'alg'=>'HS256'
        ];
        $payload = [
          'user_id'=> $user->getId()

        ];

        $token = $jwt->generate($header,$payload,$this->getParameter('app.jwtsecret'));
        
       

        
        $mail->send(
            'mikah@gmail.com',
            $user->getEmail(),
            'activation de votre Email',
            'register',
            [
                'user'=>$user,
                'token'=>$token
            ]
        
            );
            $this->addFlash('success', 'Email de vérification envoyé');
            return $this->redirectToRoute('app_profile_index');


    }


}
