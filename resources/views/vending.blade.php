<!DOCTYPE html>
<html>
<head>
    <title>Vending Machine</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="/vending.css">
</head>
<body>
    <div class="container mt-3">
        <div class="text-center mb-5">
            <h1>Vending Machine</h1><hr>
        </div>
        <div class="row">
            <div class="col-12 col-md-9">
                <div class="card mb-5">
                    <div class="card-header">Products</div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($products as $product)
                                <div class="col-12 col-md-4 text-center">
                                    @if($mode === 'service')
                                        <button class="btn btn_product btn_product_stock" onclick="addStockProduct('{{ $product->getSku() }}')">
                                            <img src="{{ $product->getImage() }}" class="img_product" />
                                            <b>{{ $product->getName() }}</b> [{{ number_format($product->getPriceInEuros(), 2) }} €]<br>
                                            Stock: <span id="stock_{{ $product->getSku() }}">{{ $product->getStock() }}</span>
                                        </button>
                                    @else
                                        <button class="btn btn_product" onclick="buyProduct('{{ $product->getSku() }}')">
                                            <img src="{{ $product->getImage() }}" class="img_product" />
                                            <b>{{ $product->getName() }}</b> [{{ number_format($product->getPriceInEuros(), 2) }} €]<br>
                                            Stock: <span id="stock_{{ $product->getSku() }}">{{ $product->getStock() }}</span>
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                            @if($mode === 'service')
                                <div class="col-12 text-center mt-4">
                                    <span>Click on each product to add stock it into the machine.</span>
                                </div>                                
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card mb-5">
                    <div class="card-header">Insert Coins</div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($coins as $coin)
                                <div class="col-12 col-md-3">
                                    <div class="d-flex justify-content-center">
                                        @if($mode === 'service')
                                            <div class="btn btn_coin_machine">
                                                {{ number_format(($coin->getValue() / 100), 2) }} €
                                            </div>
                                            <div class="change_coin_machine">
                                                <button class="btn btn-danger" onclick="insertCoinMachine({{ $coin->getValue() }}, 'subtract')">-</button>
                                                <button class="btn btn-success" onclick="insertCoinMachine({{ $coin->getValue() }}, 'add')">+</button>
                                            </div>
                                        @else
                                            <button class="btn btn-primary btn_coin" onclick="insertCoin({{ $coin->getValue() }})">
                                                {{ number_format(($coin->getValue() / 100), 2) }} €
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            @if($mode === 'service')
                                <div class="col-12 text-center mt-4">
                                    <span>Click on each coin to insert it into the machine.</span>
                                </div>                                
                            @endif
                        </div>
                    </div>
                </div><hr>
                <div class="card p-3">
                    <div class="row">
                        <div class="col-6 text-center">
                            <a class="btn btn-default btn_change_mode {{ $mode !== 'service' ? 'btn_change_mode_active' : '' }}" href="/vending">MODE CLIENT</a>
                        </div>
                        <div class="col-6 text-center">
                            <a class="btn btn-default btn_change_mode {{ $mode === 'service' ? 'btn_change_mode_active' : '' }}" href="/vending?mode=service">MODE SERVICE</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card p-3 mb-3">
                    <div class="row">
                        <div class="col-6">
                            <span>Current Balance:</span>
                        </div>
                        <div class="col-6 text-end">
                            <h4><span id="balance">{{ number_format($balance, 2, '.', '') }}</span> €</h4>
                        </div>
                    </div>
                    @if($mode !== 'service')
                        <hr><button class="btn btn-primary" onclick="returnCoin()">Return Coin</button>
                    @endif
                    <div class="text-center">
                        <div id="message" class="mt-2"></div>
                    </div>                    
                </div>
                <div id="coins_machine" class="text-center card p-3 mb-3 {{ $mode !== 'service' ? 'd-none' : '' }}">
                    <p class="mb-0">Coins Machine</p>
                    <div id="coins_machine_table"></div>
                </div>
                <div id="coins_introduced" class="text-center card p-3 mb-3 {{ $mode === 'service' ? 'd-none' : '' }}">
                    <p class="mb-0">Coins Introduced</p>
                    <div id="coins_introduced_table"></div>
                </div>                     
            </div>
        </div>        
    </div>
    <script>
        let table_coins_machine = 'coins_machine_table';
        let table_coins_introduced = 'coins_introduced_table';

        document.addEventListener("DOMContentLoaded", function () {
            updateBalance({{ $balance }});
            renderTable(table_coins_machine, @json($coins_machine));
            renderTable(table_coins_introduced, @json($coins_introduced));
        });

        function renderTable(target_id, data) {
            let html = '';
            if (!data || Object.keys(data).length === 0) {
                html += '<div class="text-center"><p class="mb-0 msg_no_coins">Empty</p></div>';
            } else {
                html += '<table class="table table-hover mb-0" cellpadding="5"><tr><th>Coin</th><th>Quantity</th></tr>';
                for (const [coin, qty] of Object.entries(data)) {
                    html += `<tr><td>${(coin / 100).toFixed(2)} €</td><td>${qty}</td></tr>`;
                }
                html += '</table>';
            }
            document.getElementById(target_id).innerHTML = html;
        }

        function updateBalance(value) {
            document.getElementById('balance').innerText = value.toFixed(2);
        }

        function showMessage(msg, class_name = '') {
            const el = document.getElementById('message');
            el.innerHTML = msg;

            if (class_name) {
                el.classList.add(class_name);
                setTimeout(() => {
                    el.classList.remove(class_name);
                    el.innerHTML = '';
                }, 3000);
            }
        }

        function insertCoin(coin) {
            axios.post('/vending/insert-coin', { coin: coin })
                .then(res => {
                    if (res.data.error) {
                        showMessage(res.data.error, 'error');
                    } else {
                        renderTable(table_coins_machine, res.data.coins_machine);
                        renderTable(table_coins_introduced, res.data.coins_introduced);
                        updateBalance(res.data.balance);
                    }                    
                });
        }

        function insertCoinMachine(coin, operation) {
            axios.post('/vending/insert-coin-machine', { coin: coin, operation: operation })
                .then(res => {
                    if (res.data.error) {
                        showMessage(res.data.error, 'error');
                    } else {
                        renderTable(table_coins_machine, res.data.coins_machine);
                    }                    
                });
        }

        function returnCoin() {
            axios.post('/vending/return-coin')
                .then(res => {
                    updateBalance(res.data.balance);
                    renderTable(table_coins_machine, res.data.coins_machine);
                    renderTable(table_coins_introduced, {});

                    const coins_introduced = res.data.coins_introduced;

                    if (!coins_introduced || Object.keys(coins_introduced).length === 0) {
                        showMessage('There are no coins to return', 'blink');
                        return;
                    }

                    let parts = [];
                    for (const [coin, qty] of Object.entries(coins_introduced)) {
                        parts.push(`${qty} x ${(coin / 100).toFixed(2)} €`);
                    }

                    showMessage('Coins returned:<br>' + parts.join('<br>'), 'blink');
                });
        }

        

        
    </script>
</body>
</html>
