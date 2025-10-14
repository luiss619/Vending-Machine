<?php

namespace Tests\Feature;

use App\Infrastructure\Http\Controllers\VendingMachineController;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Fakes\FakeVendingMachineService;

class UpdateCoinsMachineTest extends TestCase
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
    }

    public function test_ok(): void
    {
        $coin = FakeVendingMachineService::$AVAILABLE_COINS[2];
        $request = new Request([
            'coin' => $coin,
            'operation' => FakeVendingMachineService::$AVAILABLE_METHODS[0]
        ]);

        $initial_quantity = $this->service->getCoinsMachineData()[$coin] ?? 0;

        $response = $this->controller->updateCoinsMachine($request);
        $data = $response->getData(true);

        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertArrayHasKey('coins_machine', $data);
        $this->assertEquals($initial_quantity + 1, $data['coins_machine'][$coin]);
    }

    public function test_invalid_coin(): void
    {
        $request = new Request([
            'coin' => fake()->numberBetween(1000, 9999),
            'operation' => FakeVendingMachineService::$AVAILABLE_METHODS[0]
        ]);

        $response = $this->controller->updateCoinsMachine($request);
        $data = $response->getData(true);

        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertArrayHasKey('error', $data);
    }
}
