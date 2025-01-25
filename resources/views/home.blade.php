@extends('layouts.main')

@section('container')

@role('owner')
    @section('container')
        @include('dashboard.manager')
    @endsection
@endrole

@role('cashier')
    @section('container')
        @include('dashboard.cashier')
    @endsection
@endrole

@endsection
