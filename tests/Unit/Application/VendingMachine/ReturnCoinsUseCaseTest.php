<?php

namespace Tests\Unit\VendingMachine\UseCase;

use App\Application\VendingMachine\UseCase\ReturnCoinsUseCase;
use App\Domain\VendingMachine\Entity\Coin;
use Tests\TestCase;
use Tests\Fakes\FakeVendingMachineService;

class ReturnCoinsUseCaseTest extends TestCase
{
    public function test_ok_no_introduced(): void
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

        $useCase = new ReturnCoinsUseCase($service);
        $response = $useCase->execute();

        $this->assertEquals(0, $service->getBalance());
        $this->assertEmpty($response->coins_introduced);

        foreach (FakeVendingMachineService::$AVAILABLE_COINS as $coin) {
            $this->assertArrayHasKey($coin, $response->coins_machine);
            $this->assertEquals($default_quantity, $response->coins_machine[$coin]);
        }
    }

    public function test_ok_with_introduced(): void
    {
        $service = new FakeVendingMachineService();

        $default_quantity = 10;
        $introduced_quantity = 2;
        $service->setCoinsMachine([
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[0]['value'], $default_quantity),
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[1]['value'], $default_quantity),
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[2]['value'], $default_quantity + $introduced_quantity),
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[3]['value'], $default_quantity)
        ]);
        $service->setCoinsIntroduced([
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[2]['value'], $introduced_quantity)
        ]);
        $service->setBalance(50);

        $useCase = new ReturnCoinsUseCase($service);
        $response = $useCase->execute();

        $this->assertEquals(0, $service->getBalance());
        $this->assertArrayHasKey(FakeVendingMachineService::$AVAILABLE_COINS[2], $response->coins_introduced);
        $this->assertEquals($introduced_quantity, $response->coins_introduced[FakeVendingMachineService::$AVAILABLE_COINS[2]]);

        foreach (FakeVendingMachineService::$AVAILABLE_COINS as $coin) {
            $this->assertArrayHasKey($coin, $response->coins_machine);
            $this->assertEquals($default_quantity, $response->coins_machine[$coin]);
        }
    }
}
