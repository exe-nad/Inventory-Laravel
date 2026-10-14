@extends('layouts.app')

@section('title', 'Catat Barang Masuk (IN)')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">📥 Catat Transaksi Barang Masuk (Restock)</h1>
        <p class="page-subtitle">Tambah stok fisik produk dari dapur produksi atau pasokan supplier.</p>
    </div>
    <div>
        <a href="{{ route('stock.index') }}" class="btn btn-secondary">&larr; Kembali ke Histori</a>
    </div>
</div>

<div class="card" style="max-width: 650px; margin: 0 auto; border-top: 5px solid var(--success);">
    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
            <div>
                <strong>Gagal memproses transaksi:</strong>
                <ul style="margin: 0.5rem 0 0 1.25rem;">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('stock.storeIn') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="product_id">Pilih Produk Roti / Kue <span style="color:red">*</span></label>
            <select id="product_id" name="product_id" class="form-control" required>
                <option value="">-- Pilih Produk --</option>
                @foreach ($products as $p)
                    <option value="{{ $p->id }}" {{ old('product_id', $preselectedId) == $p->id ? 'selected' : '' }}>
                        [{{ $p->sku }}] {{ $p->name }} (Sisa Stok: {{ number_format($p->stock) }} {{ $p->unit }})
                    </option>
                @endforeach
            </select>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="quantity">Jumlah Masuk <span style="color:red">*</span></label>
                <input type="number" min="1" id="quantity" name="quantity" class="form-control" 
                       value="{{ old('quantity') }}" required placeholder="Contoh: 20">
                <small style="color: var(--text-muted); font-size: 0.75rem;">Aturan: Jumlah harus bilangan positif (> 0).</small>
            </div>

            <div class="form-group">
                <label class="form-label" for="supplier_id">Pemasok / Sumber Bahan</label>
                <select id="supplier_id" name="supplier_id" class="form-control">
                    <option value="">-- Dapur Internal / Umum --</option>
                    @foreach ($suppliers as $s)
                        <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>
                            🚚 {{ $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="notes">Keterangan / Catatan Batch</label>
            <textarea id="notes" name="notes" class="form-control" rows="3" 
                      placeholder="Contoh: Penerimaan adonan pagi, batch fresh bake #104...">{{ old('notes') }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            <a href="{{ route('stock.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-success">📥 Simpan Barang Masuk</button>
        </div>
    </form>
</div>
@endsection
