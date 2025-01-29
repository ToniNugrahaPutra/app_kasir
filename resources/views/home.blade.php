@extends('layouts.main')

@section('container')

@role('owner')
    @section('container')
        @include('dashboard.owner')
    @endsection
@endrole

@role('cashier')
    @section('container')
        @include('dashboard.cashier')
    @endsection
@endrole

@role('gudang')
    @section('container')
        @include('dashboard.gudang')
    @endsection
@endrole

@endsection
