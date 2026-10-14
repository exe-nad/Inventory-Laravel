<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SweetBakes Studio') — SweetBakes Inventory</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <nav class="navbar">
        <a href="{{ route('dashboard') }}" class="brand">
            <span style="font-size: 1.5rem;">🥐</span>
            <span>SweetBakes</span>
            <span class="badge-logo">Laravel MVC</span>
        </a>

        <ul class="nav-menu">
            <li><a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">📊 Dashboard</a></li>
            <li><a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">🍞 Produk</a></li>
            <li><a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">🏷️ Kategori</a></li>
            <li><a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">🚚 Supplier</a></li>
            <li><a href="{{ route('stock.index') }}" class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">📦 Transaksi Stok</a></li>
        </ul>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div class="user-badge">
                <span>👤 Nadya Salsabila Zahra</span>
                <span class="role-tag admin">admin</span>
            </div>
            <button type="button" class="theme-btn" id="themeToggle" title="Ganti Mode Tampilan">🌙</button>
        </div>
    </nav>

    <main class="container">
        @if (session('success'))
            <x-alert type="success">
                {{ session('success') }}
            </x-alert>
        @endif

        @if (session('error'))
            <x-alert type="danger">
                {{ session('error') }}
            </x-alert>
        @endif

        @if (session('warning'))
            <x-alert type="warning">
                {{ session('warning') }}
            </x-alert>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        <p>&copy; {{ date('Y') }} <strong>SweetBakes: Artisan Pastry & Cake Studio Manager</strong> (Laravel 9 MVC Application)</p>
        <p style="margin-top: 4px; font-size: 0.8rem;">
            Nadya Salsabila Zahra &bull; NIM: 250180031 &bull; Pemrograman Web (Pertemuan 5 — Laravel Fundamental)
        </p>
    </footer>

    <script>
        const themeToggle = document.getElementById('themeToggle');
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            if (themeToggle) themeToggle.textContent = '☀️';
        }

        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                if (isDark) {
                    document.documentElement.removeAttribute('data-theme');
                    localStorage.setItem('theme', 'light');
                    themeToggle.textContent = '🌙';
                } else {
                    document.documentElement.setAttribute('data-theme', 'dark');
                    localStorage.setItem('theme', 'dark');
                    themeToggle.textContent = '☀️';
                }
            });
        }
    </script>
</body>
</html>
