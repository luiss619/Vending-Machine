<?php

namespace Tests\Fakes;

use App\Domain\VendingMachine\Service\VendingMachineService;
use App\Domain\VendingMachine\Entity\Product;
use App\Domain\VendingMachine\Entity\Coin;

class FakeVendingMachineService extends VendingMachineService
{
    public static array $AVAILABLE_COINS = [5, 10, 25, 100];
    public static array $AVAILABLE_METHODS = ['add', 'subtract'];

    public static float $DEFAULT_BALANCE = 50;
    public static array $DEFAULT_PRODUCTS_DATA = [
        ['name' => 'Water', 'price' => 0.80, 'sku' => 'water', 'img' => '', 'stock' => 5],
        ['name' => 'Soda', 'price' => 0.60, 'sku' => 'soda', 'img' => '', 'stock' => 5],
    ];
    public static array $DEFAULT_COINS_MACHINE_DATA = [
        ['value' => 0.05, 'quantity' => 20],
        ['value' => 0.10, 'quantity' => 25],
        ['value' => 0.25, 'quantity' => 20],
        ['value' => 1.00, 'quantity' => 20],
    ];
    public static array $DEFAULT_COINS_INTRODUCED_DATA = [
        ['value' => 0.10, 'quantity' => 6],
    ];

    public function __construct()
    {
        parent::__construct(balance: 0, products: [], coins_machine: [], coins_introduced: []);

        $this->setBalance(self::$DEFAULT_BALANCE);

        $data_products = [];
        foreach (self::$DEFAULT_PRODUCTS_DATA as $product) {
            $data_products[] = new Product($product['name'], $product['price'], $product['sku'], $product['img'], $product['stock']);
        }
        $this->setProducts($data_products);

        $data_coins_machine = [];
        foreach (self::$DEFAULT_COINS_MACHINE_DATA as $coin) {
            $data_coins_machine[] = new Coin($coin['value'], $coin['quantity']);
        }
        $this->setCoinsMachine($data_coins_machine);

        $data_coins_introduced = [];
        foreach (self::$DEFAULT_COINS_INTRODUCED_DATA as $coin_introduced) {
            $data_coins_introduced[] = new Coin($coin_introduced['value'], $coin_introduced['quantity']);
        }
        $this->setCoinsIntroduced($data_coins_introduced);
    }
}
