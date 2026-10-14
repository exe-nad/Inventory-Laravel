<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Tampilkan riwayat transaksi stok (Search, Filter Type & Product, Pagination)
     */
    public function index(Request $request)
    {
        $movements = StockMovement::with(['product', 'supplier'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reference_no', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%")
                      ->orWhereHas('product', function ($pq) use ($search) {
                          $pq->where('name', 'like', "%{$search}%")
                             ->orWhere('sku', 'like', "%{$search}%");
                      });
                });
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->product_id, function ($query, $productId) {
                $query->where('product_id', $productId);
            })
            ->latest('transaction_date')
            ->paginate(8)
            ->withQueryString();

        $products = Product::orderBy('name')->get();

        return view('stock.index', compact('movements', 'products'));
    }

    /**
     * Form Barang Masuk (IN)
     */
    public function createIn(Request $request)
    {
        $products  = Product::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $preselectedId = $request->get('product_id');

        return view('stock.in', compact('products', 'suppliers', 'preselectedId'));
    }

    /**
     * Simpan Transaksi Barang Masuk (IN)
     */
    public function storeIn(Request $request)
    {
        $validated = $request->validate([
            'product_id'  => ['required', 'exists:products,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'quantity'    => ['required', 'integer', 'min:1'],
            'notes'       => ['nullable', 'string', 'max:255'],
        ], [
            'quantity.min' => 'Jumlah barang masuk harus berupa bilangan bulat positif lebih dari 0.',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::lockForUpdate()->findOrFail($validated['product_id']);
            $stockBefore = $product->stock;
            $stockAfter  = $stockBefore + $validated['quantity'];

            $refNo = 'TRX-IN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            StockMovement::create([
                'reference_no'     => $refNo,
                'product_id'       => $product->id,
                'supplier_id'      => $validated['supplier_id'] ?? null,
                'user_id'          => null,
                'type'             => 'IN',
                'quantity'         => $validated['quantity'],
                'stock_before'     => $stockBefore,
                'stock_after'      => $stockAfter,
                'notes'            => $validated['notes'] ?? 'Barang masuk restock',
                'transaction_date' => now(),
            ]);

            $product->update(['stock' => $stockAfter]);
        });

        return redirect()->route('stock.index')
                         ->with('success', 'Transaksi barang masuk berhasil dicatat!');
    }

    /**
     * Form Barang Keluar (OUT)
     */
    public function createOut(Request $request)
    {
        $products = Product::orderBy('name')->get();
        $preselectedId = $request->get('product_id');

        return view('stock.out', compact('products', 'preselectedId'));
    }

    /**
     * Simpan Transaksi Barang Keluar (OUT)
     * Business Rule P4: Jumlah keluar tidak boleh melebihi stok, stok tidak boleh negatif
     */
    public function storeOut(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
            'notes'      => ['nullable', 'string', 'max:255'],
        ], [
            'quantity.min' => 'Jumlah barang keluar harus bernilai minimal 1.',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($validated['quantity'] > $product->stock) {
            return back()->withInput()->withErrors([
                'quantity' => "Jumlah barang keluar ({$validated['quantity']}) melebihi stok yang tersedia ({$product->stock}) untuk produk '{$product->name}'."
            ]);
        }

        DB::transaction(function () use ($validated, $product) {
            $lockedProduct = Product::lockForUpdate()->findOrFail($product->id);
            $stockBefore   = $lockedProduct->stock;
            $stockAfter    = $stockBefore - $validated['quantity'];

            $refNo = 'TRX-OUT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            StockMovement::create([
                'reference_no'     => $refNo,
                'product_id'       => $lockedProduct->id,
                'supplier_id'      => null,
                'user_id'          => null,
                'type'             => 'OUT',
                'quantity'         => $validated['quantity'],
                'stock_before'     => $stockBefore,
                'stock_after'      => $stockAfter,
                'notes'            => $validated['notes'] ?? 'Pengeluaran etalase/penjualan',
                'transaction_date' => now(),
            ]);

            $lockedProduct->update(['stock' => $stockAfter]);
        });

        return redirect()->route('stock.index')
                         ->with('success', 'Transaksi barang keluar berhasil dicatat!');
    }
}
