<?php

namespace App\Domain\VendingMachine\Exception;

use Exception;

class InsufficientStockException extends Exception
{
    private string $sku;
    private int $requested;
    private int $available;

    public function __construct(string $sku, int $requested, int $available)
    {
        parent::__construct(
            "Cannot remove $requested of " . $sku . " stock."
        );
        $this->sku = $sku;
        $this->requested = $requested;
        $this->available = $available;
    }

    public function getSku(): int
    {
        return $this->sku;
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
