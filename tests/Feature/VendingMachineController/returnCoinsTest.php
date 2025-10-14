<?php

namespace Tests\Feature;

use App\Infrastructure\Http\Controllers\VendingMachineController;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Fakes\FakeVendingMachineService;

class ReturnCoinsTest extends TestCase
{
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

    public function test_ok()
    {
        $response = $this->controller->returnCoins();
        $data = $response->getData(true);

        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertArrayHasKey('coins_introduced', $data);
        $this->assertEmpty($data['coins_introduced']);
        $this->assertArrayHasKey('balance', $data);
        $this->assertEquals(0, $data['balance']);
    }
}
