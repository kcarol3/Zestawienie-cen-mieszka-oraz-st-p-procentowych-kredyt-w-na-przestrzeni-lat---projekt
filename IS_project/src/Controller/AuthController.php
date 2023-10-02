<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api_")
 */
class AuthController extends AbstractController
{
    #[Route('/auth/login', name: 'app_auth')]
    public function index(): JsonResponse
    {
        return new JsonResponse([]);
    }

    #[Route('/auth', name: 'auth')]
    public function tokenVerify(): JsonResponse
    {
        return new JsonResponse(["authentication"=>"success"]);
    }

    #[Route('/admin', name: 'admin')]
    public function tokenVerifyAdmin(): JsonResponse
    {
        return new JsonResponse(["authentication"=>"success"]);
    }

}
