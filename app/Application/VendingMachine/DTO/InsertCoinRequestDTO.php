<?php

namespace App\Application\VendingMachine\DTO;

class InsertCoinRequestDTO
{
    public float $coin = 0;

    public function __construct(
        float $coin = 0,
    ) {
        $this->coin = $coin;
    }
}
