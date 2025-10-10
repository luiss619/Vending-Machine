<?php

namespace App\Application\VendingMachine\UseCase;

use App\Domain\VendingMachine\Service\VendingMachineService;
use App\Application\VendingMachine\DTO\ChangeCoinsResponseDTO;

class ReturnCoinsUseCase
{
    private VendingMachineService $service;

    public function __construct(VendingMachineService $service)
    {
        $this->service = $service;
    }

    public function execute(): ChangeCoinsResponseDTO
    {
        $coins_introduced = $this->service->getCoinsIntroducedData();
        $this->service->returnCoins();

        return new ChangeCoinsResponseDTO(
            balance: $this->service->getBalanceEuro(),
            coins_machine: $this->service->getCoinsMachineData(),
            coins_introduced: $coins_introduced,
        );
    }
}
