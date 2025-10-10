<?php

namespace App\Application\VendingMachine\DTO;

class InsertCoinMachineRequestDTO
{
    public float $coin = 0;
    public string $operation = '';

    public function __construct(
        float $coin = 0,
        string $operation = ''
    ) {
        $this->coin = $coin;
        $this->operation = $operation;
    }
}
