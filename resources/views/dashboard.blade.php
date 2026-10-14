@extends('layouts.app')

@section('title', 'Dashboard Inventaris')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">📊 Dashboard Inventaris SweetBakes</h1>
        <p class="page-subtitle">Ringkasan status stok, perputaran produk, dan valuasi inventaris bakery berbasis Laravel MVC.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('stock.in') }}" class="btn btn-success">📥 + Barang Masuk</a>
        <a href="{{ route('stock.out') }}" class="btn btn-danger">📤 - Barang Keluar</a>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #EFEBE9; color: var(--primary);">🍞</div>
        <div>
            <div class="stat-label">Total Varian Menu</div>
            <div class="stat-value">{{ number_format($totalProducts) }} <span style="font-size: 0.9rem; font-weight: normal; color: var(--text-muted);">item</span></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: var(--info-bg); color: var(--info);">📦</div>
        <div>
            <div class="stat-label">Total Stok Fisik</div>
            <div class="stat-value">{{ number_format($totalStock) }} <span style="font-size: 0.9rem; font-weight: normal; color: var(--text-muted);">unit</span></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: var(--success-bg); color: var(--success);">💰</div>
        <div>
            <div class="stat-label">Valuasi Inventaris</div>
            <div class="stat-value" style="font-size: 1.25rem;">Rp {{ number_format($totalValuation, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: {{ $lowStockCount > 0 ? 'var(--danger-bg)' : 'var(--success-bg)' }}; color: {{ $lowStockCount > 0 ? 'var(--danger)' : 'var(--success)' }};">
            {{ $lowStockCount > 0 ? '⚠️' : '✅' }}
        </div>
        <div>
            <div class="stat-label">Stok Kritis (&le; 10)</div>
            <div class="stat-value" style="color: {{ $lowStockCount > 0 ? 'var(--danger)' : 'var(--success)' }};">
                {{ number_format($lowStockCount) }} <span style="font-size: 0.9rem; font-weight: normal; color: var(--text-muted);">varian</span>
            </div>
        </div>
    </div>
</div>

<!-- Transaksi Masuk/Keluar Ringkas -->
<div class="card" style="padding: 1.25rem; margin-bottom: 1.75rem; background: linear-gradient(135deg, rgba(199, 107, 50, 0.05), rgba(212, 175, 55, 0.05));">
    <div style="display: flex; justify-content: space-around; flex-wrap: wrap; text-align: center; gap: 1rem;">
        <div>
            <div style="font-size: 0.82rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Total Transaksi Masuk (IN)</div>
            <div style="font-size: 1.35rem; font-weight: 800; color: var(--success); margin-top: 4px;">
                +{{ number_format($sumQtyIn) }} unit <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">({{ $totalTrxIn }} trx)</span>
            </div>
        </div>
        <div style="border-right: 1px solid var(--border-color);"></div>
        <div>
            <div style="font-size: 0.82rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Total Transaksi Keluar (OUT)</div>
            <div style="font-size: 1.35rem; font-weight: 800; color: var(--danger); margin-top: 4px;">
                -{{ number_format($sumQtyOut) }} unit <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">({{ $totalTrxOut }} trx)</span>
            </div>
        </div>
        <div style="border-right: 1px solid var(--border-color);"></div>
        <div>
            <div style="font-size: 0.82rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Net Flow Barang</div>
            <div style="font-size: 1.35rem; font-weight: 800; color: {{ ($sumQtyIn - $sumQtyOut >= 0) ? 'var(--info)' : 'var(--warning)' }}; margin-top: 4px;">
                {{ number_format($sumQtyIn - $sumQtyOut) }} unit
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
    <!-- Laporan Stok per Kategori -->
    <div class="card" style="margin-bottom: 0;">
        <h2 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1rem;">🏷️ Laporan Stok per Kategori</h2>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Varian</th>
                        <th>Total Stok</th>
                        <th>Valuasi (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categoryStats as $row)
                        <tr>
                            <td><strong>{{ $row->name }}</strong></td>
                            <td>{{ number_format($row->products_count) }}</td>
                            <td>{{ number_format($row->total_stock) }} pcs</td>
                            <td>Rp {{ number_format($row->total_value, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">Belum ada data kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alert Stok Kritis -->
    <div class="card" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="font-size: 1.15rem; font-weight: 800;">⚠️ Peringatan Stok Menipis (&le; 10)</h2>
            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Menu Produk</th>
                        <th>Sisa Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lowStockProducts as $p)
                        <tr>
                            <td><span class="badge-sku">{{ $p->sku }}</span></td>
                            <td>{{ $p->name }}</td>
                            <td>
                                <span class="badge badge-danger">{{ number_format($p->stock) }} {{ $p->unit }}</span>
                            </td>
                            <td>
                                <a href="{{ route('stock.in', ['product_id' => $p->id]) }}" class="btn btn-success btn-sm">+ Tambah</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align: center; color: var(--success); font-weight: 600;">✅ Seluruh stok produk dalam batas aman (> 10).</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 5 Transaksi Terbaru -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 800;">📜 Histori Transaksi Inventaris Terbaru</h2>
            <p style="font-size: 0.85rem; color: var(--text-muted);">Aktivitas penerimaan & pengeluaran etalase terkini</p>
        </div>
        <a href="{{ route('stock.index') }}" class="btn btn-secondary btn-sm">Buka Histori Lengkap &rarr;</a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Ref Transaksi</th>
                    <th>Waktu</th>
                    <th>Tipe</th>
                    <th>SKU & Produk</th>
                    <th>Jumlah</th>
                    <th>Sebelum &rarr; Sesudah</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentTransactions as $tx)
                    <tr>
                        <td><code>{{ $tx->reference_no }}</code></td>
                        <td>{{ \Carbon\Carbon::parse($tx->transaction_date)->format('d M Y, H:i') }}</td>
                        <td>
                            @if ($tx->type === 'IN')
                                <span class="badge badge-success">📥 MASUK</span>
                            @else
                                <span class="badge badge-danger">📤 KELUAR</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-sku">{{ $tx->product->sku ?? '-' }}</span>
                            <strong>{{ $tx->product->name ?? '-' }}</strong>
                        </td>
                        <td style="font-weight: 700; color: {{ $tx->type === 'IN' ? 'var(--success)' : 'var(--danger)' }};">
                            {{ $tx->type === 'IN' ? '+' : '-' }}{{ number_format($tx->quantity) }}
                        </td>
                        <td>
                            {{ number_format($tx->stock_before) }} &rarr; <strong>{{ number_format($tx->stock_after) }}</strong>
                        </td>
                        <td style="font-size: 0.82rem; color: var(--text-muted);">{{ $tx->notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align: center; color: var(--text-muted);">Belum ada riwayat transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
