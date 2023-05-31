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
    private MoexService $moexService;

    public function __construct(
        BriefcaseRepository $briefcaseRepository,
        PromotionRepository $promotionRepository,
        MoexService $moexService
    ) {
        $this->briefcaseRepository = $briefcaseRepository;
        $this->promotionRepository = $promotionRepository;
        $this->moexService = $moexService;
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

    public function addPromotionInBriefcase(RequestDTO $requestDTO): ?string
    {
        $promotion = new Promotion();

        $briefcase = $this->briefcaseRepository->find($requestDTO->getBriefcaseId());

        if ($this->validTicker($requestDTO->getTicker()) === false) {
            return 'Переданный ticker не найден';
        }

        if ($requestDTO->getQuantity() <= 0) {
            return 'Количество ticker не может быть меньше или равно 0';
        }

        $promotion->setBriefcase($briefcase)
            ->setTicker($requestDTO->getTicker())
            ->setQuantity($requestDTO->getQuantity());

        $this->promotionRepository->add($promotion, true);
        return null;
    }

    public function removePromotionFromBriefcase(RequestDTO $requestDTO): ?string
    {
        $deleteQuantity = $requestDTO->getQuantity();

        $this->promotionRepository->startTransaction();
        do {
            $promotion = $this->promotionRepository->getFirstByTicker(
                $requestDTO->getBriefcaseId(),
                $requestDTO->getTicker()
            );

            if (is_null($promotion)) {
                $this->promotionRepository->rollbackTransaction();
                return 'не найдена акция в портфеле';
            }

            $deleteQuantity -= $promotion->getQuantity();

            if ($deleteQuantity >= 0) {
                $this->promotionRepository->remove($promotion, true);

                if ($deleteQuantity === 0) {
                    $this->promotionRepository->commitTransaction();
                    return null;
                }

                continue;
            }

            $promotion->setQuantity($promotion->getQuantity() - $requestDTO->getQuantity());
            $this->promotionRepository->add($promotion, true);
        } while ($deleteQuantity > 0);
        $this->promotionRepository->commitTransaction();
        return null;
    }

    public function getBriefcaseCosts(RequestDTO $requestDTO): array
    {
        $result = $this->promotionRepository->getSumByBriefcase($requestDTO->getBriefcaseId());

        $tickers = $this->moexService->getTickers();

        $sumOfBriefcase = 0;

        foreach ($result as $index => $tickerInfo) {
            $tickerCost = $tickers[$tickerInfo['ticker']];
            $totalTickerCost = $tickerCost * $tickerInfo['quantity'];
            $sumOfBriefcase += $totalTickerCost;
            $result[$index]['cost'] = $tickerCost;
            $result[$index]['total_cost'] = $totalTickerCost;
        }

        foreach ($result as $index => $tickerInfo) {
            $totalTickerCost = $tickerInfo['total_cost'];
            $result[$index]['share'] = round($totalTickerCost/$sumOfBriefcase, 4) * 100;
        }

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
            return "вы не можете удалить ticker больше чем у вас доступно";
        }

        return null;
    }

    /**
     * @return Briefcase[]
     * */
    public function getBriefcases(User $user): array
    {
        return $this->briefcaseRepository->findBy(['userId' => $user->getId()]);
    }

    private function validTicker(string $ticker): bool
    {
        $tickers = $this->moexService->getTickers();
        return isset($tickers[$ticker]);
    }

}