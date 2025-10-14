# Vending-Machine

This project models a vending machine and its internal state during operation. The machine accepts coins, vends products, gives change, and supports a service mode to refill items and change.

## Requirements

- PHP 8.3
- Composer
- PHP extensions required by Laravel: `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`

## Features

- Accepts coins: `0.05`, `0.10`, `0.25`, `1.00`
- Primary products:
  - Water → 0.65 €
  - Juice → 1.00 €
  - Soda → 1.50 €
- Returns coins if requested
- Returns change if inserted money exceeds the product price
- Tracks:
  - Available items (name, price, stock)
  - Available change (coins in machine)
  - Inserted money
- Service mode:
  - Refill items
  - Set available coins in the machine

## Installation

1. Clone the repository:

```bash
git clone https://github.com/luiss619/Vending-Machine
cd vending-machine
```

2. Install dependencies:

```bash
composer install
```

3. Copy .env and set a key:

```bash
cp .env.example .env
php artisan key:generate
```

4. Start the program:

```bash
php artisan serve
```

Open http://127.0.0.1:8000 to interact with the machine.

## Estructure

```bash
vending-machine
├─ app
│  ├─ Application
│  │  └─ VendingMachine
│  │     ├─ DTO
│  │     │  ├─ BuyItemRequestDTO.php
│  │     │  ├─ BuyItemResponseDTO.php
│  │     │  ├─ ChangeCoinsResponseDTO.php
│  │     │  ├─ ErrorResponseDTO.php
│  │     │  ├─ InsertCoinRequestDTO.php
│  │     │  ├─ UpdateCoinsMachineRequestDTO.php
│  │     │  ├─ UpdateStockRequestDTO.php
│  │     │  └─ UpdateStockResponseDTO.php
│  │     └─ UseCase
│  │        ├─ BuyItemUseCase.php
│  │        ├─ InsertCoinUseCase.php
│  │        ├─ ReturnCoinsUseCase.php
│  │        ├─ ServiceLoadUseCase.php
│  │        ├─ UpdateCoinsMachineUseCase.php
│  │        └─ UpdateStockProductUseCase.php
│  ├─ Domain
│  │  └─ VendingMachine
│  │     ├─ Entity
│  │     │  ├─ Coin.php
│  │     │  └─ Product.php
│  │     ├─ Exception
│  │     │  ├─ CoinNotAcceptedException.php
│  │     │  ├─ InsufficientBalanceException.php
│  │     │  ├─ InsufficientCoinsException.php
│  │     │  ├─ InsufficientCoinsInMachineException.php
│  │     │  ├─ InsufficientStockException.php
│  │     │  └─ ProductNotAcceptedException.php
│  │     └─ Service
│  │        └─ VendingMachineService.php
│  └─ Infrastructure
│     └─ Http
│        └─ Controllers
│           └─ VendingMachineController.php
├─ bootstrap
│  └─ app.php
├─ config
├─ database
├─ public
│  ├─ index.php
│  ├─ vending.css
│  └─ img
│     ├─ juice.jpg
│     ├─ soda.jpg
│     └─ water.jpg
├─ resources
│  └─ views
│     └─ vending.blade.php
├─ routes
│  └─ web.php
├─ tests
│  ├─ Fakes
│  │  └─ FakeVendingMachineService.php
│  ├─ Feature
│  │  └─ VendingMachineController
│  │     ├─ BuyItemTest.php
│  │     ├─ IndexTest.php
│  │     ├─ InsertCoinTest.php
│  │     ├─ ReturnCoinsTest.php
│  │     ├─ UpdateCoinsMachineTest.php
│  │     └─ UpdateStockProductTest.php
│  └─ Unit
│     └─ Application
│        └─ VendingMachine
│           └─ UseCase
│              ├─ BuyItemUseCaseTest.php
│              ├─ InsertCoinUseCaseTest.php
│              ├─ ReturnCoinsUseCaseTest.php
│              ├─ ServiceLoadUseCaseTest.php
│              ├─ UpdateCoinsMachineUseCaseTest.php
│              └─ UpdateStockProductUseCaseTest.php
├─ vendor
├─ .gitignore
├─ composer.json
├─ composer.lock
└─ README.md
```

## Running Tests

To run the unit and feature tests, use:

```bash
php artisan test
```

## Examples

### CUSTOMER MODE

```bash
Example 1: 
Add 0.05 € x1
Add 0.10 € x1
Add 0.25 € x1
Add 1.00 € x1
Balance: 1.40 €

Press return coins

Result: 
Balance: 0.00 €
Coins returned:
1 x 0.05 €
1 x 0.10 €
1 x 0.25 €
1 x 1.00 €
```

```bash
Example 2: 
Balance: 0.00 €

Press return coins

Result: 
Balance: 0.00 €
There are no coins to return
```

```bash
Example 3: 
Add 0.10 € x6
Balance: 0.60 €

Press image watter

Result: 
Balance: 0.60 €
Not enough money for Water.
Missing: 0.05 €
```

```bash
Example 4:
Add 0.05 € x1
Add 0.10 € x6
Balance: 0.65 €

Press image watter

Result: 
Balance: 0.00 €
You bought Water
Your change is 0.00 €
```

```bash
Example 5:
Add 0.10 € x7
Balance: 0.70 €

Press image watter

Result: 
Balance: 0.00 €
You bought Water
Your change is 0.05 €
Coin: 0.05 € x 1
```

### SERVICE MODE

```bash
Example 6:
WATER Stock: 5

Press Add Stock +1

Result: 
WATER Stock: 6
```

```bash
Example 7:
WATER Stock: 5

Press Remove Stock -1

Result: 
WATER Stock: 4
```

```bash
Example 8:
WATER Stock: 0

Press Remove Stock -1

Result: 
Cannot remove 1 of water stock.
```

```bash
Example 9:
0.05 € Coin in machine: 10

Press Add +1

Result: 
0.05 € Coin in machine: 11
```

```bash
Example 10:
0.05 € Coin in machine: 10

Press Remove -1

Result: 
0.05 € Coin in machine: 9
```

```bash
Example 11:
0.05 € Coin in machine: 0

Press Remove -1

Result: 
Cannot remove 1 coin of 0.05 €.
No coins available.
```