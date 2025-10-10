<?php

namespace App\Domain\VendingMachine\Entity;

use App\Domain\VendingMachine\Exception\InsufficientStockException;

class Product
{
    private string $name;
    private int $price;
    private string $sku;
    private ?string $image_url = null;
    private int $stock;

    public function __construct(string $name, float $price, string $sku, string $image_url, int $stock)
    {
        $this->name = $name;
        $this->price = (int) round($price * 100);
        $this->sku = $sku;
        $this->image_url = $image_url;
        $this->stock = $stock;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getPriceInEuros(): float
    {
        return $this->price / 100;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getImage(): string
    {
        return $this->image_url;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): void
    {
        $this->stock = $stock;
    }

    public function increaseStock(int $stock = 1): void
    {
        $this->stock += $stock;
    }

    public function decreaseStock(int $stock = 1): void
    {
        if ($this->stock - $stock < 0) {
            throw new InsufficientStockException($this->sku, $stock, $this->stock);
        }

        $this->stock -= $stock;
    }
}
