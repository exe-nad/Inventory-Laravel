<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts   = Product::count();
        $totalStock      = Product::sum('stock');
        $totalValuation  = Product::selectRaw('SUM(stock * price) as val')->value('val') ?? 0;
        $lowStockCount   = Product::where('stock', '<=', 10)->count();

        $sumQtyIn        = StockMovement::where('type', 'IN')->sum('quantity');
        $sumQtyOut       = StockMovement::where('type', 'OUT')->sum('quantity');
        $totalTrxIn      = StockMovement::where('type', 'IN')->count();
        $totalTrxOut     = StockMovement::where('type', 'OUT')->count();

        // Laporan per kategori
        $categoryStats   = Category::withCount('products')
            ->with(['products' => function ($q) {
                $q->select('id', 'category_id', 'stock', 'price');
            }])
            ->get()
            ->map(function ($cat) {
                $cat->total_stock = $cat->products->sum('stock');
                $cat->total_value = $cat->products->sum(fn ($p) => $p->stock * $p->price);
                return $cat;
            });

        $lowStockProducts = Product::with('category')
            ->where('stock', '<=', 10)
            ->orderBy('stock')
            ->take(6)
            ->get();

        $recentTransactions = StockMovement::with(['product', 'supplier'])
            ->latest('transaction_date')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalStock',
            'totalValuation',
            'lowStockCount',
            'sumQtyIn',
            'sumQtyOut',
            'totalTrxIn',
            'totalTrxOut',
            'categoryStats',
            'lowStockProducts',
            'recentTransactions'
        ));
    }
}
