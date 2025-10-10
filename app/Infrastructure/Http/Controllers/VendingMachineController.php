<?php

namespace App\Infrastructure\Http\Controllers;

use App\Infrastructure\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VendingMachineController extends Controller
{
    public function __construct() {}

    public function index() {}

    public function insertCoin(Request $request) {}

    public function returnCoins() {}

    public function buyItem(Request $request) {}
}
