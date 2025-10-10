<?php

namespace App\Domain\VendingMachine\Entity;

class Coin
{
    private int $value;
    private int $quantity;

    public function __construct(float $value, int $quantity)
    {
        $this->value = (int) round($value * 100);
        $this->quantity = $quantity;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getValueInEuros(): float
    {
        return $this->value / 100;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function increaseQuantity(int $amount = 1): void
    {
        $this->quantity += $amount;
    }

    public function decreaseQuantity(int $amount = 1): void
    {
        $this->quantity -= $amount;
    }
}
