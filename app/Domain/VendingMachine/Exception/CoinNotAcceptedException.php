<?php

namespace App\Domain\VendingMachine\Exception;

use Exception;

class CoinNotAcceptedException extends Exception
{
    private int $cents;

    public function __construct(int $cents)
    {
        $coin = number_format($cents / 100, 2);

        parent::__construct("Coin " . $coin . " € not accepted");
        $this->cents = $cents;
    }
}
