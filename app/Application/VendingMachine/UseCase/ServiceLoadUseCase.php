<?php

namespace App\Application\VendingMachine\UseCase;

use App\Domain\VendingMachine\Entity\Coin;
use App\Domain\VendingMachine\Entity\Product;
use App\Domain\VendingMachine\Service\VendingMachineService;

class ServiceLoadUseCase
{
    private VendingMachineService $service;

    public function __construct(VendingMachineService $service)
    {
        $this->service = $service;
    }

    public function execute(): void
    {
        $balance = session('balance', 0);
        $products = session('products', $this->initProducts());
        $coins_machine = session('coins_machine', $this->initCoins());
        $coins_introduced = session('coins_introduced', []);

        session([
            'balance' => $balance,
            'products' => $products,
            'coins_machine' => $coins_machine,
            'coins_introduced' => $coins_introduced,
        ]);

        $this->service->setBalance($balance);
        $this->service->setProducts($products);
        $this->service->setCoinsMachine($coins_machine);
        $this->service->setCoinsIntroduced($coins_introduced);
    }

    private function initProducts(): array
    {
        return [
            new Product(name: 'Water', price: 0.65, sku: 'water', image_url: '/img/products/water.jpg', stock: 10),
            new Product(name: 'Juice', price: 1.00, sku: 'juice', image_url: '/img/products/juice.jpg', stock: 10),
            new Product(name: 'Soda', price: 1.50, sku: 'soda', image_url: '/img/products/soda.jpg', stock: 10)
        ];
    }
    private function initCoins(): array
    {
        return [
            new Coin(value: 0.05, quantity: 20),
            new Coin(value: 0.10, quantity: 20),
            new Coin(value: 0.25, quantity: 20),
            new Coin(value: 1.00, quantity: 20)
        ];
    }
}
