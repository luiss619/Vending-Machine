<?php

namespace App\Domain\VendingMachine\Exception;

use Exception;

class ProductNotAcceptedException extends Exception
{
    private string $sku;

    public function __construct(string $sku)
    {
        parent::__construct("Product " . $sku . " not accepted");
        $this->sku = $sku;
    }

    public function getSku(): float
    {
        return $this->sku;
    }
}
