<?php

namespace App\Domain\VendingMachine\Exception;

use Exception;

class InsufficientCoinsInMachineException extends Exception
{
    private int $cents;
    private int $price_in_cents;
    private string $product_name;

    public function __construct(int $cents, int $price_in_cents, string $product_name)
    {
        parent::__construct(
            "The following amount of change is missing: " . number_format($cents / 100, 2) . " €. <br>
            Please try to enter the exact amount (" . number_format($price_in_cents / 100, 2) . " €) to " . $product_name
        );

        $this->cents = $cents;
        $this->price_in_cents = $price_in_cents;
        $this->product_name = $product_name;
    }

    public function getProductName(): string
    {
        return $this->product_name;
    }
}
