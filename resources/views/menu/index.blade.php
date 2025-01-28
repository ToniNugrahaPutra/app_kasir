@extends('layouts.main')

@section('title', 'Menu')

@section('container')
    <h1 class="app-page-title mb-2">Daftar Menu</h1>
    <div class="menu mb-4">
        <ul class="nav nav-tabs d-flex justify-content-center" data-aos="fade-up" data-aos-delay="200">
            @foreach ($categories as $category)
            <li class="nav-item">
                <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#{{ $category->name }}">
                    <h4>{{ $category->name }}</h4>
                </a>
            </li>
            @endforeach
        </ul>
    </div>

    <div class="tab-content" data-aos="fade-up" data-aos-delay="300">
        @foreach ($categories as $category)
        <div class="tab-pane fade {{ $loop->first ? 'active show' : '' }}" id="{{ $category->name }}">
            <div class="row g-4">
                    @foreach ($menus->where('category_id', $category->id) as $menu)
                        <div class="col-sm-6 col-md-3 col-lg-3 mb-4 mb-lg-0">
                            <div class="card rounded shadow-sm app-card-doc border-0 card-menu bg-white">
                            <div class="card-body p-4">
                                <img src="{{ asset('storage/products/' . $menu->image) }}" alt="" class="img-fluid d-block mx-auto mb-3">
                                <div class="d-flex justify-content-between">
                                    <h5 class="col-11 text-banner text-primary text-capitalize">{{ $menu->name }}</h5>
                                    <div class="app-card-actions">
                                        <div class="dropdown">
                                            <div class="dropdown-toggle no-toggle-arrow mx-3" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fa-solid fa-ellipsis-vertical" style="cursor: pointer;"></i>
                                            </div>
                                            <ul class="bg-white dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" id="show-menu"
                                                        onclick="showMenu({{ $menu->id }})">
                                                        <i class="fa-solid fa-eye mx-2"></i> Lihat</a>
                                                </li>
                                                <li><a class="dropdown-item" href="{{ route('menu.edit', $menu->id) }}"><i
                                                            class="fa-solid fa-pen-to-square mx-2"></i> Edit</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li class="delete">
                                                    <form action="{{ route('menu.destroy', $menu->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="dropdown-item border-0 bg-transparent text-danger"
                                                            onclick="return confirm('Yakin ingin menghapus?')"><i
                                                                class="fa-solid fa-trash-can mx-2"></i> Hapus</button>
                                                    </form>
                                                </li>
                                            </ul>

                                        </div>
                                    </div>
                                </div>
                                <p class="small price">IDR
                                    <span class="nominal">{{ number_format($menu->productPrice->where('price_category_id', 1)->first()->price, 0, ',', '.') }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="show" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Menu</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0" id="menu-body">
                        <div class="container-fluid">
                            <div class="row d-flex">
                                <div class="col-md">
                                    <div class="row">
                                        <div class="col-md-6 p-4 d-flex align-items-center">
                                            <div class="images">
                                                <div class="text-center">
                                                    <img id="menu-image" src="" width="200" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md p-4 rounded-end" style="background-color: #eee;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <span id="menu-uploaded" class="ml-1 small"></span>
                                                </div>
                                            </div>
                                            <div class="mt-4 mb-4">
                                                <span id="menu-category" class="text-uppercase text-muted brand"></span>
                                                <h5 id="menu-name" class="text-uppercase"></h5>
                                                <div class="d-flex flex-row align-items-center">
                                                    <span id="menu-price" class="text-primary fw-500 small"></span>
                                                </div>
                                            </div>
                                            <p id="menu-description" class="about mt-2"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function showMenu(id) {
        $.ajax({
            url: "/menu/" + id,
            success: (data) => {
                $('#menu-image').attr('src', 'storage/products/' + data.image);
                $('#menu-uploaded').html('Dibuat : ' + data.tanggalDibuat);
                $('#menu-category').html(data.category.name);
                $('#menu-name').html(data.name);
                $('#menu-price').html('IDR ' + data.price);
                $('#menu-description').html(data.description);

                $('#show').modal('show');
            }
        });
    }
</script>
@endpush
