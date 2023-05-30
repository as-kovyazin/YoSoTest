<?php

namespace App\Services;

use App\DTO\RequestDTO;
use App\Entity\Briefcase;
use App\Entity\Promotion;
use App\Entity\User;
use App\Repository\BriefcaseRepository;
use App\Repository\PromotionRepository;
use Throwable;

class BriefcaseService
{

    private BriefcaseRepository $briefcaseRepository;
    private PromotionRepository $promotionRepository;

    public function __construct(BriefcaseRepository $briefcaseRepository, PromotionRepository $promotionRepository)
    {
        $this->briefcaseRepository = $briefcaseRepository;
        $this->promotionRepository = $promotionRepository;
    }

    public function createEmptyBriefcaseForUser(User $user): ?Briefcase
    {
        try {
            $briefcase = new Briefcase();
            $briefcase->setUserId($user);
            $briefcase->setName("если надо подставлять название портфеля");

            $this->briefcaseRepository->add($briefcase, true);
        } catch (Throwable $err) {
            return null;
        }
        return $briefcase;
    }

    public function addPromotionInBriefcase(RequestDTO $requestDTO): ?Promotion
    {
        try {
            $promotion = new Promotion();

            $briefcase = $this->briefcaseRepository->find($requestDTO->getBriefcaseId());

            $promotion->setBriefcase($briefcase)
                ->setTicker($requestDTO->getTicker())
                ->setQuantity($requestDTO->getQuantity());

            $this->promotionRepository->add($promotion, true);
        } catch (Throwable $err) {
            return null;
        }
        return $promotion;
    }

    public function removePromotionFromBriefcase(RequestDTO $requestDTO): ?string
    {
        $startQuantity = $requestDTO->getQuantity();

        do {
            $promotion = $this->promotionRepository->getFirstByTicker(
                $requestDTO->getBriefcaseId(),
                $requestDTO->getTicker()
            );

            if (is_null($promotion)) {
                return 'не найдена акция в портфеле';
            }

            $startQuantity -= $promotion->getQuantity();

            if ($startQuantity >= 0) {
                $this->promotionRepository->remove($promotion, true);

                if ($startQuantity === 0) {
                    return null;
                }

                continue;
            }

            $promotion->setQuantity($promotion->getQuantity() - $requestDTO->getQuantity());
            $this->promotionRepository->add($promotion, true);
        } while ($startQuantity > 0);
        return null;
    }

    public function getBriefcaseCost(RequestDTO $requestDTO): array
    {
        $result = $this->promotionRepository->getSumByBriefcase($requestDTO->getBriefcaseId());

        return $result;
    }

    public function validate(RequestDTO $requestDTO, User $user): ?string
    {
        $briefcase = $this->briefcaseRepository->find($requestDTO->getBriefcaseId());

        if (is_null($briefcase)) {
            return "портфель не найден";
        }

        if ($briefcase->getUserId()->getId() !== $user->getId()) {
            return "нет доступа к портфелю";
        }

        return null;
    }

    public function validateForDelete(RequestDTO $requestDTO, User $user): ?string
    {
        $err = $this->validate($requestDTO, $user);
        if (is_string($err)) {
            return $err;
        }

        if (!$this->promotionRepository->isTickerExist($requestDTO->getBriefcaseId(), $requestDTO->getTicker())) {
            return "в вашем портфеле отсутствует переданный ticker";
        }

        $quantityInDb = $this->promotionRepository->quantityOfTickers(
            $requestDTO->getBriefcaseId(),
            $requestDTO->getTicker()
        );

        if ($quantityInDb < $requestDTO->getQuantity()) {
            return "вы хотите удалить ticker больше чем у вас доступно";
        }

        return null;
    }

}