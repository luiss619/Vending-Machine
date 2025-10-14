<?php

namespace Tests\Feature;

use App\Application\VendingMachine\UseCase\UpdateStockProductUseCase;
use App\Infrastructure\Http\Controllers\VendingMachineController;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Fakes\FakeVendingMachineService;

class UpdateStockProductTest extends TestCase
{
    protected Request $request;
    protected FakeVendingMachineService $service;
    protected VendingMachineController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new FakeVendingMachineService();
        $this->controller = new VendingMachineController();
        $reflection = new \ReflectionClass($this->controller);

        $prop = $reflection->getProperty('service');
        $prop->setAccessible(true);
        $prop->setValue($this->controller, $this->service);

        $buyItemProp = $reflection->getProperty('updateStockProductUseCase');
        $buyItemProp->setAccessible(true);
        $buyItemProp->setValue($this->controller, new UpdateStockProductUseCase($this->service));
    }

    public function test_ok(): void
    {
        $sku = FakeVendingMachineService::$DEFAULT_PRODUCTS_DATA[1]['sku'];
        $request = new Request(['sku' => $sku, 'operation' => FakeVendingMachineService::$AVAILABLE_METHODS[0]]);

        $response = $this->controller->updateStockProduct($request);
        $data = $response->getData(true);

        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertArrayHasKey('new_stock', $data);
        $this->assertEquals($this->service->getProducts()[$sku]->getStock(), $data['new_stock']);
    }

    public function test_invalid_sku(): void
    {
        $request = new Request(['sku' => fake()->lexify(str_repeat('?', 6)), 'operation' => FakeVendingMachineService::$AVAILABLE_METHODS[0]]);

        $response = $this->controller->updateStockProduct($request);
        $data = $response->getData(true);

        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertArrayHasKey('error', $data);
    }
}
