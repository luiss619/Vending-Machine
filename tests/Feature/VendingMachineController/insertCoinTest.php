<?php

namespace Tests\Feature;

use App\Infrastructure\Http\Controllers\VendingMachineController;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Fakes\FakeVendingMachineService;

class InsertCoinTest extends TestCase
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
        $request = new Request(['coin' => fake()->randomElement($this->service::$AVAILABLE_COINS)]);

        $response = $this->controller->insertCoin($request);
        $data = $response->getData(true);

        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertArrayHasKey('balance', $data);
        $this->assertArrayHasKey('coins_introduced', $data);
    }

    public function test_invalid(): void
    {
        $request = new Request(['coin' => fake()->numberBetween(1000, 9999)]);

        $response = $this->controller->insertCoin($request);
        $data = $response->getData(true);

        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertArrayHasKey('error', $data);
    }
}
