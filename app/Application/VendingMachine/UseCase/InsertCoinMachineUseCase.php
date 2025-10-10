<?php

namespace App\Application\VendingMachine\UseCase;

use App\Domain\VendingMachine\Exception\CoinNotAcceptedException;
use App\Domain\VendingMachine\Service\VendingMachineService;
use App\Application\VendingMachine\DTO\InsertCoinMachineRequestDTO;
use App\Application\VendingMachine\DTO\ChangeCoinsResponseDTO;
use App\Application\VendingMachine\DTO\ErrorResponseDTO;

class InsertCoinMachineUseCase
{
    private VendingMachineService $service;

    public function __construct(VendingMachineService $service)
    {
        $this->service = $service;
    }

    public function execute(InsertCoinMachineRequestDTO $request): ChangeCoinsResponseDTO|ErrorResponseDTO
    {
        try {
            $this->service->insertCoinMachine((int) $request->coin, (string) $request->operation);

            return new ChangeCoinsResponseDTO(
                balance: $this->service->getBalanceEuro(),
                coins_machine: $this->service->getCoinsMachineData(),
                coins_introduced: $this->service->getCoinsIntroducedData(),
            );
        } catch (CoinNotAcceptedException $e) {
            return new ErrorResponseDTO(
                balance: $this->service->getBalanceEuro(),
                error: $e->getMessage()
            );
        }
    }
}
