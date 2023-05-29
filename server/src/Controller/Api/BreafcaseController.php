<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/', name: 'api_')]
class BreafcaseController extends AbstractController
{
    #[Route('breafcase', name: 'user', methods: "POST")]
    public function index(): JsonResponse
    {

    }
}
