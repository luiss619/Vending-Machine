<?php

namespace App\Application\VendingMachine\DTO;

class ErrorResponseDTO
{
    public float $balance;
    public ?string $error;

    public function __construct(
        float $balance,
        ?string $error = null,
    ) {
        $this->balance = $balance;
        $this->error = $error;
    }
}
