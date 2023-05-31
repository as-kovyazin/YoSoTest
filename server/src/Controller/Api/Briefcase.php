<?php

namespace App\Controller\Api;

use App\DTO\RequestDTO;
use App\Services\BriefcaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/briefcase/", name="api_briefcase_")
 */
class Briefcase extends MainController
{
    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(BriefcaseService $briefcaseService): JsonResponse
    {
        $user = $this->getUser();
        $briefcases = $briefcaseService->getBriefcases($user);

        return $this->json(['briefcases' => $this->getResponseBriefcases($briefcases)]);
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(BriefcaseService $briefcaseService): JsonResponse
    {
        $user = $this->getUser();
        $briefcase = $briefcaseService->createEmptyBriefcaseForUser($user);

        if (is_null($briefcase)) {
            return $this->json([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $result = [
            'briefcase' => $briefcase->getId(),
        ];

        return $this->json($result);
    }

    /**
     * @Route("promotion_add", name="promotion_add", methods={"POST"})
     */
    public function promotionAdd(Request $request, BriefcaseService $briefcaseService): JsonResponse
    {
        $user = $this->getUser();

        $request = $this->transformJsonBody($request);

        $requestDTO = new RequestDTO($request);
        $error = $briefcaseService->validate($requestDTO, $user);

        if (is_string($error)) {
            return $this->json(['error' => $error], Response::HTTP_BAD_REQUEST);
        }

        $requestDTO = $briefcaseService->addPromotionInBriefcase($requestDTO);

        if (is_null($requestDTO)) {
            return $this->json([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $result = [];

        return $this->json($result);
    }

    /**
     * @Route("promotion_delete", name="promotion_delete", methods={"POST"})
     */
    public function promotionDelete(Request $request, BriefcaseService $briefcaseService): JsonResponse
    {
        $user = $this->getUser();

        $request = $this->transformJsonBody($request);

        $requestDTO = new RequestDTO($request);
        $error = $briefcaseService->validateForDelete($requestDTO, $user);

        if (is_string($error)) {
            return $this->json(['error' => $error], Response::HTTP_BAD_REQUEST);
        }

        $error = $briefcaseService->removePromotionFromBriefcase($requestDTO);

        if (is_string($error)) {
            return $this->json(['error' => $error], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $result = [];

        return $this->json($result);
    }

    /**
     * @Route("cost", name="get_cost", methods={"POST"})
     */
    public function getCost(Request $request, BriefcaseService $briefcaseService): JsonResponse
    {
        $user = $this->getUser();

        $request = $this->transformJsonBody($request);

        $requestDTO = new RequestDTO($request);
        $error = $briefcaseService->validate($requestDTO, $user);

        if (is_string($error)) {
            return $this->json(['error' => $error], Response::HTTP_BAD_REQUEST);
        }

        $costs = $briefcaseService->getBriefcaseCosts($requestDTO);

        $result = [
            'costs' => $costs
        ];

        return $this->json($result);
    }

    private function getResponseBriefcases($briefcases): array
    {
        $result = [];
        foreach ($briefcases as $briefcase) {
            $result[] = [
                'id' => $briefcase->getId(),
                'name' => $briefcase->getName(),
            ];
        }
        return $result;
    }
}
