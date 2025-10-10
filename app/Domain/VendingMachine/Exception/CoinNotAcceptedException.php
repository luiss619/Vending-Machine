<?php

namespace App\Domain\VendingMachine\Exception;

use Exception;

class CoinNotAcceptedException extends Exception
{
    private float $coin;

    public function __construct(float $coin)
    {
        parent::__construct("Coin " . number_format($coin, 2) . " € not accepted");
        $this->coin = $coin;
    }

    public function getCoin(): float
    {
        return $this->coin;
    }
}
