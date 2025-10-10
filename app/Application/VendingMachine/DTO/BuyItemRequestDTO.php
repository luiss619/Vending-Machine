<?php

namespace App\Application\VendingMachine\DTO;

class BuyItemRequestDTO
{
    public string $sku;

    public function __construct(
        string $sku = '',
    ) {
        $this->sku = $sku;
    }
}
