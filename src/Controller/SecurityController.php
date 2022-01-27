<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setName($request->request->get('user')['name']);
            $user->setLastName($request->request->get('user')['lastName']);
            $user->setEmail($request->request->get('user')['email']);
            $password = $passwordEncoder->encodePassword($user, $request->request->get('user')['password']['first']);
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->loginUserAutomatically($user, $password);
            return $this->redirectToRoute('admin_main_page');
        }


        return $this->render('front/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('front/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    private function loginUserAutomatically($user, $password)
    {
        $token = new UsernamePasswordToken(
            $user,
            $password,
            'main',
            $user->getRoles()
        );
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached');
    }
}