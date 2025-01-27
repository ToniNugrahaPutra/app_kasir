@extends('layouts.main')

@section('title', 'Edit Menu')

@section('container')
<h1 class="app-page-title">Edit Menu</h1>
<form class="settings-form" action="/menu/{{ $product->id }}" method="post" enctype="multipart/form-data">
    @csrf
    @method('put')
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
                        <input type="text" class="form-control @error('modal') is-invalid @enderror" id="modal" name="modal" value="{{ old('modal', number_format($product->purchase_price,0,',','.')) }}" required>
                        @error('modal')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Kategori</label>
                        <div class="d-flex justify-content-between mb-1 gap-2">
                            <select class="form-select" name="category" id="category">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
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
                            <input type="text" name="price" class="form-control" id="price" value="{{ old('price', number_format($product->productPrice->where('price_category_id', 1)->first()->price,0,',','.') )}}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Harga Member (satuan)</label>
                                <input type="text" name="price" class="form-control" id="price" value="{{ old('price', number_format($product->productPrice->where('price_category_id', 3)->first()->price,0,',','.') )}}" placeholder="(jika ada)" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label">Harga Grosir</label>
                        <div class="card bg-white p-3">
                            <!-- Harga Grosir -->
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Jumlah Min.</th>
                                    <th>Harga Satuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="number" class="form-control"></td>
                                    <td><input type="text" class="form-control"></td>
                                    <td>
                                        <button class="btn"><i class="fa-solid fa-trash"></i></button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <button type="submit" class="btn app-btn-info" >Simpan</button>
                    <a href="/menu/" class="btn btn-danger text-white" role="button">Batal</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('script')
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
