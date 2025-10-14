<?php

namespace Tests\Feature;

use App\Application\VendingMachine\UseCase\BuyItemUseCase;
use App\Infrastructure\Http\Controllers\VendingMachineController;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Fakes\FakeVendingMachineService;

class BuyItemTest extends TestCase
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

        $buyItemProp = $reflection->getProperty('buyItemUseCase');
        $buyItemProp->setAccessible(true);
        $buyItemProp->setValue($this->controller, new BuyItemUseCase($this->service));
    }

    public function test_ok(): void
    {
        $this->service->setBalance(250);
        $request = new Request(['sku' => FakeVendingMachineService::$DEFAULT_PRODUCTS_DATA[1]['sku']]);

        $response = $this->controller->buyItem($request);
        $data = $response->getData(true);

        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertArrayHasKey('change_message', $data);
        $this->assertArrayHasKey('balance', $data);
        $this->assertArrayHasKey('new_stock', $data);
        $this->assertArrayHasKey('coins_machine', $data);
        $this->assertEquals(0, $this->service->getBalance());
    }

    public function test_invalid_sku(): void
    {
        $request = new Request(['sku' => fake()->lexify(str_repeat('?', 6))]);

        $response = $this->controller->insertCoin($request);
        $data = $response->getData(true);

        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertArrayHasKey('error', $data);
    }

    public function test_invalid_balance(): void
    {
        $request = new Request(['sku' => FakeVendingMachineService::$DEFAULT_PRODUCTS_DATA[1]['sku']]);

        $response = $this->controller->buyItem($request);
        $data = $response->getData(true);

        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertArrayHasKey('error', $data);
    }
}
