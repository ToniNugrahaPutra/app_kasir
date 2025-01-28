@extends('layouts.main')

@section('title', 'Edit Menu')

@section('container')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
<h1 class="app-page-title">Edit Menu</h1>
<form class="settings-form" action="{{ route('menu.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-3 settings-section">
        @error('picture')
            <p class="small text-danger">{{ $message }}</p>
        @enderror
        <div class="col-12 col-md-4 picture-container" style="display: flex; flex-direction: column; justify-content: center;">
            <img src="{{ asset('storage/products/'. $product->image) }}" alt="" class="img-fluid picture-preview">
            <input type="file" id="select-picture" name="image">
            <div class="black-screen">{{ $product->image }} <p> Tekan untuk mengganti </p></div>
        </div>
        <div class="col-12 col-md-8">
            <div class="app-card app-card-settings p-4">
                <div class="app-card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Produk</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="modal" class="form-label">Harga Modal</label>
                        <input type="text" class="form-control @error('modal') is-invalid @enderror" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', number_format($product->purchase_price ?? 0,0,',','.')) }}" required>
                        @error('purchase_price')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Kategori</label>
                        <div class="d-flex justify-content-between mb-1 gap-2">
                            <select class="form-select" name="category_id" id="category_id">
                                <option value="">Tidak Berkategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <a href="/category/create" class="btn btn-primary"><i class="fa-solid fa-plus"></i></a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        @error('description')
                            <p class="small text-danger">{{ $message }}</p>
                        @enderror
                        <input id="description" type="hidden" name="description" value="{{ old('description', $product->description) }}">
                        <trix-editor input="description"></trix-editor>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Harga Umum (satuan)</label>
                                <input type="text" name="umumPrice" class="form-control" value="{{ old('umumPrice', number_format($umumPrice,0,',','.') )}}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label text-danger">Harga Member (satuan)</label>
                                <input type="text" name="memberPrice" class="form-control" value="{{ old('memberPrice', number_format($memberPrice,0,',','.') )}}" placeholder="(jika ada)">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label">Harga Grosir Umum</label>
                        <div class="card bg-white p-3">
                            <!-- Harga Grosir Umum -->
                            <table class="table table-borderless" id="grosir-umum">
                                <thead>
                                <tr>
                                    <th>Jumlah Min.</th>
                                    <th>Harga Satuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($umumGrosir->isNotEmpty())
                                    @foreach ($umumGrosir as $index => $price)
                                        <tr>
                                            <td>
                                                <input type="number" class="form-control min-quantity" name="grosir-umum[{{ $index }}][min_quantity]" value="{{ $price->min_quantity }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control price" name="grosir-umum[{{ $index }}][price]" value="{{ number_format($price->price,0,',','.') }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn" onclick="deleteRow(this)">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>
                                            <input type="number" class="form-control" name="grosir-umum[0][min_quantity]" placeholder="Jumlah Min" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control price" name="grosir-umum[0][price]" placeholder="Harga Satuan" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger" onclick="deleteRow(this)">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary" id="add-grosir-umum">Tambah</button>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label text-danger">Harga Grosir Member</label>
                        <div class="card bg-white p-3">
                            <!-- Harga Grosir Non Member -->
                            <table class="table table-borderless" id="grosir-member">
                                <thead>
                                <tr>
                                    <th>Jumlah Min.</th>
                                    <th>Harga Satuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($memberGrosir->isNotEmpty())
                                    @foreach ($memberGrosir as $index => $price)
                                        <tr>
                                            <td>
                                                <input type="number" class="min-quantity form-control" name="grosir-member[{{ $index }}][min_quantity]" value="{{ $price->min_quantity }}">
                                            </td>
                                            <td>
                                                <input type="text" class="price form-control" name="grosir-member[{{ $index }}][price]" value="{{ number_format($price->price,0,',','.') }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn" onclick="deleteRow(this)"><i class="fa-solid fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>
                                            <input type="number" class="min-quantity form-control" name="grosir-member[0][min_quantity]" placeholder="Jumlah Min" required>
                                        </td>
                                        <td>
                                            <input type="text" class="price form-control" name="grosir-member[0][price]" placeholder="Harga Satuan" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn" onclick="deleteRow(this)"><i class="fa-solid fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary" id="add-grosir-member">Tambah</button>
                        </div>
                    </div>
                    <button type="submit" class="btn app-btn-info">Simpan</button>
                    <a href="/menu/" class="btn btn-danger text-white" role="button">Batal</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/price-edit.js') }}"></script>
<script>
    const select_picture = document.getElementById('select-picture');
    const input_picture = document.getElementById('input-picture');
    const picture_preview = document.querySelector('.picture-preview');
    const black_screen = document.querySelector('.black-screen');

    select_picture.addEventListener('change', function () {
        const files = select_picture.files[0];
        const fileReader = new FileReader();
        fileReader.readAsDataURL(files);
        fileReader.addEventListener("load", function () {
            picture_preview.src = this.result;
            black_screen.innerHTML = `${files.name} <p> click to change </p>`
        });
    })
</script>
@endpush
