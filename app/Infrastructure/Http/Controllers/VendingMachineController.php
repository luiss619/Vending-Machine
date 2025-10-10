<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\VendingMachine\DTO\BuyItemRequestDTO;
use App\Application\VendingMachine\DTO\InsertCoinRequestDTO;
use App\Application\VendingMachine\DTO\UpdateCoinsMachineRequestDTO;
use App\Application\VendingMachine\DTO\UpdateStockRequestDTO;
use App\Application\VendingMachine\UseCase\BuyItemUseCase;
use App\Application\VendingMachine\UseCase\InsertCoinUseCase;
use App\Application\VendingMachine\UseCase\UpdateCoinsMachineUseCase;
use App\Application\VendingMachine\UseCase\UpdateStockProductUseCase;
use App\Application\VendingMachine\UseCase\ReturnCoinsUseCase;
use App\Application\VendingMachine\UseCase\ServiceLoadUseCase;
use App\Domain\VendingMachine\Service\VendingMachineService;
use App\Infrastructure\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VendingMachineController extends Controller
{
    private BuyItemUseCase $buyItemUseCase;
    private InsertCoinUseCase $insertCoinUseCase;
    private UpdateCoinsMachineUseCase $updateCoinsMachineUseCase;
    private UpdateStockProductUseCase $updateStockProductUseCase;
    private ReturnCoinsUseCase $returnCoinsUseCase;
    private ServiceLoadUseCase $serviceLoadUseCase;
    private VendingMachineService $service;

    public function __construct()
    {
        $this->service = new VendingMachineService(
            balance: 0,
            products: [],
            coins_machine: [],
            coins_introduced: []
        );
        $this->buyItemUseCase = new BuyItemUseCase($this->service);
        $this->insertCoinUseCase = new InsertCoinUseCase($this->service);
        $this->updateCoinsMachineUseCase = new UpdateCoinsMachineUseCase($this->service);
        $this->updateStockProductUseCase = new UpdateStockProductUseCase($this->service);
        $this->returnCoinsUseCase = new ReturnCoinsUseCase($this->service);
        $this->serviceLoadUseCase = new ServiceLoadUseCase($this->service);

        $this->serviceLoadUseCase->execute();
    }

    public function index(Request $request)
    {
        $mode = $request->mode;

        return view('vending', [
            'mode' => $mode,
            'products' => $this->service->getProducts(),
            'balance' => $this->service->getBalanceEuro(),
            'coins' => $this->service->getCoinsMachine(),
            'coins_machine' => $this->service->getCoinsMachineData(),
            'coins_introduced' => $this->service->getCoinsIntroducedData(),
        ]);
    }

    public function insertCoin(Request $request)
    {
        $dto = new InsertCoinRequestDTO(
            coin: (int)$request->input('coin')
        );
        $response = $this->insertCoinUseCase->execute($dto);

        return response()->json($response);
    }

    public function returnCoins()
    {
        $response = $this->returnCoinsUseCase->execute();

        return response()->json($response);
    }

    public function buyItem(Request $request)
    {
        $dto = new BuyItemRequestDTO(
            sku: (string)$request->input('sku')
        );
        $response = $this->buyItemUseCase->execute($dto);

        return response()->json($response);
    }

    public function updateCoinsMachine(Request $request)
    {
        $dto = new UpdateCoinsMachineRequestDTO(
            coin: (int)$request->input('coin'),
            operation: (string)$request->input('operation')
        );
        $response = $this->updateCoinsMachineUseCase->execute($dto);

        return response()->json($response);
    }

    public function updateStockProduct(Request $request)
    {
        $dto = new UpdateStockRequestDTO(
            sku: (string)$request->input('sku'),
            operation: (string)$request->input('operation')
        );
        $response = $this->updateStockProductUseCase->execute($dto);

        return response()->json($response);
    }
}
