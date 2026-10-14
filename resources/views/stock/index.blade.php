@extends('layouts.app')

@section('title', 'Histori Transaksi Stok')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">📦 Histori Transaksi & Log Mutasi Stok</h1>
        <p class="page-subtitle">Pencatatan lengkap audit trail barang masuk dan keluar toko roti.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('stock.in') }}" class="btn btn-success">📥 + Transaksi Masuk (IN)</a>
        <a href="{{ route('stock.out') }}" class="btn btn-danger">📤 - Transaksi Keluar (OUT)</a>
    </div>
</div>

<!-- Filter Bar -->
<form method="GET" action="{{ route('stock.index') }}" class="filter-bar">
    <div style="flex: 2; min-width: 220px;">
        <input type="text" name="search" class="form-control" placeholder="🔍 Cari No. Ref, SKU, produk, catatan..." value="{{ request('search') }}">
    </div>

    <div style="flex: 1; min-width: 150px;">
        <select name="type" class="form-control">
            <option value="">-- Semua Tipe --</option>
            <option value="IN" {{ request('type') === 'IN' ? 'selected' : '' }}>📥 Barang Masuk (IN)</option>
            <option value="OUT" {{ request('type') === 'OUT' ? 'selected' : '' }}>📤 Barang Keluar (OUT)</option>
        </select>
    </div>

    <div style="flex: 1.5; min-width: 200px;">
        <select name="product_id" class="form-control">
            <option value="">-- Semua Produk --</option>
            @foreach ($products as $pr)
                <option value="{{ $pr->id }}" {{ request('product_id') == $pr->id ? 'selected' : '' }}>
                    {{ $pr->sku }} - {{ $pr->name }}
                </option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-secondary">Filter</button>
    @if (request()->hasAny(['search', 'type', 'product_id']))
        <a href="{{ route('stock.index') }}" class="btn btn-sm btn-secondary">Reset</a>
    @endif
</form>

<!-- Table of Transactions -->
<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No. Referensi</th>
                    <th>Waktu Mutasi</th>
                    <th>Tipe</th>
                    <th>Produk / SKU</th>
                    <th>Jumlah</th>
                    <th>Perubahan Stok</th>
                    <th>Pemasok / Asal</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $trx)
                <tr>
                    <td><code>{{ $trx->reference_no }}</code></td>
                    <td>{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y, H:i') }}</td>
                    <td>
                        @if ($trx->type === 'IN')
                            <span class="badge badge-success">📥 MASUK</span>
                        @else
                            <span class="badge badge-danger">📤 KELUAR</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge-sku">{{ $trx->product->sku ?? '-' }}</span>
                        <strong>{{ $trx->product->name ?? '-' }}</strong>
                    </td>
                    <td style="font-weight: 800; color: {{ $trx->type === 'IN' ? 'var(--success)' : 'var(--danger)' }};">
                        {{ $trx->type === 'IN' ? '+' : '-' }}{{ number_format($trx->quantity) }} {{ $trx->product->unit ?? 'pcs' }}
                    </td>
                    <td>
                        {{ number_format($trx->stock_before) }} &rarr; 
                        <strong style="color: var(--text-primary);">{{ number_format($trx->stock_after) }}</strong>
                    </td>
                    <td>
                        {{ $trx->supplier ? '🚚 ' . $trx->supplier->name : '-' }}
                    </td>
                    <td style="max-width: 220px; font-size: 0.82rem; color: var(--text-muted);">
                        {{ $trx->notes ?: '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">📋</div>
                        Tidak ditemukan data transaksi mutasi stok yang sesuai.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if ($movements->hasPages())
<div class="pagination">
    <div>
        Halaman <strong>{{ $movements->currentPage() }}</strong> dari <strong>{{ $movements->lastPage() }}</strong> (Total <strong>{{ $movements->total() }}</strong> catatan)
    </div>
    <div>
        {{ $movements->links() }}
    </div>
</div>
@endif
@endsection
