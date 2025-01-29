@extends('layouts.main')

@section('container')
<div class="container">
    <div class="row">
        <!-- Input Promo Code Form -->
        <div class="col-md-6 mb-4 mt-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Tambah Promo Baru</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('promo.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-2">
                            <label for="promo_code">Kode Promo</label>
                            <input type="text" class="form-control" id="promo_code" name="promo_code" placeholder="Masukkan kode promo" required>
                        </div>

                        <div class="form-group mb-2">
                            <label for="description">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Masukkan deskripsi promo" required></textarea>
                        </div>

                        <div class="form-group mb-2">
                            <label for="promo_details">Tipe dan Nilai Promo</label>
                            <div class="input-group">
                                <select class="form-control" id="type" name="type" required>
                                    <option value="fixed">Fixed (Nominal)</option>
                                    <option value="percentage">Persentase</option>
                                </select>
                                <input type="number" class="form-control" id="value" name="value" placeholder="Masukkan nilai promo" required>
                            </div>
                            <small id="value-help" class="form-text text-muted">Masukkan nominal tetap jika tipe promo adalah Fixed, atau masukkan persentase jika tipe promo adalah Percentage.</small>
                        </div>

                        <div class="form-group mb-2">
                            <label for="start_date">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>

                        <div class="form-group mb-2">
                            <label for="end_date">Tanggal Berakhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>

                        <div class="form-group mb-2">
                            <label for="min_transaction">Minimal Transaksi</label>
                            <input type="number" class="form-control" id="min_transaction" name="min_transaction" placeholder="Masukkan minimal transaksi" required>
                        </div>

                        <div class="form-group mb-2">
                            <label for="usage_limit">Batas Penggunaan</label>
                            <input type="number" class="form-control" id="usage_limit" name="usage_limit" placeholder="Masukkan batas penggunaan" required>
                        </div>

                        <button type="submit" class="btn btn-success mt-3 mb-3">Simpan Promo</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Promo List -->
        <div class="col-md-6 mb-4 mt-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">Promo Aktif</h4>
                </div>
                <div class="card-body">
                    <!-- Menampilkan Daftar Promo Aktif -->
                    @if ($promos->isEmpty())
                        <div class="alert alert-info">
                            Tidak ada promo aktif saat ini.
                        </div>
                    @else
                        <ul id="promo-list" class="list-group mt-1 mb-2">
                            @foreach ($promos as $promo)
                                <li class="list-group-item">
                                    <strong>{{ $promo->promo_code }}</strong> - {{ $promo->description }} <br>
                                    <strong>Tipe:</strong> {{ ucfirst($promo->type) }} <br>
                                    <strong>Nilai:</strong>
                                    @if ($promo->type == 'fixed')
                                        Rp {{ number_format($promo->value, 0, ',', '.') }}
                                    @else
                                        {{ $promo->value }} %
                                    @endif
                                    <br>
                                    <strong>Mulai:</strong> {{ \Carbon\Carbon::parse($promo->start_date)->format('d-m-Y') }} <br>
                                    <strong>Berakhir:</strong> {{ \Carbon\Carbon::parse($promo->end_date)->format('d-m-Y') }} <br>
                                    <strong>Minimal Transaksi:</strong> Rp {{ number_format($promo->min_transaction, 0, ',', '.') }} <br>
                                    <strong>Batas Penggunaan:</strong> {{ $promo->usage_limit }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk mengubah tipe nilai berdasarkan tipe promo -->
<script>
    // Fungsi untuk menyesuaikan input value berdasarkan tipe promo yang dipilih
    document.getElementById('type').addEventListener('change', function() {
        var type = this.value;
        var valueInput = document.getElementById('value');
        var valueHelpText = document.getElementById('value-help');

        if (type === 'fixed') {
            valueInput.setAttribute('placeholder', 'Masukkan nominal tetap (misal: 50000)');
            valueHelpText.innerHTML = 'Masukkan nominal tetap jika tipe promo adalah Fixed (contoh: 50000).';
        } else if (type === 'percentage') {
            valueInput.setAttribute('placeholder', 'Masukkan persentase (misal: 20 untuk 20%)');
            valueHelpText.innerHTML = 'Masukkan persentase jika tipe promo adalah Percentage (contoh: 20 untuk diskon 20%).';
        }
    });

    // Trigger event agar penyesuaian terjadi saat halaman pertama kali dimuat
    document.getElementById('type').dispatchEvent(new Event('change'));
</script>
@endsection