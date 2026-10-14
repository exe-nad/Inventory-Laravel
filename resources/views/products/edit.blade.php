@extends('layouts.app')

@section('title', 'Edit Menu: ' . $product->name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">✏️ Perbarui Data Produk</h1>
        <p class="page-subtitle">Ubah informasi menu, harga, kode SKU, dan mitra supplier.</p>
    </div>
    <div>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
            <div>
                <strong>Kesalahan pengisian form:</strong>
                <ul style="margin: 0.5rem 0 0 1.25rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('products.update', $product) }}">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="sku">Kode SKU <span style="color:red">*</span></label>
                <input type="text" id="sku" name="sku" class="form-control" 
                       value="{{ old('sku', $product->sku) }}" required style="font-family: monospace; font-weight: bold;">
                @error('sku')
                    <small style="color: var(--danger); font-size: 0.78rem;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="name">Nama Menu Produk <span style="color:red">*</span></label>
                <input type="text" id="name" name="name" class="form-control" 
                       value="{{ old('name', $product->name) }}" required>
                @error('name')
                    <small style="color: var(--danger); font-size: 0.78rem;">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="category_id">Kategori Menu <span style="color:red">*</span></label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <small style="color: var(--danger); font-size: 0.78rem;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="price">Harga Jual (Rp) <span style="color:red">*</span></label>
                <input type="number" step="0.01" min="0" id="price" name="price" class="form-control" 
                       value="{{ old('price', $product->price) }}" required>
                @error('price')
                    <small style="color: var(--danger); font-size: 0.78rem;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Stok Fisik Saat Ini</label>
                <input type="text" class="form-control" value="{{ number_format($product->stock) }} {{ $product->unit }}" disabled style="background: #EFEBE9; cursor: not-allowed;">
                <small style="color: var(--text-muted); font-size: 0.72rem;">Mutasi stok dilakukan via menu Transaksi Stok.</small>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="unit">Satuan Penjualan</label>
                <input type="text" id="unit" name="unit" class="form-control" 
                       value="{{ old('unit', $product->unit) }}">
            </div>

            <div class="form-group">
                <label class="form-label" for="minimum_stock">Batas Minimum Stok</label>
                <input type="number" min="0" id="minimum_stock" name="minimum_stock" class="form-control" 
                       value="{{ old('minimum_stock', $product->minimum_stock) }}">
            </div>
        </div>

        <!-- Many-to-Many Supplier Checkboxes -->
        <div class="form-group" style="background: var(--primary-light); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
            <label class="form-label" style="margin-bottom: 0.5rem;">
                🚚 Hubungkan Mitra Supplier (Relasi Many-to-Many)
            </label>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.5rem;">
                @foreach ($suppliers as $sup)
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.88rem; cursor: pointer;">
                    <input type="checkbox" name="suppliers[]" value="{{ $sup->id }}"
                           {{ in_array($sup->id, old('suppliers', $selectedSuppliers)) ? 'checked' : '' }}>
                    <span>{{ $sup->name }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="description">Deskripsi Singkat / Catatan Resep</label>
            <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
