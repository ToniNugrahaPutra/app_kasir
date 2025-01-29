@extends('layouts.main')

@section('container')
<div class="container">
    <h2 class="mb-4 text-center">Manajemen Stok Gudang</h2>

    {{-- Statistik Pergerakan Stok --}}
    <div class="row mb-4">
        {{-- Total Produk Stok --}}
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow rounded bg-white">
                <div class="card-body text-center">
                    <div class="avatar mb-3">
                        <img src="images/icons/unicons/box.png" width="40" alt="Total Produk Stok" class="rounded" />
                    </div>
                    <span class="fw-semibold d-block mb-1 text-dark">Total Produk</span>
                    <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> {{ $total_products }}</small>
                </div>
            </div>
        </div>

        {{-- Total Stok Masuk --}}
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow rounded bg-white">
                <div class="card-body text-center">
                    <div class="avatar mb-3">
                        <img src="images/icons/unicons/arrow-up-circle.png" width="40" alt="Stok Masuk" class="rounded" />
                    </div>
                    <span class="fw-semibold d-block mb-1 text-dark">Stok Masuk</span>
                    <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> {{ $total_stock_in }} items</small>
                </div>
            </div>
        </div>

        {{-- Total Stok Keluar --}}
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow rounded bg-white">
                <div class="card-body text-center">
                    <div class="avatar mb-3">
                        <img src="images/icons/unicons/arrow-down-circle.png" width="40" alt="Stok Keluar" class="rounded" />
                    </div>
                    <span class="fw-semibold d-block mb-1 text-dark">Stok Keluar</span>
                    <small class="text-success fw-semibold"><i class="bx bx-down-arrow-alt"></i> {{ $total_stock_out }} items</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Menampilkan Produk Gudang --}}
    <h3 class="mb-4">Daftar Produk di Gudang</h3>
    <div class="row">
        @foreach($products as $product)
            <div class="col-md-4 mb-3">
                <div class="card">
                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">Stok Tersedia: {{ $productStocks->where('product_id', $product->id)->first()->stock ?? 0 }} items</p>
                        <a href="{{ route('gudang.show', $product->id) }}" class="btn btn-primary">Lihat Detail</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Form Pergerakan Stok --}}
    <h3 class="mt-5">Pergerakan Stok</h3>
    <form action="{{ route('gudang.manageStock') }}" method="POST" class="mt-3">
        @csrf
        <div class="form-group">
            <label for="product_id">Produk</label>
            <select name="product_id" id="product_id" class="form-control" required>
                <option value="">Pilih Produk</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="quantity">Jumlah</label>
            <input type="number" name="quantity" id="quantity" class="form-control" required min="1">
        </div>
        <div class="form-group">
            <label for="type">Jenis Pergerakan</label>
            <select name="type" id="type" class="form-control" required>
                <option value="in">Stok Masuk</option>
                <option value="out">Stok Keluar</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success mt-3">Simpan</button>
    </form>
</div>
@endsection