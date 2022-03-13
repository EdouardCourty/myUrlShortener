<?php

namespace App\Controller\Security;

use App\Entity\User;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted(User::ROLE_ADMIN)) {
            return $this->redirectToRoute('admin_dashboard');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@EasyAdmin/page/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
            'csrf_token_intention' => 'authenticate',
            'username_label' => 'Username',
            'password_label' => 'Password',
            'sign_in_label' => 'Login',
            'remember_me_enabled' => true,
            'remember_me_checked' => true,
            'remember_me_label' => 'Remember me',
            'username_parameter' => 'username',
            'password_parameter' => 'password',
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
