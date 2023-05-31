<?php

namespace App\DTO;

use Symfony\Component\HttpFoundation\Request;

class RequestDTO
{

    private string $briefcaseId;
    private string $ticker;
    private int $quantity;

    public function __construct(Request $request)
    {
        $this->briefcaseId = $request->get("briefcaseId") ?: '';
        $this->ticker = $request->get("ticker") ?: '';
        $this->quantity = (int)$request->get("quantity") ?: 0;
    }

    public function getBriefcaseId(): string
    {
        return $this->briefcaseId;
    }

    public function getTicker(): string
    {
        return $this->ticker;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

}