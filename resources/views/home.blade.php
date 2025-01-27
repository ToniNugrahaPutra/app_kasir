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

@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('#sidepanel-close').click(function() {
                $('#app-sidepanel').removeClass('open');
            });
        });
    </script>
@endpush
