<?php

namespace Tests\Unit\VendingMachine\UseCase;

use App\Application\VendingMachine\DTO\UpdateCoinsMachineRequestDTO;
use App\Application\VendingMachine\UseCase\UpdateCoinsMachineUseCase;
use App\Domain\VendingMachine\Entity\Coin;
use Tests\TestCase;
use Tests\Fakes\FakeVendingMachineService;

class UpdateCoinsMachineUseCaseTest extends TestCase
{
    public function test_ok(): void
    {
        $service = new FakeVendingMachineService();

        $default_quantity = 10;
        $service->setCoinsMachine([
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[0]['value'], $default_quantity),
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[1]['value'], $default_quantity),
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[2]['value'], $default_quantity),
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[3]['value'], $default_quantity)
        ]);
        $service->setCoinsIntroduced([]);
        $service->setBalance(0);

        $useCase = new UpdateCoinsMachineUseCase($service);
        $dto = new UpdateCoinsMachineRequestDTO(
            coin: FakeVendingMachineService::$AVAILABLE_COINS[2],
            operation: FakeVendingMachineService::$AVAILABLE_METHODS[0]
        );
        $response = $useCase->execute($dto);

        $this->assertArrayHasKey(FakeVendingMachineService::$AVAILABLE_COINS[0], $response->coins_machine);
        $this->assertEquals($default_quantity, $response->coins_machine[FakeVendingMachineService::$AVAILABLE_COINS[0]]);
        $this->assertArrayHasKey(FakeVendingMachineService::$AVAILABLE_COINS[1], $response->coins_machine);
        $this->assertEquals($default_quantity, $response->coins_machine[FakeVendingMachineService::$AVAILABLE_COINS[1]]);
        $this->assertArrayHasKey(FakeVendingMachineService::$AVAILABLE_COINS[2], $response->coins_machine);
        $this->assertEquals($default_quantity + 1, $response->coins_machine[FakeVendingMachineService::$AVAILABLE_COINS[2]]);
        $this->assertArrayHasKey(FakeVendingMachineService::$AVAILABLE_COINS[3], $response->coins_machine);
        $this->assertEquals($default_quantity, $response->coins_machine[FakeVendingMachineService::$AVAILABLE_COINS[3]]);

        $dto = new UpdateCoinsMachineRequestDTO(
            coin: FakeVendingMachineService::$AVAILABLE_COINS[2],
            operation: FakeVendingMachineService::$AVAILABLE_METHODS[1]
        );
        $response = $useCase->execute($dto);

        $this->assertArrayHasKey(FakeVendingMachineService::$AVAILABLE_COINS[0], $response->coins_machine);
        $this->assertEquals($default_quantity, $response->coins_machine[FakeVendingMachineService::$AVAILABLE_COINS[0]]);
        $this->assertArrayHasKey(FakeVendingMachineService::$AVAILABLE_COINS[1], $response->coins_machine);
        $this->assertEquals($default_quantity, $response->coins_machine[FakeVendingMachineService::$AVAILABLE_COINS[1]]);
        $this->assertArrayHasKey(FakeVendingMachineService::$AVAILABLE_COINS[2], $response->coins_machine);
        $this->assertEquals($default_quantity, $response->coins_machine[FakeVendingMachineService::$AVAILABLE_COINS[2]]);
        $this->assertArrayHasKey(FakeVendingMachineService::$AVAILABLE_COINS[3], $response->coins_machine);
        $this->assertEquals($default_quantity, $response->coins_machine[FakeVendingMachineService::$AVAILABLE_COINS[3]]);
    }

    public function test_error_coin_not_accepted(): void
    {
        $service = new FakeVendingMachineService();
        $useCase = new UpdateCoinsMachineUseCase($service);

        $dto = new UpdateCoinsMachineRequestDTO(coin: fake()->numberBetween(1000, 9999));
        $response = $useCase->execute($dto);

        $this->assertEquals(FakeVendingMachineService::$DEFAULT_BALANCE, $service->getBalance());
        $this->assertMatchesRegularExpression('/Coin .* not accepted/', $response->error);
    }

    public function test_error_insufficient_coins(): void
    {
        $service = new FakeVendingMachineService();

        $default_quantity = 0;
        $service->setCoinsMachine([
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[0]['value'], $default_quantity),
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[1]['value'], $default_quantity),
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[2]['value'], $default_quantity),
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[3]['value'], $default_quantity)
        ]);
        $service->setCoinsIntroduced([]);
        $service->setBalance(0);

        $useCase = new UpdateCoinsMachineUseCase($service);
        $dto = new UpdateCoinsMachineRequestDTO(
            coin: FakeVendingMachineService::$AVAILABLE_COINS[2],
            operation: FakeVendingMachineService::$AVAILABLE_METHODS[1]
        );
        $response = $useCase->execute($dto);

        $this->assertStringContainsString('Cannot remove 1 coin of', $response->error);
        $this->assertStringContainsString('No coins available', $response->error);
    }
}
