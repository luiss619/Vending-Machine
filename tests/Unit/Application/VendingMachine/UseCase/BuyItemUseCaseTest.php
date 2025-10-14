<?php

namespace Tests\Unit\VendingMachine\UseCase;

use App\Application\VendingMachine\UseCase\BuyItemUseCase;
use App\Application\VendingMachine\DTO\BuyItemRequestDTO;
use App\Application\VendingMachine\DTO\BuyItemResponseDTO;
use App\Application\VendingMachine\DTO\ErrorResponseDTO;
use App\Domain\VendingMachine\Entity\Coin;
use Tests\TestCase;
use Tests\Fakes\FakeVendingMachineService;

class BuyItemUseCaseTest extends TestCase
{
    public function test_ok(): void
    {
        $service = new FakeVendingMachineService();

        $default_quantity = 10;
        $introduced_quantity = 6;
        $service->setCoinsMachine([
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[0]['value'], $default_quantity),
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[1]['value'], $default_quantity + $introduced_quantity),
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[2]['value'], $default_quantity),
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[3]['value'], $default_quantity)
        ]);
        $service->setCoinsIntroduced([
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[1]['value'], $introduced_quantity),
        ]);
        $service->setBalance(60);

        $useCase = new BuyItemUseCase($service);
        $dto = new BuyItemRequestDTO(sku: FakeVendingMachineService::$DEFAULT_PRODUCTS_DATA[1]['sku']);
        $response = $useCase->execute($dto);

        $this->assertInstanceOf(BuyItemResponseDTO::class, $response);
        $this->assertEquals(0, $service->getBalance());
        $this->assertArrayHasKey('coins_machine', (array)$response);
        $this->assertArrayHasKey('coins_introduced', (array)$response);
        $this->assertArrayHasKey('change_message', (array)$response);
        $this->assertArrayHasKey('new_stock', (array)$response);
        $this->assertEquals(FakeVendingMachineService::$DEFAULT_PRODUCTS_DATA[1]['stock'] - 1, $response->new_stock);
        $this->assertMatchesRegularExpression(
            '/^You bought .+<br> Your change is 0\.00 €$/',
            $response->change_message
        );

        $default_quantity = 10;
        $introduced_quantity = 7;
        $service->setCoinsMachine([
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[0]['value'], $default_quantity),
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[1]['value'], $default_quantity + $introduced_quantity),
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[2]['value'], $default_quantity),
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[3]['value'], $default_quantity)
        ]);
        $service->setCoinsIntroduced([
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[1]['value'], $introduced_quantity),
        ]);
        $service->setBalance(70);

        $dto = new BuyItemRequestDTO(sku: FakeVendingMachineService::$DEFAULT_PRODUCTS_DATA[1]['sku']);
        $response = $useCase->execute($dto);

        $this->assertInstanceOf(BuyItemResponseDTO::class, $response);
        $this->assertEquals(0, $service->getBalance());
        $this->assertArrayHasKey('coins_machine', (array)$response);
        $this->assertArrayHasKey('coins_introduced', (array)$response);
        $this->assertArrayHasKey('change_message', (array)$response);
        $this->assertArrayHasKey('new_stock', (array)$response);
        $this->assertEquals(FakeVendingMachineService::$DEFAULT_PRODUCTS_DATA[1]['stock'] - 2, $response->new_stock);
        $this->assertMatchesRegularExpression(
            '/^You bought .+<br> Your change is .+ €/',
            $response->change_message
        );
        $this->assertStringNotContainsString('Your change is 0.00 €', $response->change_message);
    }

    public function test_error_invalid_sku(): void
    {
        $service = new FakeVendingMachineService();
        $useCase = new BuyItemUseCase($service);

        $dto = new BuyItemRequestDTO(sku: fake()->lexify(str_repeat('?', 6)));
        $response = $useCase->execute($dto);

        $this->assertInstanceOf(ErrorResponseDTO::class, $response);
        $this->assertMatchesRegularExpression('/Product .* not accepted/', $response->error);
    }

    public function test_insufficient_balance(): void
    {
        $service = new FakeVendingMachineService();

        $default_quantity = 10;
        $introduced_quantity = 5;
        $service->setCoinsMachine([
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[0]['value'], $default_quantity),
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[1]['value'], $default_quantity + $introduced_quantity),
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[2]['value'], $default_quantity),
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[3]['value'], $default_quantity)
        ]);
        $service->setCoinsIntroduced([
            new Coin(FakeVendingMachineService::$DEFAULT_COINS_MACHINE_DATA[1]['value'], $introduced_quantity),
        ]);
        $service->setBalance(50);

        $useCase = new BuyItemUseCase($service);
        $dto = new BuyItemRequestDTO(sku: FakeVendingMachineService::$DEFAULT_PRODUCTS_DATA[1]['sku']);
        $response = $useCase->execute($dto);

        $this->assertInstanceOf(ErrorResponseDTO::class, $response);
        $this->assertMatchesRegularExpression(
            '/^Not enough money for .+<br> Missing: .+ €$/',
            $response->error
        );
    }
}
