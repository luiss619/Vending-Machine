<?php

namespace App\Application\VendingMachine\DTO;

class UpdateStockResponseDTO
{
    public int $new_stock;

    public function __construct(
        int $new_stock = 0
    ) {
        $this->new_stock = $new_stock;
    }
}
