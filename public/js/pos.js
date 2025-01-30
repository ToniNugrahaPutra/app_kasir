// Deklarasikan variabel global di awal script
let list_Product = [];
let total_price = 0;
let fixed_price = 0;
let validMember = false;
const menu_order = document.querySelector('.menu-order');
const subtotal = document.querySelector('.cart-payment .sub-total');
const total_transaction = document.querySelector('.cart-payment .total-transaction');
const input_transaction = document.querySelector('input[name="total_transaction"]');

function showProductDetail(id) {
    $.ajax({
        url: "/menu/detail/" + id,
        success: (data) => {
            $('#product-id').val(data.id);
            $('#product-image').attr('src', '/storage/products/' + data.image);
            $('#product-name').html(data.name);

            // Simpan data harga dalam variabel global
            window.productPrices = data.product_price;

            // Set nilai awal
            $('#product-quantity').val(1);
                updatePrice();

            // Event listener untuk perubahan quantity
            $('#product-quantity').on('input', function() {
                updatePrice();
            });

            $('#productDetail').modal('show');
        }
    });
}

function updatePrice() {
    let memberCode = $('#member_code').val();
    let quantity = parseInt($('#product-quantity').val()) || 1;
    let prices = window.productPrices;
    // Filter harga berdasarkan member/non-member
    let availablePrices = prices.filter(price => {
        if (memberCode && validMember) {
            // Jika ada kode member, cek harga member (3,4) atau alihkan ke harga umum (1,2)
            let memberPrices = prices.filter(p => p.price_category_id === 3 || p.price_category_id === 4);
            return memberPrices.length > 0 ?
                (price.price_category_id === 3 || price.price_category_id === 4) :
                (price.price_category_id === 1 || price.price_category_id === 2);
        } else {
            // Jika tidak ada kode member, ambil harga umum (1) dan grosir umum (2)
            return price.price_category_id === 1 || price.price_category_id === 2;
        }
    });

    // Cari harga yang sesuai dengan quantity
    let selectedPrice = availablePrices
        .filter(price => quantity >= price.min_quantity)
        .sort((a, b) => b.min_quantity - a.min_quantity)[0];

    // Jika tidak ada harga yang sesuai, gunakan harga default (retail)
    if (!selectedPrice) {
        selectedPrice = availablePrices.find(price =>
            price.price_category_id === (memberCode ? 3 : 1)
        );
    }

    // Update tampilan harga
    $('#product-price').html('Rp ' + selectedPrice.price.toLocaleString('id-ID'));

    // Simpan data harga yang dipilih untuk digunakan saat menambah ke keranjang
    $('#selected_price_id').val(selectedPrice.id);
}

function incrementQuantity() {
    let input = $('#product-quantity');
    input.val(parseInt(input.val()) + 1);
    input.trigger('input');
}

function decrementQuantity() {
    let input = $('#product-quantity');
    let currentVal = parseInt(input.val());
    if (currentVal > 1) {
        input.val(currentVal - 1);
        input.trigger('input');
    }
}

function searchMember() {
    let memberCode = $('#member_code').val();

    // Validasi input tidak boleh kosong
    if (!memberCode) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Kode member tidak boleh kosong!'
        });
        return;
    }

    // Kirim request AJAX ke server
    $.ajax({
        url: '/member/search',
        type: 'POST',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            member_code: memberCode,
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Member ditemukan!'
                });

                validMember = true;

                // Update harga jika ada produk yang dipilih
                if (window.productPrices) {
                    updatePrice();
                    // Update harga produk di keranjang
                    list_Product.forEach(product => {
                    let priceInfo = updateProductPrice(product, product.qty, memberCode);
                    if (priceInfo) {
                        // Update total price
                        total_price = total_price - product.price + priceInfo.price;

                        // Update data produk
                        product.price = priceInfo.price;
                        product.price_category_id = priceInfo.priceCategory;

                        // Update tampilan harga satuan
                        let container = document.querySelector(`.cart-product[data-cart-id="${product.id}"]`);
                        if (container) {
                            container.querySelector('.product-order h6').innerText =
                                `Rp ${priceInfo.unitPrice.toLocaleString('id-ID')}`;
                        }
                    }
                });

                // Update tampilan total dan input hidden
                updateTotalDisplay();
                $('#listProduct').val(JSON.stringify(list_Product));
                }

                // disable input member code
                $('#member_code').prop('disabled', true);
                $('#member_code').val(response.member.name + ' (' + response.member.member_code + ')');

                // disable button search member
                $('#search_member').prop('disabled', true);
                //change button search member text
                $('#search_member').html('<i class="fa-solid fa-check"></i>');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Member tidak ditemukan!'
                });
                $('#member_code').val('');
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat mencari member!'
            });
        }
    });
}

function updateListProduct(memberCode) {
    let listProduct = JSON.parse($('#listProduct').val() || '[]');
    let total_price_new = 0;
    let ppn = parseInt($('#ppn').val().replace('%', ''));

    listProduct.forEach(product => {
        // Gunakan product_prices yang tersimpan untuk update harga
        let availablePrices = product.product_prices.filter(price => {
            if (memberCode && validMember) {
                let memberPrices = product.product_prices.filter(p => p.price_category_id === 3 || p.price_category_id === 4);
                return memberPrices.length > 0 ?
                    (price.price_category_id === 3 || price.price_category_id === 4) :
                    (price.price_category_id === 1 || price.price_category_id === 2);
            } else {
                return price.price_category_id === 1 || price.price_category_id === 2;
            }
        });

        let selectedPrice = availablePrices
            .filter(price => product.qty >= price.min_quantity)
            .sort((a, b) => b.min_quantity - a.min_quantity)[0];

        if (!selectedPrice) {
            selectedPrice = availablePrices.find(price =>
                price.price_category_id === (memberCode ? 3 : 1)
            );
        }

        if (selectedPrice) {
            product.price = selectedPrice.price * product.qty;
            product.price_category_id = selectedPrice.price_category_id;

            let cartItem = document.querySelector(`[data-cart-id='${product.id}']`);
            if (cartItem) {
                let priceElement = cartItem.querySelector('.product-order h6');
                priceElement.innerText = `Rp ${selectedPrice.price.toLocaleString('id-ID')}`;
            }

            total_price_new += product.price;
        }
    });

    total_price = total_price_new;
    fixed_price = total_price + (total_price * ppn / 100);

    subtotal.innerText = `Rp ${formatRupiah(total_price.toString())}`;
    total_transaction.innerText = `Rp ${formatRupiah(fixed_price.toString())}`;
    input_transaction.value = fixed_price;

    $('#listProduct').val(JSON.stringify(listProduct));
}

function addToCart() {
    let productId = $('#product-id').val();
    let quantity = parseInt($('#product-quantity').val());
    let selectedPriceId = $('#selected_price_id').val();
    let productName = $('#product-name').html();
    let productPrices = window.productPrices;
    let productPrice = productPrices.find(p => p.id == selectedPriceId);
    let ppn = parseInt($('#ppn').val().replace('%', ''));

    // Validasi quantity
    if (quantity < productPrice.min_quantity) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: `Minimum pembelian untuk harga ini adalah ${productPrice.min_quantity}`
        });
        return;
    }

    // Cek apakah produk sudah ada di keranjang
    let existingProduct = list_Product.find(p => p.id == productId);
    let totalPrice = productPrice.price * quantity;

    if (existingProduct) {
        // Update quantity jika produk sudah ada
        existingProduct.qty += quantity;
        existingProduct.price += totalPrice;
        existingProduct.price_category_id = productPrice.price_category_id;
        existingProduct.product_prices = productPrices; // Simpan semua harga produk

        // Update tampilan di keranjang
        let cartItem = document.querySelector(`[data-cart-id='${productId}']`);
        if (cartItem) {
            cartItem.innerHTML = `
                <div class="product-order">
                    <h5 class="text-white ms-4 mt-3" style="font-size: 18px;">${productName}</h5>
                    <h6 class="text-white ms-4 mb-3" style="font-size: 12px;">Rp ${productPrice.price.toLocaleString('id-ID')}</h6>
                </div>
                <div class="qty d-flex justify-content-between w-25 ms-4 mb-3">
                    <i onclick="remove(this)" class="minusQty btn-remove-inner fa-solid fa-minus" style="color: #fff;"></i>
                    <div class="qty-numbers text-white me-2 ms-2">
                        ${existingProduct.qty}
                    </div>
                    <i onclick="add(this)" class="plusQty btn-add-inner fa-solid fa-plus" style="color: #fff;"></i>
                </div>`;
        }
    } else {
        // Tambah produk baru ke keranjang
        list_Product.push({
            'id': productId,
            'qty': quantity,
            'price': totalPrice,
            'price_category_id': productPrice.price_category_id,
            'name': productName,
            'product_prices': productPrices // Simpan semua harga produk
        });

        // Tambah tampilan ke keranjang
        menu_order.innerHTML += `
            <div class="cart-product d-flex flex-column rounded mb-4" data-cart-id="${productId}">
                <div class="product-order">
                    <h5 class="text-white ms-4 mt-3" style="font-size: 18px;">${productName}</h5>
                    <h6 class="text-white ms-4 mb-3" style="font-size: 12px;">Rp ${productPrice.price.toLocaleString('id-ID')}</h6>
                </div>
                <div class="qty d-flex justify-content-between w-25 ms-4 mb-3">
                    <i onclick="remove(this)" class="minusQty btn-remove-inner fa-solid fa-minus" style="color: #fff;"></i>
                    <div class="qty-numbers text-white me-2 ms-2">
                        ${quantity}
                    </div>
                    <i onclick="add(this)" class="plusQty btn-add-inner fa-solid fa-plus" style="color: #fff;"></i>
                </div>
            </div>`;
    }

    // Update total harga
    total_price += totalPrice;
    fixed_price = total_price + (total_price * ppn / 100);

    // Update tampilan total
    subtotal.innerText = `Rp ${formatRupiah(total_price.toString())}`;
    total_transaction.innerText = `Rp ${formatRupiah(fixed_price.toString())}`;
    input_transaction.value = fixed_price;
    $('#listProduct').val(JSON.stringify(list_Product));

    // Tutup modal
    $('#productDetail').modal('hide');

    // Tampilkan notifikasi sukses
    let Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 1500
    });
    Toast.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Produk berhasil ditambahkan ke keranjang!'
    });
}

function updateItemPrice(product, quantity, memberCode) {
    let prices = window.productPrices.filter(p => p.id == product.id);
    // Filter harga berdasarkan member/non-member
    let availablePrices = prices.filter(price => {
        if (memberCode && validMember) {
            // Jika ada kode member, cek harga member (3,4) atau alihkan ke harga umum (1,2)
            let memberPrices = prices.filter(p => p.price_category_id === 3 || p.price_category_id === 4);
            return memberPrices.length > 0 ?
                (price.price_category_id === 3 || price.price_category_id === 4) :
                (price.price_category_id === 1 || price.price_category_id === 2);
        } else {
            // Jika tidak ada kode member, ambil harga umum (1) dan grosir umum (2)
            return price.price_category_id === 1 || price.price_category_id === 2;
        }
    });

    // Cari harga yang sesuai dengan quantity
    let selectedPrice = availablePrices
        .filter(price => quantity >= price.min_quantity)
        .sort((a, b) => b.min_quantity - a.min_quantity)[0];

    // Jika tidak ada harga yang sesuai, gunakan harga default (retail)
    if (!selectedPrice) {
        selectedPrice = availablePrices.find(price =>
            price.price_category_id === (memberCode ? 3 : 1)
        );
    }

    return selectedPrice;
}

// Fungsi helper untuk format rupiah
function formatRupiah(angka) {
    var number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return rupiah;
}

// Fungsi helper untuk menghitung harga berdasarkan member dan quantity
function calculatePrice(prices, quantity, memberCode) {
    let availablePrices = prices.filter(price => {
        if (memberCode && validMember) {
            let memberPrices = prices.filter(p => p.price_category_id === 3 || p.price_category_id === 4);
            return memberPrices.length > 0 ?
                (price.price_category_id === 3 || price.price_category_id === 4) :
                (price.price_category_id === 1 || price.price_category_id === 2);
        }
        return price.price_category_id === 1 || price.price_category_id === 2;
    });

    let selectedPrice = availablePrices
        .filter(price => quantity >= price.min_quantity)
        .sort((a, b) => b.min_quantity - a.min_quantity)[0] ||
        availablePrices.find(price => price.price_category_id === (memberCode ? 3 : 1));

    return selectedPrice;
}

// Fungsi helper untuk update tampilan total
function updateTotalDisplay() {
    let ppn = parseInt($('#ppn').val().replace('%', ''));
    fixed_price = total_price + (total_price * ppn / 100);

    subtotal.innerText = `Rp ${formatRupiah(total_price.toString())}`;
    total_transaction.innerText = `Rp ${formatRupiah(fixed_price.toString())}`;
    input_transaction.value = fixed_price || '';
}

// Fungsi untuk update harga produk
function updateProductPrice(product, quantity, memberCode) {
    let selectedPrice = calculatePrice(product.product_prices, quantity, memberCode);
    if (!selectedPrice) return null;

    return {
        price: selectedPrice.price * quantity,
        unitPrice: selectedPrice.price,
        priceCategory: selectedPrice.price_category_id
    };
}

// Fungsi untuk menambah quantity pada menu order
function add(element) {
    let container = element.closest('.cart-product');
    let qtyElement = container.querySelector('.qty-numbers');
    let currentQty = parseInt(qtyElement.innerText) + 1;
    let productId = container.getAttribute('data-cart-id');
    let memberCode = $('#member_code').val();

    let product = list_Product.find(p => p.id == productId);
    if (!product) return;

    let priceInfo = updateProductPrice(product, currentQty, memberCode);
    if (!priceInfo) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Harga produk tidak ditemukan!'
        });
        return;
    }

    // Update tampilan dan data
    qtyElement.innerText = currentQty;
    container.querySelector('.product-order h6').innerText = `Rp ${priceInfo.unitPrice.toLocaleString('id-ID')}`;

    total_price = total_price - product.price + priceInfo.price;
    product.qty = currentQty;
    product.price = priceInfo.price;
    product.price_category_id = priceInfo.priceCategory;

    updateTotalDisplay();
    $('#listProduct').val(JSON.stringify(list_Product));
}

// Fungsi untuk mengurangi quantity pada menu order
function remove(element) {
    let container = element.closest('.cart-product');
    let qtyElement = container.querySelector('.qty-numbers');
    let currentQty = parseInt(qtyElement.innerText);

    if (currentQty <= 1) {
        Swal.fire({
            title: 'Hapus produk?',
            text: "Produk akan dihapus dari keranjang",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let productId = container.getAttribute('data-cart-id');
                total_price -= list_Product.find(p => p.id == productId).price;
                list_Product = list_Product.filter(p => p.id != productId);

                container.remove();
                updateTotalDisplay();
                $('#listProduct').val(JSON.stringify(list_Product));
            }
        });
        return;
    }

    let productId = container.getAttribute('data-cart-id');
    let memberCode = $('#member_code').val();
    let product = list_Product.find(p => p.id == productId);

    if (!product) return;

    currentQty--;
    let priceInfo = updateProductPrice(product, currentQty, memberCode);
    if (!priceInfo) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Harga produk tidak ditemukan!'
        });
        return;
    }

    // Update tampilan dan data
    qtyElement.innerText = currentQty;
    container.querySelector('.product-order h6').innerText = `Rp ${priceInfo.unitPrice.toLocaleString('id-ID')}`;

    total_price = total_price - product.price + priceInfo.price;
    product.qty = currentQty;
    product.price = priceInfo.price;
    product.price_category_id = priceInfo.priceCategory;

    updateTotalDisplay();
    $('#listProduct').val(JSON.stringify(list_Product));
}

function deleteOrder() {
    Swal.fire({
        title: 'Hapus Pesanan?',
        text: "Semua pesanan akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Reset semua variabel dan tampilan
            list_Product = [];
            total_price = 0;
            menu_order.innerHTML = '';

            // Reset tampilan total
            updateTotalDisplay();

            // Reset input hidden untuk list produk
            $('#listProduct').val('[]');

            // Reset input member dan table
            $('#member_code').val('');
            $('#table_selected').text('-');
            $('input[name="no_table"]').val('');
            $('#customer_id').val('');

            // Aktifkan input member code
            $('#member_code').prop('disabled', false);
            $('#search_member').prop('disabled', false);
            $('#search_member').html('Cari');

            Swal.fire(
                'Terhapus!',
                'Pesanan berhasil dihapus.',
                'success'
            );
        }
    });
}

// Event listener untuk perubahan ppn
$('#ppn').on('change', function() {
    recalculatePrice();
});

function recalculatePrice() {
    let ppn = parseInt($('#ppn').val().replace('%', ''));

    // Hitung ulang fixed price berdasarkan total_price dan ppn baru
    fixed_price = total_price + (total_price * ppn / 100);

    // Update tampilan total
    total_transaction.innerText = `Rp ${formatRupiah(fixed_price.toString())}`;
    input_transaction.value = fixed_price;
}

function applyDiscount() {
    $.ajax({
        url: '/promo/apply',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            discount_code: $('#discount_code').val(),
            total: total_price
        },
        beforeSend: function() {
            $('#apply_discount').prop('disabled', true);
            $('#apply_discount').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Diskon berhasil diterapkan!'
                });
                console.log(response);
                // logika untuk menampilkan diskon
                $('#discount_amount').html(`- Rp ${response.discountAmount.toLocaleString('id-ID')}`);
                $('#sub-total').html(`Rp ${response.totalPrice.toLocaleString('id-ID')}`);
                total_price = response.totalPrice;
                fixed_price = total_price + (total_price * ppn / 100);
                $('#apply_discount').prop('disabled', true);
                $('#apply_discount').html('<i class="fa-solid fa-check"></i>');
                $('#discount_code').prop('disabled', true);

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Kode diskon tidak valid!'
                });

                $('#apply_discount').prop('disabled', false);
                $('#apply_discount').html('Terapkan');
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat menerapkan diskon!'
            });

            $('#apply_discount').prop('disabled', false);
            $('#apply_discount').html('Terapkan');
        }
    });
}




