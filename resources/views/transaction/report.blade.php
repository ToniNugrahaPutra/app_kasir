<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/images/logofood.ico">
    <title>Laporan Keuntungan</title>

    <!-- FontAwesome JS-->
    <script defer src="/plugins/fontawesome/js/all.min.js"></script>

    <!-- App CSS -->
    <link rel="stylesheet" href="/css/portal.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/fontawesome-free-6.2.1-web/css/all.css">
</head>
<body>
    <div class="container">
        <div class="row my-5 d-flex flex-column justify-content-between">
            <!-- Header -->
            <div class="col d-flex flex-column align-items-center">
                <img src="/images/logo.png" class="mb-3" alt="logo" width="100">
                <h2>Ngelapak.ID</h2>
                <p class="mb-1">Malang</p>
                <p class="mb-1">0852-5747-6522</p>
            </div>
            <hr class="mt-3" style="border: 2px solid black;">

            <!-- Report Title -->
            <div class="col my-5">
                <h3 class="text-center">Laporan Keuntungan</h3>
                <p class="mb-0 text-center">
                    @if(request()->has('data') && request()->get('data') == 'all')
                        All Transactions
                    @elseif(request()->has('data') && request()->get('data') == 'today')
                        Today's Transactions
                    @elseif(request()->has('data') && request()->get('data') == 'thisMonth')
                        This Month's Transactions
                    @elseif(request()->has('month') && request()->has('year'))
                        Transactions for {{ strftime('%B', mktime(0, 0, 0, request()->get('month'), 1)) }} {{ request()->get('year') }}
                    @else
                        Filtered Transactions
                    @endif
                </p>
            </div>
        </div>

        <!-- Report Table -->
        <table class="table my-5">
            <thead>
            <tr>
            <th scope="col">Transaction Date</th>
            <th scope="col">Products</th>
            <th scope="col">No Meja</th>
            <th scope="col">Total Penjualan</th>
            @role('owner')
            <th scope="col">Total Harga Barang </th>
            <th scope="col">Keuntungan</th>
            @endrole
            </tr>
            </thead>
            <tbody>
            @forelse ($data as $item)
            <tr>
            <td>{{ $item->created_at->format('d M Y') }}</td>
            <td>
            @foreach ($item->transaction_details as $detail)
            {{ $detail->product->name }} (x{{ $detail->qty }})<br>
            @endforeach
            </td>
            <td>{{ $item->no_table }}</td>
            <td>Rp {{ number_format($item->total_transaction, 0, ',', '.') }}</td>
            @role('owner')
            <?php
            $totalCost = 0;
            foreach ($item->transaction_details as $detail) {
                $totalCost += $detail->qty * $detail->product->purchase_price;
            }
            $profit = $item->total_transaction - $totalCost;
            ?>
            <td>Rp {{ number_format($totalCost, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($profit, 0, ',', '.') }}</td>
            @endrole
            </tr>
            @empty
            <tr>
            <td colspan="6" class="text-center">No transactions found</td>
            </tr>
            @endforelse
            </tbody>
            @role('owner')
            <tfoot>
            <tr>
                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                <td>Rp {{ number_format($profitReport['revenue'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($profitReport['cost'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($profitReport['profit'], 0, ',', '.') }}</td>
            </tr>
            </tfoot>
            @endrole
        </table>

        <!-- Print Button -->
        <div class="text-center my-5">
            <button id="print-button" class="btn btn-primary">
                <i class="fa-solid fa-print"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Print Function -->
    <script>
        document.getElementById('print-button').addEventListener('click', function () {
            window.print();
        });
    </script>
</body>
</html>