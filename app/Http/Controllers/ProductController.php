<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * Fitur: Search, Filter Kategori/Stok, Sorting Whitelist, Pagination, Eager Loading with('category')
     */
    public function index(Request $request)
    {
        $allowedSorts = ['sku', 'name', 'price', 'stock', 'created_at'];
        $sortBy = in_array($request->get('sort_by'), $allowedSorts) ? $request->get('sort_by') : 'created_at';
        $sortOrder = strtolower($request->get('sort_order')) === 'asc' ? 'asc' : 'desc';

        $products = Product::with(['category', 'suppliers'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($request->stock_filter, function ($query, $filter) {
                if ($filter === 'low') {
                    $query->where('stock', '<=', 10)->where('stock', '>', 0);
                } elseif ($filter === 'empty') {
                    $query->where('stock', 0);
                } elseif ($filter === 'available') {
                    $query->where('stock', '>', 0);
                }
            })
            ->orderBy($sortBy, $sortOrder)
            ->paginate(6)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'sortBy', 'sortOrder'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     * Alur: Validasi input -> Simpan data -> Transaksi stok awal jika ada -> Redirect dengan session flash
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku'           => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name'          => ['required', 'string', 'min:3', 'max:150'],
            'category_id'   => ['required', 'exists:categories,id'],
            'price'         => ['required', 'numeric', 'min:0'],
            'stock'         => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['nullable', 'integer', 'min:0'],
            'unit'          => ['nullable', 'string', 'max:20'],
            'description'   => ['nullable', 'string'],
            'suppliers'     => ['nullable', 'array'],
            'suppliers.*'   => ['exists:suppliers,id'],
        ], [
            'sku.unique'       => 'Kode SKU sudah terdaftar, SKU harus unik!',
            'price.min'        => 'Harga tidak boleh negatif.',
            'stock.min'        => 'Stok tidak boleh negatif.',
            'name.min'         => 'Nama produk minimal harus 3 karakter.',
            'category_id.required' => 'Silakan pilih kategori produk.',
        ]);

        $validated['minimum_stock'] = $validated['minimum_stock'] ?? 10;
        $validated['unit'] = $validated['unit'] ?? 'pcs';

        DB::transaction(function () use ($validated, $request) {
            $product = Product::create($validated);

            // Sync Many-to-Many Suppliers jika dipilih
            if (!empty($request->suppliers)) {
                $attachData = [];
                foreach ($request->suppliers as $supId) {
                    $attachData[$supId] = [
                        'supply_price' => $validated['price'] * 0.6,
                        'lead_time_days' => 2,
                    ];
                }
                $product->suppliers()->sync($attachData);
            }

            // Jika stok awal > 0, catat sebagai transaksi stok masuk awal
            if ($validated['stock'] > 0) {
                StockMovement::create([
                    'reference_no'     => 'TRX-INIT-' . date('Ymd') . '-' . sprintf('%03d', $product->id),
                    'product_id'       => $product->id,
                    'supplier_id'      => $request->suppliers[0] ?? null,
                    'user_id'          => null,
                    'type'             => 'IN',
                    'quantity'         => $validated['stock'],
                    'stock_before'     => 0,
                    'stock_after'      => $validated['stock'],
                    'notes'            => 'Saldo stok awal saat pendaftaran menu baru',
                    'transaction_date' => now(),
                ]);
            }
        });

        return redirect()->route('products.index')
                         ->with('success', "Menu '{$validated['name']}' ({$validated['sku']}) berhasil ditambahkan ke inventaris!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'suppliers', 'stockMovements' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();
        $selectedSuppliers = $product->suppliers->pluck('id')->toArray();

        return view('products.edit', compact('product', 'categories', 'suppliers', 'selectedSuppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku'           => ['required', 'string', 'max:50', 'unique:products,sku,' . $product->id],
            'name'          => ['required', 'string', 'min:3', 'max:150'],
            'category_id'   => ['required', 'exists:categories,id'],
            'price'         => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['nullable', 'integer', 'min:0'],
            'unit'          => ['nullable', 'string', 'max:20'],
            'description'   => ['nullable', 'string'],
            'suppliers'     => ['nullable', 'array'],
            'suppliers.*'   => ['exists:suppliers,id'],
        ], [
            'sku.unique' => 'Kode SKU sudah terdaftar pada produk lain.',
            'price.min'  => 'Harga tidak boleh negatif.',
            'name.min'   => 'Nama produk minimal 3 karakter.',
        ]);

        $validated['minimum_stock'] = $validated['minimum_stock'] ?? 10;
        $validated['unit'] = $validated['unit'] ?? 'pcs';

        DB::transaction(function () use ($product, $validated, $request) {
            $product->update($validated);

            if ($request->has('suppliers')) {
                $attachData = [];
                foreach ($request->suppliers as $supId) {
                    $attachData[$supId] = [
                        'supply_price' => $validated['price'] * 0.6,
                        'lead_time_days' => 2,
                    ];
                }
                $product->suppliers()->sync($attachData);
            } else {
                $product->suppliers()->detach();
            }
        });

        return redirect()->route('products.index')
                         ->with('success', "Data produk '{$product->name}' berhasil diperbarui!");
    }

    /**
     * Remove the specified resource from storage.
     * Business Rule P4: Produk yang memiliki histori transaksi dilarang dihapus!
     */
    public function destroy(Product $product)
    {
        if ($product->stockMovements()->count() > 0) {
            return redirect()->route('products.index')
                             ->with('error', "Aturan Bisnis: Produk '{$product->name}' tidak boleh dihapus karena memiliki riwayat mutasi transaksi!");
        }

        $product->suppliers()->detach();
        $product->delete();

        return redirect()->route('products.index')
                         ->with('success', "Produk '{$product->name}' berhasil dihapus dari inventaris.");
    }
}
