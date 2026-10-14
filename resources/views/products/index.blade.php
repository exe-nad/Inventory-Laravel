@extends('layouts.app')

@section('title', 'Katalog & Inventaris Produk')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">🍞 Master Data Produk & Etalase</h1>
        <p class="page-subtitle">Kelola produk bakery, SKU unik, kategori, relasi supplier, dan stok inventaris via Laravel Eloquent.</p>
    </div>
    <div>
        <a href="{{ route('products.create') }}" class="btn btn-primary">➕ Tambah Menu Baru</a>
    </div>
</div>

<!-- Filter, Search & Sorting Bar -->
<form method="GET" action="{{ route('products.index') }}" class="filter-bar">
    <div style="flex: 2; min-width: 220px;">
        <input type="text" name="search" class="form-control" placeholder="🔍 Cari nama roti, rasa, atau SKU..." value="{{ request('search') }}">
    </div>

    <div style="flex: 1; min-width: 160px;">
        <select name="category_id" class="form-control">
            <option value="">-- Semua Kategori --</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div style="flex: 1; min-width: 140px;">
        <select name="stock_filter" class="form-control">
            <option value="">-- Status Stok --</option>
            <option value="available" {{ request('stock_filter') === 'available' ? 'selected' : '' }}>Tersedia (&gt; 0)</option>
            <option value="low" {{ request('stock_filter') === 'low' ? 'selected' : '' }}>Menipis (&le; 10)</option>
            <option value="empty" {{ request('stock_filter') === 'empty' ? 'selected' : '' }}>Habis (0)</option>
        </select>
    </div>

    <div style="flex: 1; min-width: 140px;">
        <select name="sort_by" class="form-control">
            <option value="created_at" {{ $sortBy === 'created_at' ? 'selected' : '' }}>Urut: Terbaru</option>
            <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>Urut: Nama Menu</option>
            <option value="sku" {{ $sortBy === 'sku' ? 'selected' : '' }}>Urut: Kode SKU</option>
            <option value="price" {{ $sortBy === 'price' ? 'selected' : '' }}>Urut: Harga</option>
            <option value="stock" {{ $sortBy === 'stock' ? 'selected' : '' }}>Urut: Jumlah Stok</option>
        </select>
    </div>

    <div style="width: 100px;">
        <select name="sort_order" class="form-control">
            <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>A &rarr; Z / Naik</option>
            <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>Z &rarr; A / Turun</option>
        </select>
    </div>

    <button type="submit" class="btn btn-secondary">Filter</button>
    @if (request()->hasAny(['search', 'category_id', 'stock_filter', 'sort_by', 'sort_order']))
        <a href="{{ route('products.index') }}" class="btn btn-sm btn-secondary" title="Reset Filter">Reset</a>
    @endif
</form>

<!-- Table of Products -->
<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga Satuan</th>
                    <th>Stok Fisik</th>
                    <th>Mitra Supplier (M:N)</th>
                    <th>Detail</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $p)
                <tr>
                    <td>
                        <span class="badge-sku">{{ $p->sku }}</span>
                    </td>
                    <td>
                        <strong style="font-size: 0.95rem;">{{ $p->name }}</strong>
                        @if ($p->description)
                            <div style="font-size: 0.78rem; color: var(--text-muted); max-width: 280px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $p->description }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-primary">{{ $p->category->name ?? '-' }}</span>
                    </td>
                    <td style="font-weight: 700; color: var(--text-primary);">
                        Rp {{ number_format($p->price, 0, ',', '.') }}
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal;">/{{ $p->unit }}</span>
                    </td>
                    <td>
                        @if ($p->stock == 0)
                            <span class="badge badge-danger">Habis (0)</span>
                        @elseif ($p->stock <= 10)
                            <span class="badge badge-warning">{{ number_format($p->stock) }} {{ $p->unit }} (Menipis)</span>
                        @else
                            <span class="badge badge-success">{{ number_format($p->stock) }} {{ $p->unit }}</span>
                        @endif
                    </td>
                    <td style="font-size: 0.8rem; max-width: 200px;">
                        @if ($p->suppliers->isNotEmpty())
                            <span>🚚 {{ $p->suppliers->pluck('name')->join(', ') }}</span>
                        @else
                            <span style="color: var(--text-muted); font-style: italic;">Belum terhubung</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('products.show', $p) }}" class="btn btn-secondary btn-sm">👁️ Detail</a>
                    </td>
                    <td style="text-align: right; white-space: nowrap;">
                        <a href="{{ route('products.edit', $p) }}" class="btn btn-secondary btn-sm" title="Edit Produk">✏️ Edit</a>
                        
                        <form action="{{ route('products.destroy', $p) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Produk">🗑️ Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">🥖</div>
                        Tidak ada produk yang cocok dengan kriteria pencarian / filter Anda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination Bar -->
@if ($products->hasPages())
<div class="pagination">
    <div>
        Menampilkan data <strong>{{ $products->firstItem() }}</strong> - <strong>{{ $products->lastItem() }}</strong> dari <strong>{{ $products->total() }}</strong> total produk
    </div>
    <div>
        {{ $products->links() }}
    </div>
</div>
@endif

@endsection
