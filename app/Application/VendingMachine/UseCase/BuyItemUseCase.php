<?php

namespace App\Application\VendingMachine\UseCase;

use App\Domain\VendingMachine\Exception\InsufficientBalanceException;
use App\Domain\VendingMachine\Exception\InsufficientCoinsInMachineException;
use App\Domain\VendingMachine\Exception\InsufficientStockException;
use App\Domain\VendingMachine\Exception\ProductNotAcceptedException;
use App\Domain\VendingMachine\Service\VendingMachineService;
use App\Application\VendingMachine\DTO\BuyItemRequestDTO;
use App\Application\VendingMachine\DTO\BuyItemResponseDTO;
use App\Application\VendingMachine\DTO\ErrorResponseDTO;

class BuyItemUseCase
{
    private VendingMachineService $service;

    public function __construct(VendingMachineService $service)
    {
        $this->service = $service;
    }

    public function execute(BuyItemRequestDTO $request): BuyItemResponseDTO|ErrorResponseDTO
    {
        try {
            $result = $this->service->buyItem($request->sku);

            return new BuyItemResponseDTO(
                balance: $this->service->getBalanceEuro(),
                coins_machine: $this->service->getCoinsMachineData(),
                coins_introduced: $this->service->getCoinsIntroducedData(),
                product: $result['product'],
                change_message: $result['change_message'],
                new_stock: $result['new_stock']
            );
        } catch (InsufficientBalanceException | InsufficientCoinsInMachineException | InsufficientStockException | ProductNotAcceptedException $e) {
            return new ErrorResponseDTO(
                balance: $this->service->getBalanceEuro(),
                error: $e->getMessage()
            );
        }
    }
}
