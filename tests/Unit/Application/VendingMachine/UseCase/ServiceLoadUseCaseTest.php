<?php

namespace Tests\Unit\Application\VendingMachine\UseCase;

use App\Application\VendingMachine\UseCase\ServiceLoadUseCase;
use App\Domain\VendingMachine\Entity\Coin;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;
use Tests\Fakes\FakeVendingMachineService;

class ServiceLoadUseCaseTest extends TestCase
{
    public function test_ok(): void
    {
        Session::start();
        $service = new FakeVendingMachineService();

        $loadUseCase = new ServiceLoadUseCase($service);
        $loadUseCase->execute();

        $this->assertEquals(0, $service->getBalance());
        $this->assertCount(count(Session::get('products')), $service->getProducts());
        $this->assertCount(count(Session::get('coins_machine')), $service->getCoinsMachine());
        $this->assertEmpty($service->getCoinsIntroducedData());

        $new_balance = 250;
        Session::put('balance', $new_balance);
        Session::put('coins_machine', [
            '5' => new Coin(0.05, 5),
            '10' => new Coin(0.15, 5)
        ]);
        Session::put('coins_introduced', [
            '5' => new Coin(0.05, 5),
            '10' => new Coin(0.15, 5)
        ]);

        $loadUseCase->execute();

        $this->assertEquals($new_balance, $service->getBalance());
        $this->assertCount(count(Session::get('coins_machine')), $service->getCoinsMachine());
        $this->assertCount(count(Session::get('coins_introduced')), $service->getCoinsIntroducedData());
    }
}
