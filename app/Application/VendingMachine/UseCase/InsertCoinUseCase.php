<?php

namespace App\Application\VendingMachine\UseCase;

use App\Domain\VendingMachine\Service\VendingMachineService;
use App\Application\VendingMachine\DTO\InsertCoinRequestDTO;
use App\Application\VendingMachine\DTO\ChangeCoinsResponseDTO;

class InsertCoinUseCase
{
    private VendingMachineService $service;

    public function __construct(VendingMachineService $service)
    {
        $this->service = $service;
    }

    public function execute(InsertCoinRequestDTO $request): ChangeCoinsResponseDTO
    {
        $this->service->insertCoin((int) $request->coin);

        return new ChangeCoinsResponseDTO(
            balance: $this->service->getBalance(),
            coins_machine: $this->service->getCoinsMachineData(),
            coins_introduced: $this->service->getCoinsIntroducedData(),
        );
    }
}
