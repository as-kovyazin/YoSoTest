<?php

namespace App\Controller;

use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/", name="user_")
 */
class User extends AbstractController
{

    /**
     * @Route("create", name="create", methods={"POST"})
     */
    public function create(UserService $userService): JsonResponse
    {
        $user = $userService->createUser();
        if (is_null($user)) {
            return $this->json([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $result = [
            "apiKey" => $user->getApiKey()
        ];

        return $this->json($result);
    }
}