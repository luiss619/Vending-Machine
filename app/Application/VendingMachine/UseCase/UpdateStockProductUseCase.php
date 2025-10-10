<?php

namespace App\Application\VendingMachine\UseCase;

use App\Domain\VendingMachine\Exception\ProductNotAcceptedException;
use App\Domain\VendingMachine\Exception\InsufficientStockException;
use App\Domain\VendingMachine\Service\VendingMachineService;
use App\Application\VendingMachine\DTO\UpdateStockRequestDTO;
use App\Application\VendingMachine\DTO\UpdateStockResponseDTO;
use App\Application\VendingMachine\DTO\ErrorResponseDTO;

class UpdateStockProductUseCase
{
    private VendingMachineService $service;

    public function __construct(VendingMachineService $service)
    {
        $this->service = $service;
    }

    public function execute(UpdateStockRequestDTO $request): UpdateStockResponseDTO|ErrorResponseDTO
    {
        try {
            $response = $this->service->updateStockProduct((string) $request->sku, (string) $request->operation);

            return new UpdateStockResponseDTO(
                new_stock: $response['new_stock']
            );
        } catch (ProductNotAcceptedException | InsufficientStockException $e) {
            return new ErrorResponseDTO(
                balance: $this->service->getBalanceEuro(),
                error: $e->getMessage()
            );
        }
    }
}
