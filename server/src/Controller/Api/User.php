<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/user/", name="api_user_")
 */
class User extends AbstractController
{
    /**
     * @Route("", name="index")
     */
    public function index(): JsonResponse
    {
        return $this->json([]);
    }
}