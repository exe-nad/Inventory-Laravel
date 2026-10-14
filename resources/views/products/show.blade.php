@extends('layouts.app')

@section('title', 'Detail Produk: ' . $product->name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">🔍 Detail Menu: {{ $product->name }}</h1>
        <p class="page-subtitle">Informasi lengkap spesifikasi produk, relasi supplier, dan riwayat mutasi stok.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary">✏️ Edit</a>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; align-items: start;">
    <!-- Kartu Info Produk -->
    <div class="card">
        <h2 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1rem;">📋 Informasi Produk</h2>
        
        <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.9rem;">
            <div>
                <span style="color: var(--text-muted); display: block; font-size: 0.75rem; text-transform: uppercase;">Kode SKU</span>
                <span class="badge-sku" style="font-size: 1rem;">{{ $product->sku }}</span>
            </div>
            <div>
                <span style="color: var(--text-muted); display: block; font-size: 0.75rem; text-transform: uppercase;">Kategori</span>
                <span class="badge badge-primary">{{ $product->category->name }}</span>
            </div>
            <div>
                <span style="color: var(--text-muted); display: block; font-size: 0.75rem; text-transform: uppercase;">Harga Jual</span>
                <strong style="font-size: 1.15rem; color: var(--primary);">Rp {{ number_format($product->price, 0, ',', '.') }}</strong> /{{ $product->unit }}
            </div>
            <div>
                <span style="color: var(--text-muted); display: block; font-size: 0.75rem; text-transform: uppercase;">Stok Saat Ini</span>
                @if ($product->stock <= 10)
                    <span class="badge badge-danger" style="font-size: 0.95rem;">{{ $product->stock }} {{ $product->unit }} (Menipis)</span>
                @else
                    <span class="badge badge-success" style="font-size: 0.95rem;">{{ $product->stock }} {{ $product->unit }}</span>
                @endif
            </div>
            <div>
                <span style="color: var(--text-muted); display: block; font-size: 0.75rem; text-transform: uppercase;">Deskripsi / Resep</span>
                <p style="margin-top: 0.2rem; line-height: 1.5;">{{ $product->description ?: 'Tidak ada deskripsi.' }}</p>
            </div>
        </div>

        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
            <h3 style="font-size: 0.95rem; font-weight: 800; margin-bottom: 0.5rem;">🚚 Mitra Supplier</h3>
            @if ($product->suppliers->isNotEmpty())
                <ul style="padding-left: 1.25rem; font-size: 0.85rem;">
                    @foreach ($product->suppliers as $sup)
                        <li><strong>{{ $sup->name }}</strong> (Lead Time: {{ $sup->pivot->lead_time_days }} hari)</li>
                    @endforeach
                </ul>
            @else
                <p style="font-size: 0.82rem; color: var(--text-muted);">Belum ada supplier yang terhubung.</p>
            @endif
        </div>
    </div>

    <!-- Riwayat Mutasi Produk -->
    <div class="card">
        <h2 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1rem;">📜 Riwayat Mutasi Stok Produk Ini</h2>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ref No.</th>
                        <th>Waktu</th>
                        <th>Tipe</th>
                        <th>Qty</th>
                        <th>Perubahan</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($product->stockMovements as $sm)
                    <tr>
                        <td><code>{{ $sm->reference_no }}</code></td>
                        <td>{{ \Carbon\Carbon::parse($sm->transaction_date)->format('d/m/Y H:i') }}</td>
                        <td>
                            @if ($sm->type === 'IN')
                                <span class="badge badge-success">IN</span>
                            @else
                                <span class="badge badge-danger">OUT</span>
                            @endif
                        </td>
                        <td style="font-weight: 700; color: {{ $sm->type === 'IN' ? 'var(--success)' : 'var(--danger)' }};">
                            {{ $sm->type === 'IN' ? '+' : '-' }}{{ $sm->quantity }}
                        </td>
                        <td>{{ $sm->stock_before }} &rarr; <strong>{{ $sm->stock_after }}</strong></td>
                        <td style="font-size: 0.8rem; color: var(--text-muted);">{{ $sm->notes ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">
                            Belum ada riwayat mutasi untuk produk ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
