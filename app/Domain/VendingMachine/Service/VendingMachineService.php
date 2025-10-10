<?php

namespace App\Domain\VendingMachine\Service;

use App\Domain\VendingMachine\Entity\Coin;
use App\Domain\VendingMachine\Exception\CoinNotAcceptedException;

class VendingMachineService
{
    private int $balance = 0;
    private array $products = [];
    private array $coins = [];
    private array $coins_machine = [];
    private array $coins_introduced = [];

    public function __construct(int $balance, array $products, array $coins_machine, array $coins_introduced)
    {
        $this->balance = $balance;

        foreach ($products as $product) {
            $this->products[$product->getSku()] = $product;
        }

        foreach ($coins_machine as $coin) {
            $this->coins[$coin->getValue()] = $coin;
            $this->coins_machine[$coin->getValue()] = $coin->getQuantity();
        }

        $this->coins_introduced = $coins_introduced;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function getBalanceEuro(): float
    {
        return ($this->balance / 100);
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function getCoinsMachine(): array
    {
        return $this->coins_machine;
    }

    public function getCoinsMachineData(): array
    {
        $coins = [];
        foreach ($this->coins_machine as $coin) {
            $coins[$coin->getValue()] = $coin->getQuantity();
        }
        return $coins;
    }

    public function getCoinsIntroduced(): array
    {
        return $this->coins_introduced;
    }

    public function getCoinsIntroducedData(): array
    {
        $coins = [];
        foreach ($this->coins_introduced as $coin) {
            $coins[$coin->getValue()] = $coin->getQuantity();
        }
        return $coins;
    }

    public function setBalance(int $balance): void
    {
        $this->balance = $balance;
    }

    public function setProducts(array $products): void
    {
        foreach ($products as $product) {
            $this->products[$product->getSku()] = $product;
        }
    }

    public function setCoinsMachine(array $coins_machine): void
    {
        $this->coins_machine = [];

        foreach ($coins_machine as $coin) {
            $this->coins_machine[$coin->getValue()] = $coin;
        }
    }

    public function setCoinsIntroduced(array $coins_introduced): void
    {
        $this->coins_introduced = [];

        foreach ($coins_introduced as $coin) {
            $this->coins_introduced[$coin->getValue()] = $coin;
        }
    }

    public function insertCoin(int $cents): void
    {
        if (!isset($this->coins_machine[$cents])) {
            throw new CoinNotAcceptedException($cents / 100);
        }
        $this->balance += $cents;
        $this->coins_machine[$cents]->increaseQuantity(1);
        $this->coins_introduced[$cents] = $this->coins_introduced[$cents] ?? new Coin($cents / 100, 0);
        $this->coins_introduced[$cents]->increaseQuantity();

        session([
            'balance' => $this->balance,
            'coins_machine' => $this->coins_machine,
            'coins_introduced' => $this->coins_introduced,
        ]);
    }

    public function returnCoins(): void
    {
        foreach ($this->coins_introduced as $coin) {
            $value = $coin->getValue();
            $quantity = $coin->getQuantity();
            $this->coins_machine[$value]->decreaseQuantity($quantity);
        }

        $this->balance = 0;
        $this->coins_introduced = [];

        session([
            'balance' => $this->balance,
            'coins_introduced' => $this->coins_introduced,
        ]);
    }
}
