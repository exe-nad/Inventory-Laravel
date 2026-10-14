@extends('layouts.app')

@section('title', 'Master Data Kategori')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">🏷️ Master Data Kategori Menu</h1>
        <p class="page-subtitle">Pengelompokan varian pastry, artisan cake, dan roti bakery.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; align-items: start;">
    <!-- Form Tambah Kategori -->
    <div class="card">
        <h2 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1rem;">➕ Tambah Kategori Baru</h2>

        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 1rem;">
                <ul style="margin-left: 1.25rem;">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('categories.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="name">Nama Kategori <span style="color:red">*</span></label>
                <input type="text" id="name" name="name" class="form-control" required
                       value="{{ old('name') }}" placeholder="Misal: Viennoiserie">
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Deskripsi</label>
                <textarea id="description" name="description" class="form-control" rows="3"
                          placeholder="Keterangan kategori...">{{ old('description') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 1rem;">
                ➕ Tambah Kategori
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Kategori -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Total Produk</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $cat)
                    <tr>
                        <td><strong style="color: var(--primary);">{{ $cat->name }}</strong></td>
                        <td style="font-size: 0.85rem; color: var(--text-muted); max-width: 250px;">
                            {{ $cat->description ?: '-' }}
                        </td>
                        <td>
                            <span class="badge badge-primary">{{ number_format($cat->products_count) }} menu</span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            @if ($cat->products_count === 0)
                                <form method="POST" action="{{ route('categories.destroy', $cat) }}" style="display: inline;" onsubmit="return confirm('Hapus kategori ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">🗑️ Hapus</button>
                                </form>
                            @else
                                <button class="btn btn-secondary btn-sm" disabled title="Terkunci: Masih memiliki produk terkait" style="opacity: 0.5; cursor: not-allowed;">🔒 Terpakai</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center; padding: 2rem;">Belum ada kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
