<?php

namespace Tests\Feature;

use App\Infrastructure\Http\Controllers\VendingMachineController;
use Illuminate\Http\Request;
use Tests\TestCase;
use Tests\Fakes\FakeVendingMachineService;

class IndexTest extends TestCase
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
        $request = new Request(['mode' => 'client']);

        $response = $this->controller->index($request);

        $this->assertNotNull($response);
        $this->assertStringContainsString('vending', $response->name());
        $this->assertArrayHasKey('mode', $response->getData());
        $this->assertArrayHasKey('products', $response->getData());
    }
}
