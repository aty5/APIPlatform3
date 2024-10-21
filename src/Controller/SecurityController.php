<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    #[Route(path: '/api/login', name: 'api_login', methods: ['POST'])]
    public function login()
    {
        $user = $this->getUser();
        return $this->json([
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles()
        ]);
    }

    #[Route(path: '/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout()
    {

    }
}