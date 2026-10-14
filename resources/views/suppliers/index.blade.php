@extends('layouts.app')

@section('title', 'Master Data Mitra Supplier')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">🚚 Master Data Mitra Supplier</h1>
        <p class="page-subtitle">Kelola mitra distributor bahan baku dan hubungan Many-to-Many dengan produk etalase.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; align-items: start;">
    <!-- Form Tambah Supplier -->
    <div class="card">
        <h2 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1rem;">➕ Tambah Mitra Supplier</h2>

        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 1rem;">
                <ul style="margin-left: 1.25rem;">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('suppliers.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="name">Nama Perusahaan / Supplier <span style="color:red">*</span></label>
                <input type="text" id="name" name="name" class="form-control" required
                       value="{{ old('name') }}" placeholder="Misal: PT Cacao Indonesia">
            </div>

            <div class="form-group">
                <label class="form-label" for="contact_person">Contact Person (PIC)</label>
                <input type="text" id="contact_person" name="contact_person" class="form-control"
                       value="{{ old('contact_person') }}" placeholder="Nama penanggung jawab">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label class="form-label" for="phone">No. Telepon</label>
                    <input type="text" id="phone" name="phone" class="form-control"
                           value="{{ old('phone') }}" placeholder="0812...">
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                           value="{{ old('email') }}" placeholder="sales@...">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="address">Alamat Gudang / Kantor</label>
                <textarea id="address" name="address" class="form-control" rows="2"
                          placeholder="Alamat distributor...">{{ old('address') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 1rem;">
                ➕ Tambah Supplier
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Supplier -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Supplier</th>
                        <th>Kontak PIC</th>
                        <th>Bahan / Produk Terpasok (M:N)</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $s)
                    <tr>
                        <td>
                            <strong>{{ $s->name }}</strong>
                            @if ($s->address)
                                <div style="font-size: 0.78rem; color: var(--text-muted);">{{ $s->address }}</div>
                            @endif
                        </td>
                        <td style="font-size: 0.85rem;">
                            <div>👤 {{ $s->contact_person ?: '-' }}</div>
                            <div style="color: var(--text-muted);">📞 {{ $s->phone ?: '-' }}</div>
                        </td>
                        <td>
                            <span class="badge badge-info" style="margin-bottom: 4px;">
                                {{ number_format($s->products_count) }} produk
                            </span>
                            @if ($s->products->isNotEmpty())
                                <div style="font-size: 0.78rem; color: var(--text-muted); max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $s->products->pluck('name')->join(', ') }}
                                </div>
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <form method="POST" action="{{ route('suppliers.destroy', $s) }}" style="display: inline;" onsubmit="return confirm('Hapus supplier ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">🗑️ Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center; padding: 2rem;">Belum ada supplier terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
