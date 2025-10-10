<?php

namespace App\Domain\VendingMachine\Exception;

use Exception;

class InsufficientCoinsException extends Exception
{
    private int $coin;
    private int $requested;
    private int $available;

    public function __construct(int $coin, int $requested, int $available)
    {
        $coin = number_format($coin / 100, 2);
        $message = "Cannot remove $requested coin of {$coin} €. <br>" .
            ($available == 0 ? "No coins available." : "Only $available available.");

        parent::__construct(
            $message
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
