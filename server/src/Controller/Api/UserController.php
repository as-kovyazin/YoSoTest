<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/', name: 'api_')]
class UserController extends AbstractController
{
    #[Route('user', name: 'user', methods: "PUT")]
    public function index(): JsonResponse
    {

    }
}
