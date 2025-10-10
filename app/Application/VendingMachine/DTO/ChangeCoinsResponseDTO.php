<?php

namespace App\Application\VendingMachine\DTO;

class ChangeCoinsResponseDTO
{
    public float $balance;
    public array $coins_machine;
    public array $coins_introduced;

    public function __construct(
        float $balance,
        array $coins_machine = [],
        array $coins_introduced = []
    ) {
        $this->balance = $balance;
        $this->coins_machine = $coins_machine;
        $this->coins_introduced = $coins_introduced;
    }
}
