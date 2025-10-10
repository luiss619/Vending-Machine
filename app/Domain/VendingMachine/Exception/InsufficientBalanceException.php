<?php

namespace App\Domain\VendingMachine\Exception;

use Exception;

class InsufficientBalanceException extends Exception
{
    private float $required;
    private float $available;
    private string $productName;

    public function __construct(float $required, float $available, string $productName)
    {
        parent::__construct("Not enough money for $productName");
        $this->required = $required;
        $this->available = $available;
        $this->productName = $productName;
    }

    public function getRequired(): float
    {
        return $this->required;
    }
    public function getAvailable(): float
    {
        return $this->available;
    }
    public function getMissing(): float
    {
        return $this->required - $this->available;
    }
    public function getProductName(): string
    {
        return $this->productName;
    }
}
