@extends('layouts.main')

@section('title', 'Pilih Outlet')

@section('container')
    <div class="container mt-3">
        <div class="card py-3">
            <div class="card-body">
                <div class="row text-center">
                    <h5 class="card-title">Pilih Outlet</h5>
                </div>
                <div class="row mt-3">
                    @foreach($outlets as $outlet)
                        <div class="col-md-3">
                            <div class="card bg-white p-2 mt-2">
                                <img src="{{ $outlet->logo ? asset('storage/logo-toko/'.$outlet->logo) : asset('images/logo-toko.png') }}" class="card-img-top" alt="...">
                                <div class="card-body">
                                    <small class="text-muted">{{ $outlet->outlet_code }}</small>
                                    <h5 class="card-title">{{ $outlet->name }}</h5>
                                    <p class="card-text">{{ $outlet->address }}</p>
                                    <button class="btn btn-primary" onclick="chooseOutlet({{ $outlet->id }})">Pilih</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function chooseOutlet(outlet_id){
                $.ajax({
                    url: '{{ route('choose-outlet') }}',
                    type: 'get',
                    data: {outlet_id: outlet_id},
                    success: function(response){
                        window.location.href = '/';
                    }
                });
            }

        $(document).ready(function() {
            $('.card').hover(function() {
                $(this).addClass('shadow-lg');
            }, function() {
                $(this).removeClass('shadow-lg');
            });

            //hide sidebar
            $('#app-sidepanel').hide();
        });
    </script>
@endpush

