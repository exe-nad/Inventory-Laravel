@extends('layouts.app')

@section('title', 'Catat Barang Keluar (OUT)')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">📤 Catat Transaksi Barang Keluar</h1>
        <p class="page-subtitle">Pencatatan distribusi display etalase, penjualan harian, atau konsumsi toko.</p>
    </div>
    <div>
        <a href="{{ route('stock.index') }}" class="btn btn-secondary">&larr; Kembali ke Histori</a>
    </div>
</div>

<div class="card" style="max-width: 650px; margin: 0 auto; border-top: 5px solid var(--danger);">
    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
            <div>
                <strong>Transaksi ditolak sistem:</strong>
                <ul style="margin: 0.5rem 0 0 1.25rem;">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('stock.storeOut') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="product_id">Pilih Produk Yang Dikeluarkan <span style="color:red">*</span></label>
            <select id="product_id" name="product_id" class="form-control" required onchange="updateMaxQty(this)">
                <option value="">-- Pilih Produk --</option>
                @foreach ($products as $p)
                    <option value="{{ $p->id }}" 
                            data-stock="{{ $p->stock }}"
                            data-unit="{{ $p->unit }}"
                            {{ old('product_id', $preselectedId) == $p->id ? 'selected' : '' }}>
                        [{{ $p->sku }}] {{ $p->name }} (Tersedia: {{ number_format($p->stock) }} {{ $p->unit }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="quantity">Jumlah Keluar <span style="color:red">*</span></label>
            <input type="number" min="1" id="quantity" name="quantity" class="form-control" 
                   value="{{ old('quantity') }}" required placeholder="Contoh: 5">
            <small id="stockHint" style="color: var(--text-muted); font-size: 0.75rem;">
                Aturan Bisnis: Barang keluar tidak boleh melebihi stok yang ada di sistem.
            </small>
        </div>

        <div class="form-group">
            <label class="form-label" for="notes">Tujuan / Alasan Pengeluaran</label>
            <textarea id="notes" name="notes" class="form-control" rows="3" 
                      placeholder="Contoh: Display etalase kafe pagi, pesanan katering kantor...">{{ old('notes') }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            <a href="{{ route('stock.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-danger">📤 Simpan Barang Keluar</button>
        </div>
    </form>
</div>

<script>
function updateMaxQty(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const stock = selectedOption.getAttribute('data-stock');
    const unit = selectedOption.getAttribute('data-unit') || 'unit';
    const qtyInput = document.getElementById('quantity');
    const hint = document.getElementById('stockHint');
    
    if (stock !== null) {
        qtyInput.max = stock;
        hint.innerHTML = 'Maksimal yang bisa dikeluarkan: <strong style="color:var(--danger);">' + stock + ' ' + unit + '</strong>';
    } else {
        hint.innerHTML = 'Aturan Bisnis: Barang keluar tidak boleh melebihi stok yang ada di sistem.';
    }
}
</script>
@endsection
