@extends('layouts.order')

@push('styles')
    <style>
        .sidepanel-hidden {
            left: -400px;
        }
        .sidepanel-visible {
            left: 0;
        }
        .card-image-top {
            width: 100%;
            margin: 0;
            padding: 0;
            background-size: cover;
            background-position: center;
        }
    </style>
@endpush

@section('container')
    @php
        $tables = json_encode($tables);
        echo "
            <script>
                var tables = $tables;
            </script>
        ";
    @endphp
    <div class="col-md-8 p-0 h-100 flex flex-column justify-content-between">
        <div class="hd-menu d-flex align-items-center justify-content-between shadow bg-white">
            <div class="col-sm-5 d-flex align-items-center">
                <a id="back-to-dashboard" class="sidepanel-toggler d-inline-block" href="{{ route('home') }}">
                    <i class="fas fa-arrow-left text-black"></i>
                </a>
                <h5 class="fs-5 fw-bold text-black ms-4 mb-0">Daftar Menu</h5>
            </div>
            <div class="col-sm-5 d-flex align-items-center search-container-tr">
                <div class="search-mobile-trigger search-icon-transaction">
                    <i class="search-mobile-trigger-icon fas fa-search"></i>
                </div>
                <div class="app-search-box sb-tr">
                    <form class="app-search-form">
                        <input type="text" placeholder="Search..." name="search" class="form-control search-input">
                        <button type="submit" class="btn search-btn btn-primary" value="Search"><i
                                class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
        <div class="wp-menu d-flex flex-column">
            <div class="row">
            <div class="menu-tr mt-3 mb-3">
                <ul class="nav nav-tabs d-flex justify-content-center" data-aos="fade-up" data-aos-delay="200">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#all">
                            <h4>Semua</h4>
                        </a>
                    </li>
                    @foreach ($categories as $category)
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" data-bs-target="#{{ $category->name }}">
                            <h4>{{ $category->name }}</h4>
                        </a>
                    </li>
                    @endforeach
                </ul>
                </div>
            </div>
            <div class="tab-content menu-tab overflow-hidden" style="height: 85%" data-aos="fade-up" data-aos-delay="300">
                <div class="tab-pane fade show active" id="all">
                    <div class="row mt-2 px-3">
                            @foreach ($products as $product)
                                <div class="col-md-3 col-sm-6">
                                    <a type="button" data-id="{{ $product->id }}" class="decoration-none text-black" onclick="showProductDetail({{ $product->id }})">
                                    <div class="d-flex flex-column bg-white rounded shadow h-100" data-id="{{ $product->id }}">
                                        <img class="rounded card-image-top" src="{{ asset('storage/products/' . $product->image) }}">
                                        <div class="d-flex justify-content-center flex-column p-2">
                                            <h6 class="text-left mt-2 fs-6 fw-bold text-primary">{{ Str::limit($product->name, 18) }}</h6>
                                            {{-- <div class="text-left">
                                                <small class="text-bold">Rp <span class="fw-bold fs-5">{{ number_format($product->productPrice->where('price_category_id', 1)->first()->price ?? 0, 0, ',', '.') }}</span></small>
                                            </div> --}}
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                    </div>
                </div>

                @foreach ($categories as $category)
                <div class="tab-pane fade" id="{{ $category->name }}">
                    <div class="row mt-2 px-3">
                        @foreach ($products->where('category_id', $category->id) as $product)
                        <div class="col-md-3 col-sm-6">
                            <a type="button" data-id="{{ $product->id }}" class="decoration-none text-black" onclick="showProductDetail({{ $product->id }})">
                            <div class="d-flex flex-column bg-white rounded shadow h-100" data-id="{{ $product->id }}">
                                <img class="rounded card-image-top" src="{{ asset('storage/products/' . $product->image) }}">
                                <div class="d-flex justify-content-center flex-column p-2">
                                    <p class="text-left mt-2 fs-6 fw-bold text-primary">{{ Str::limit($product->name, 18) }}</p>
                                    {{-- <div class="text-left">
                                        <small class="text-bold">Rp <span class="fw-bold fs-5">{{ number_format($product->productPrice->where('price_category_id', 1)->first()->price ?? 0, 0, ',', '.') }}</span></small>
                                    </div> --}}
                                </div>
                            </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Modal Product Detail with Quantity Set --}}
    <div class="modal fade" id="productDetail" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-sm">
            <div class="modal-content shadow" style="background-color: #181818fd">
                <div class="modal-header" id="staticBackdropLabel">
                    <h1 class="modal-title fs-5 text-white" id="exampleModalLabel">Detail Pesanan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="background-color: #fff"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <img id="product-image" class="rounded" src="" alt="" style="width: 100%; height: 200px; object-fit: cover;">
                    </div>
                    <div class="row d-flex flex-column justify-content-between align-items-center">
                        <input type="hidden" id="product-id">
                        <h6 id="product-name" class="fw-semibold text-white mt-1"></h6>
                        <h6 id="product-price" class="fw-semibold fs-5" style="color: yellow;"></h6>
                    </div>
                    <div class="row">
                        <div class="col-md-12 d-flex align-items-center justify-content-between gap-2">
                            <button type="button" class="btn btn-primary" onclick="decrementQuantity()">-</button>
                            <input type="text" id="product-quantity" class="form-control text-center" value="1" min="1">
                            <button type="button" class="btn btn-primary" onclick="incrementQuantity()">+</button>
                        </div>
                        <div class="d-flex justify-content-center mt-2">
                            <button class="btn btn-primary flex-fill" type="button" onclick="addToCart()">Tambah</button>
                        </div>
                    </div>
                    <input type="hidden" id="selected_price_id">
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 h-100 p-0 d-flex flex-column">
        <div class="cart-title d-flex justify-content-between align-items-center p-4 shadow-sm">
            <h5 class="text-white">Detail Pesanan</h5>
            <button class="fas fa-trash text-white" onclick="deleteOrder()" role="button"></button>
        </div>
        <div class="cart-body d-flex flex-column justify-content-between" style="height: 780px;">
            <div class="d-flex justify-content-between p-3 align-items-center">
                <h6 class="fw-semibold text-white ms-2 tables-selected">No Meja : <span id="table_selected">-</span></h6>
                <h6 class="fw-semibold text-white me-2" style="font-size: 13px;">{{ now()->format('Y-m-d') }}</h6>
            </div>

            <div class="align-self-center p-0 m-0" style="width: 90%;">
            <div class="member-code d-flex justify-content-between align-items-center gap-2 mb-3">
                <input class="form-control" type="text" name="member_code" id="member_code" placeholder="Kode Member" autocomplete="off">
                <button id="search_member" class="btn btn-primary" type="button" onclick="searchMember()">Cari</button>
                </div>
            </div>

            <div class="list-order align-self-center rounded p-4 mb-4">
                <div class="menu-order">

                </div>
            </div>
            <form action="{{ route('transaction.store') }}" method="POST" class="align-self-center p-0 m-0" style="width: 90%;">
                @csrf
                <input type="hidden" name="listProduct" id="listProduct">
                <input type="hidden" name="no_table" id="table_selected">
                <input type="hidden" name="customer_id" id="customer_id">

                <div class="discount-code d-flex justify-content-between align-items-center gap-2">
                    <input class="form-control" type="text" name="discount_code" id="discount_code" placeholder="Kode Diskon / Voucher">
                    <button id="apply_discount" class="btn btn-primary" type="button" onclick="applyDiscount()">Terapkan</button>
                </div>

                <div class="cart-payment p-2 d-flex flex-column rounded mt-3">
                    {{-- Diskon --}}
                    <div class="discount-info d-flex justify-content-between align-items-center mt-2 p-2" style="height: 30px;">
                        <h6 class="text-white">Diskon</h6>
                        <h6 class="discount-amount text-white">Rp 0</h6>
                    </div>
                    <div class="subtotal d-flex justify-content-between align-items-center p-2"
                        style="height: 40px;">
                        <h6 class="text-white">Subtotal</h6>
                        <h6 class="sub-total text-white">Rp 0</h6>
                    </div>
                    <div class="ppn d-flex justify-content-between align-items-center p-2" style="height: 30px;">
                        <h6 class="text-white">PPN</h6>
                        <select class="" name="ppn" id="ppn" style="width: 60px;">
                            <option value="10%">10%</option>
                            <option value="11%">11%</option>
                            <option value="12%">12%</option>
                        </select>
                    </div>
                    <hr class="mt-3 text-white">
                    <div class="section-transaction d-flex justify-content-between align-items-center p-2">
                        <h6 class="text-white">Total</h6>
                        <h6 class="total-transaction text-white">Rp 0</h6>
                        <input type="hidden" name="total_transaction">
                    </div>
                    <div class="section-pay d-flex justify-content-between align-items-center p-2">
                        <h6 class="text-white">Pilih Meja</h6>
                        <select class="form-control" name="table_id" id="table_id">
                            @foreach ($tables as $table)
                                <option value="{{ $table->id }}">{{ $table->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit"
                    class="w-100 cart-order p-2 mt-3 mb-3 rounded text-center border-0 text-dark bg-white">
                    Buat Pesanan
                </button>
            </form>
        </div>
    </div>
    <!-- Modal Table -->
    <div class="modal fade" id="table" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-xl">
            <div class="modal-content shadow" style="background-color: #181818fd">
                <div class="modal-header" id="staticBackdropLabel">
                    <h1 class="modal-title fs-5 text-white" id="exampleModalLabel">Pilih Meja</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="background-color: #fff"></button>
                </div>
                <div class="modal-body" style="background-image: url(/images/map-layout.png);">
                    <div class="row h-100">
                        <div class="col-9 p-0">
                            <div class="tables d-flex flex-column justify-content-between h-100">
                                <div class="top d-flex pb-5">
                                    <div class="d-flex justify-content-between align-items-center w-75">
                                        <div class="tab-container position-relative">
                                            <img class="table-A" src="/images/table/meja4.png" width="120"
                                                srcset="" data-table="not-selected" data-number="A1">
                                            <p
                                                class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">
                                                A1</p>
                                        </div>
                                        <div class="tab-container position-relative">
                                            <img class="table-A" src="/images/table/meja4.png" width="120"
                                                srcset="" data-table="not-selected" data-number="A2">
                                            <p
                                                class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">
                                                A2</p>
                                        </div>
                                        <div class="tab-container position-relative">
                                            <img class="table-B" src="/images/table/meja5.png" width="75"
                                                srcset="" data-table="not-selected" data-number="B1">
                                            <p
                                                class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">
                                                B1</p>
                                        </div>
                                        <div class="tab-container position-relative">
                                            <img class="table-B" src="/images/table/meja5.png" width="75"
                                                srcset="" data-table="not-selected" data-number="B2">
                                            <p
                                                class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">
                                                B2</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="midle d-flex justify-content-between align-items-end mt-4">
                                    <div class="tab-container position-relative">
                                        <img class="table-C1" src="/images/table/meja0.png" width="120"
                                            alt="" srcset="" data-table="not-selected" data-number="C1">
                                        <p class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">
                                            C1</p>
                                    </div>
                                    <div class="tab-container position-relative">
                                        <img class="table-C1" src="/images/table/meja0.png" width="120"
                                            alt="" srcset="" data-table="not-selected" data-number="C2">
                                        <p class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">
                                            C2</p>
                                    </div>
                                    <div class="tab-container position-relative">
                                        <img class="table-C1" src="/images/table/meja0.png" width="120"
                                            alt="" srcset="" data-table="not-selected" data-number="C3">
                                        <p class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">
                                            C3</p>
                                    </div>
                                    <div class="tab-container position-relative">
                                        <img class="table-C1" src="/images/table/meja0.png" width="120"
                                            alt="" srcset="" data-table="not-selected" data-number="C4">
                                        <p class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">
                                            C4</p>
                                    </div>
                                    <div class="tab-container position-relative">
                                        <img class="table-C1" src="/images/table/meja0.png" width="120"
                                            alt="" srcset="" data-table="not-selected" data-number="C5">
                                        <p class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">
                                            C5</p>
                                    </div>
                                    <div class="tab-container position-relative">
                                        <img class="table-C1" src="/images/table/meja0.png" width="120"
                                            alt="" srcset="" data-table="not-selected" data-number="C6">
                                        <p class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">
                                            C6</p>
                                    </div>
                                </div>
                                <div class="bottom d-flex justify-content-between">
                                    <div class="tab-container position-relative">
                                        <img class="table-C2" src="/images/table/mejabottom.png" width="75"
                                            alt="" data-table="not-selected" data-number="C7">
                                        <p class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">
                                            C7</p>
                                    </div>
                                    <div class="tab-container position-relative">
                                        <img class="table-C2" src="/images/table/mejabottom.png" width="75"
                                            alt="" data-table="not-selected" data-number="C8">
                                        <p class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">
                                            C8</p>
                                    </div>
                                    <div class="tab-container position-relative">
                                        <img class="table-C2" src="/images/table/mejabottom.png" width="75"
                                            alt="" data-table="not-selected" data-number="C9">
                                        <p class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">
                                            C9</p>
                                    </div>
                                    <div class="tab-container position-relative">
                                        <img class="table-C2" src="/images/table/mejabottom.png" width="75"
                                            alt="" data-table="not-selected" data-number="C10">
                                        <p class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">
                                            C10</p>
                                    </div>
                                    <div class="tab-container position-relative">
                                        <img class="table-C2" src="/images/table/mejabottom.png" width="75"
                                            alt="" data-table="not-selected" data-number="C11">
                                        <p class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">
                                            C11</p>
                                    </div>
                                    <div class="tab-container position-relative">
                                        <img class="table-C2" src="/images/table/mejabottom.png" width="75"
                                            alt="" data-table="not-selected" data-number="C12">
                                        <p class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">
                                            C12</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 d-flex flex-column justify-content-between align-items-end">
                            <div class="tab-container position-relative">
                                <img class="table-D" src="/images/table/meja3.png" width="80" alt=""
                                    srcset="" data-table="not-selected" data-number="D1">
                                <p class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">D1</p>
                            </div>
                            <div class="tab-container position-relative">
                                <img class="table-D" src="/images/table/meja3.png" width="80" alt=""
                                    srcset="" data-table="not-selected" data-number="D2">
                                <p class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">D2</p>
                            </div>
                            <div class="tab-container position-relative">
                                <img class="table-D" src="/images/table/meja3.png" width="80" alt=""
                                    srcset="" data-table="not-selected" data-number="D3">
                                <p class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">D3</p>
                            </div>
                            <div class="tab-container position-relative">
                                <img class="table-D" src="/images/table/meja3.png" width="80" alt=""
                                    srcset="" data-table="not-selected" data-number="D4">
                                <p class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">D4</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary text-white" data-bs-dismiss="modal">Pilih Meja</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let sidePanel = $('#app-sidepanel');
            sidePanel.removeClass('sidepanel-visible');
            sidePanel.addClass('sidepanel-hidden');
        });
    </script>
@endpush
