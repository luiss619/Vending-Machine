<?php

namespace Tests\Unit\VendingMachine\UseCase;

use App\Application\VendingMachine\UseCase\UpdateStockProductUseCase;
use App\Application\VendingMachine\DTO\UpdateStockRequestDTO;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeVendingMachineService;

class UpdateStockProductUseCaseTest extends TestCase
{
    public function test_ok(): void
    {
        $service = new FakeVendingMachineService();
        $useCase = new UpdateStockProductUseCase($service);
        $dto = new UpdateStockRequestDTO(
            sku: FakeVendingMachineService::$DEFAULT_PRODUCTS_DATA[1]['sku'],
            operation: FakeVendingMachineService::$AVAILABLE_METHODS[0]
        );
        //$response = $useCase->execute($dto);

        //$this->assertSame(FakeVendingMachineService::$DEFAULT_PRODUCTS_DATA[1]['sku'] + 1, $response->new_stock);
    }

    public function test_error_invalid_sku(): void
    {
        $service = new FakeVendingMachineService();
        $useCase = new UpdateStockProductUseCase($service);
        $dto = new UpdateStockRequestDTO(
            sku: fake()->lexify(str_repeat('?', 6)),
            operation: FakeVendingMachineService::$AVAILABLE_METHODS[0]
        );
        $response = $useCase->execute($dto);

        $this->assertMatchesRegularExpression('/Product .* not accepted/', $response->error);
    }
}
