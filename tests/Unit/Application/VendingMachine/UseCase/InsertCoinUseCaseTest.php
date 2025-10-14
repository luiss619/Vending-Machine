<?php

namespace Tests\Unit\Application\VendingMachine\UseCase;

use App\Application\VendingMachine\DTO\InsertCoinRequestDTO;
use App\Application\VendingMachine\UseCase\InsertCoinUseCase;
use Tests\TestCase;
use Tests\Fakes\FakeVendingMachineService;

class InsertCoinUseCaseTest extends TestCase
{
    public function test_ok(): void
    {
        $service = new FakeVendingMachineService();
        $useCase = new InsertCoinUseCase($service);

        $coin = FakeVendingMachineService::$AVAILABLE_COINS[2];
        $spected_balance = $coin + FakeVendingMachineService::$DEFAULT_BALANCE;
        $dto = new InsertCoinRequestDTO(coin: FakeVendingMachineService::$AVAILABLE_COINS[2]);
        $response = $useCase->execute($dto);

        $this->assertEquals($spected_balance, $service->getBalance());
        $this->assertArrayHasKey($coin, $service->getCoinsIntroducedData());
        $this->assertEquals($spected_balance / 100, $response->balance);
    }

    public function test_error_coin_not_accepted(): void
    {
        $service = new FakeVendingMachineService();
        $useCase = new InsertCoinUseCase($service);

        $dto = new InsertCoinRequestDTO(coin: fake()->numberBetween(1000, 9999));
        $response = $useCase->execute($dto);

        $this->assertEquals(FakeVendingMachineService::$DEFAULT_BALANCE, $service->getBalance());
        $this->assertMatchesRegularExpression('/Coin .* not accepted/', $response->error);
    }
}
