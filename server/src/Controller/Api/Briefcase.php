<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/briefcase/", name="api_briefcase_")
 */
class Briefcase extends AbstractController
{
    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        return $this->json([]);
    }
}
