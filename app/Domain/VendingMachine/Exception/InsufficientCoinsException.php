<?php

namespace App\Domain\VendingMachine\Exception;

use Exception;

class InsufficientCoinsException extends Exception
{
    private int $coin; // céntimos
    private int $requested;
    private int $available;

    public function __construct(int $coin, int $requested, int $available)
    {
        parent::__construct(
            "Cannot remove $requested coin of " . number_format($coin / 100, 2) . " €. Only $available available."
        );
        $this->coin = $coin;
        $this->requested = $requested;
        $this->available = $available;
    }

    public function getCoin(): int
    {
        return $this->coin;
    }

    public function getRequested(): int
    {
        return $this->requested;
    }

    public function getAvailable(): int
    {
        return $this->available;
    }
}
