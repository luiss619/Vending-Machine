<?php

namespace App\Application\VendingMachine\DTO;

class UpdateStockRequestDTO
{
    public string $sku = '';
    public string $operation = '';

    public function __construct(
        string $sku = '',
        string $operation = ''
    ) {
        $this->sku = $sku;
        $this->operation = $operation;
    }
}
