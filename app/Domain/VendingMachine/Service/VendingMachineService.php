<?php

namespace App\Domain\VendingMachine\Service;

use App\Domain\VendingMachine\Entity\Coin;
use App\Domain\VendingMachine\Exception\CoinNotAcceptedException;
use App\Domain\VendingMachine\Exception\ProductNotAcceptedException;
use App\Domain\VendingMachine\Exception\InsufficientBalanceException;
use App\Domain\VendingMachine\Exception\InsufficientCoinsException;
use App\Domain\VendingMachine\Exception\InsufficientCoinsInMachineException;
use App\Domain\VendingMachine\Exception\InsufficientStockException;

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

    public function buyItem(string $sku): array
    {
        if (!isset($this->products[$sku])) {
            throw new ProductNotAcceptedException(sku: $sku);
        }

        $product = $this->products[$sku];

        if (($product->getStock() - 1) < 0) {
            throw new InsufficientStockException(
                sku: $sku,
                requested: 1,
                available: $product->getStock()
            );
        }

        if ($this->balance < $product->getPrice()) {
            throw new InsufficientBalanceException(
                required: $product->getPrice(),
                available: $this->balance,
                productName: $product->getName()
            );
        }

        $change_amount = $this->balance - $product->getPrice();
        $change_message = 'You bought ' . $product->getName() . '<br> Your change is ' . number_format($change_amount / 100, 2, '.', ',') . ' €';
        if ($change_amount > 0) {
            $change_response = $this->getChangeCoins($change_amount);
            if ($change_response['change_amount_restant'] > 0) {
                throw new InsufficientCoinsInMachineException(
                    cents: $change_response['change_amount_restant'],
                    price_in_cents: $product->getPrice(),
                    product_name: $product->getName()
                );
            }
            foreach ($change_response['change'] as $coin => $value) {
                $change_message .= '<br> Coin: ' . number_format($coin / 100, 2, '.', '') . ' € x ' . $value;
            }
            $this->updateMachineCoinsAfterSell($change_response['change']);
        }

        $this->balance = 0;
        $this->coins_introduced = [];
        $product->decreaseStock();

        session([
            'balance' => $this->balance,
            'coins_introduced' => $this->coins_introduced,
        ]);

        return [
            'product' => $product->getName(),
            'change_message' => $change_message,
            'new_stock' => $product->getStock()
        ];
    }

    private function getChangeCoins(int $change_amount): array
    {
        $change = [];
        $coins = array_reverse(array_keys($this->coins_machine));

        foreach ($coins as $coin) {
            $count = intdiv($change_amount, $coin);
            $obj_coin = $this->coins_machine[$coin];
            $quantity_coin = $obj_coin->getQuantity();
            if ($count > 0 && ($quantity_coin - $count) >= 0) {
                $change[$coin] = $count;
                $change_amount -= $count * $coin;
            }
        }

        return ['change' => $change, 'change_amount_restant' => $change_amount];
    }

    private function updateMachineCoinsAfterSell(array $change): void
    {
        foreach ($change as $coin => $value) {
            $this->coins_machine[$coin]->decreaseQuantity($value);
        }

        session([
            'coins_machine' => $this->coins_machine
        ]);
    }

    public function updateCoinsMachine(int $cents, string $operation): void
    {
        if (!isset($this->coins_machine[$cents])) {
            throw new CoinNotAcceptedException($cents / 100);
        }

        $method = $operation === 'add' ? 'increaseQuantity' : 'decreaseQuantity';
        $this->coins_machine[$cents]->$method();

        if ($this->coins_machine[$cents]->getQuantity() < 0) {
            throw new InsufficientCoinsException($cents, 1, $this->coins_machine[$cents]->getQuantity());
        }

        session([
            'coins_machine' => $this->coins_machine,
        ]);
    }

    public function updateStockProduct(string $sku, string $operation): array
    {
        if (!isset($this->products[$sku])) {
            throw new ProductNotAcceptedException($sku);
        }

        $method = $operation === 'add' ? 'increaseStock' : 'decreaseStock';
        $this->products[$sku]->$method();

        if ($this->products[$sku]->getStock() < 0) {
            throw new InsufficientStockException($sku, 1, $this->products[$sku]->getStock());
        }

        session([
            'products' => $this->products,
        ]);

        return ['new_stock' => $this->products[$sku]->getStock()];
    }
}
