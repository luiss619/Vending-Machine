<?php

namespace App\Infrastructure\Http\Controllers;

use App\Domain\VendingMachine\Service\VendingMachineService;
use App\Infrastructure\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VendingMachineController extends Controller
{
    private VendingMachineService $service;

    public function __construct()
    {
        $this->service = new VendingMachineService(
            balance: 0,
            products: [],
            coins_machine: [],
            coins_introduced: []
        );
    }

    public function index()
    {
        return view('vending', [
            'products' => $this->service->getProducts(),
            'balance' => $this->service->getBalance(),
            'coins' => $this->service->getCoinsMachine(),
            'coins_machine' => $this->service->getCoinsMachineData(),
            'coins_introduced' => $this->service->getCoinsIntroducedData(),
        ]);
    }

    public function insertCoin(Request $request) {}

    public function returnCoins() {}

    public function buyItem(Request $request) {}
}
