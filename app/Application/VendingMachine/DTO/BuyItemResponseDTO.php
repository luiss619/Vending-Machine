<?php

namespace App\Application\VendingMachine\DTO;

class BuyItemResponseDTO
{
    public float $balance;
    public array $coins_machine;
    public array $coins_introduced;
    public ?string $product;
    public string $change_message;
    public int $new_stock;

    public function __construct(
        float $balance,
        array $coins_machine = [],
        array $coins_introduced = [],
        ?string $product = null,
        string $change_message = '',
        int $new_stock = 0
    ) {
        $this->balance = $balance;
        $this->coins_machine = $coins_machine;
        $this->coins_introduced = $coins_introduced;
        $this->product = $product;
        $this->change_message = $change_message;
        $this->new_stock = $new_stock;
    }
}
