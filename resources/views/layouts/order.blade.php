<!DOCTYPE html>
<html lang="en">
<head>
    <title>
        {{
            Request::is('transaction/create') ? 'Kasir POS' :
            (Request::is('transaction/*') ? 'Payment' : 'appantuh')
        }}
    </title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('images/logofood.ico') }}">

    <!-- FontAwesome JS-->
    <script defer src="{{ asset('plugins/fontawesome/js/all.min.js') }}"></script>

    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.2.1-web/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('css/payment.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
    @stack('styles')
</head>

<body>
    <header>
        @include('layouts.sidebar')
    </header>
    <div class="app-wrapper" style="margin-left: {{ request()->routeIs('transaction.create') ? '0' : '250px' }}">
        <div class="app-content">
            <div class="container-xxl page-order">
                <div class="row h-100">
                    @yield('container')
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('plugins/popper.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/jquery-3.6.3.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/pos.js') }}"></script>
    {{-- <script src="{{ asset('js/order.js') }}"></script> --}}
    <script src="{{ asset('js/formatmoney.js') }}"></script>
    @stack('scripts')
</body>
</html>

