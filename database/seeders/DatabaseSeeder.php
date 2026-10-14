<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. Seed Users (Admin & Staff)
        $admin = User::firstOrCreate(
            ['email' => 'admin@sweetbakes.com'],
            [
                'name' => 'Nadya Salsabila Zahra (Administrator)',
                'password' => Hash::make('admin123'),
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@sweetbakes.com'],
            [
                'name' => 'Budi Santoso (Staff Gudang)',
                'password' => Hash::make('staff123'),
            ]
        );

        // 2. Seed Categories dari P4
        $categoriesData = [
            ['name' => 'Pastry', 'description' => 'Aneka pastry panggang berlapis renyah seperti croissant dan danish.'],
            ['name' => 'Artisan Cake', 'description' => 'Kue bolu premium dan cheesecake untuk perayaan dan kafe.'],
            ['name' => 'Cupcake & Muffin', 'description' => 'Kue mungil lembut dengan aneka topping dan isian buah.'],
            ['name' => 'Cookies & Treats', 'description' => 'Kue kering artisan renyah di luar dan lumer di dalam.'],
            ['name' => 'Bread & Loaf', 'description' => 'Roti gandum ragi alami sourdough dan brioche mentega.'],
        ];

        $categories = [];
        foreach ($categoriesData as $c) {
            $categories[$c['name']] = Category::create($c);
        }

        // 3. Seed Suppliers dari P4
        $suppliersData = [
            [
                'name' => 'PT Parisienne Dairy & Butter',
                'contact_person' => 'Jean Pierre',
                'phone' => '081234567890',
                'email' => 'contact@parisienne.co.id',
                'address' => 'Kawasan Industri Pulo Gadung, Jakarta Timur',
            ],
            [
                'name' => 'Belcolade Cacao Indonesia',
                'contact_person' => 'Rian Hidayat',
                'phone' => '081987654321',
                'email' => 'sales@belcolade.id',
                'address' => 'Jl. Gatot Subroto No. 45, Bandung',
            ],
            [
                'name' => 'Kyoto Uji Tea Importir',
                'contact_person' => 'Meilani Wijaya',
                'phone' => '081345678912',
                'email' => 'info@kyotouji.com',
                'address' => 'Ruko Central Park No. 12, Surabaya',
            ],
            [
                'name' => 'Sentosa Flour Mill & Grain',
                'contact_person' => 'Hendra Tan',
                'phone' => '081122334455',
                'email' => 'order@sentosaflour.co.id',
                'address' => 'Kawasan Industri Cikarang, Bekasi',
            ],
        ];

        $suppliers = [];
        foreach ($suppliersData as $s) {
            $suppliers[] = Supplier::create($s);
        }

        // 4. Seed Products dari P4
        $productsData = [
            [
                'sku' => 'SWB-PST-001',
                'name' => 'Butter Croissant Parisienne',
                'category_id' => $categories['Pastry']->id,
                'price' => 28000.00,
                'stock' => 24,
                'minimum_stock' => 10,
                'unit' => 'pcs',
                'description' => 'Croissant mentega Prancis dengan tekstur renyah dan rongga berlapis sempurna.',
            ],
            [
                'sku' => 'SWB-PST-002',
                'name' => 'Pain au Chocolat Belgia',
                'category_id' => $categories['Pastry']->id,
                'price' => 34000.00,
                'stock' => 18,
                'minimum_stock' => 10,
                'unit' => 'pcs',
                'description' => 'Pastry gulung berisi lelehan cokelat hitam murni Belgia 70%.',
            ],
            [
                'sku' => 'SWB-CAK-001',
                'name' => 'Signature Dark Chocolate Fudge Cake',
                'category_id' => $categories['Artisan Cake']->id,
                'price' => 245000.00,
                'stock' => 6,
                'minimum_stock' => 10,
                'unit' => 'slice',
                'description' => 'Kue bolu cokelat lembut berlapis ganache dark chocolate premium.',
            ],
            [
                'sku' => 'SWB-CAK-002',
                'name' => 'Uji Matcha Basque Cheesecake',
                'category_id' => $categories['Artisan Cake']->id,
                'price' => 275000.00,
                'stock' => 5,
                'minimum_stock' => 10,
                'unit' => 'slice',
                'description' => 'Cheesecake panggang beraroma teh hijau Uji Kyoto dengan lumeran krim lembut.',
            ],
            [
                'sku' => 'SWB-CUP-001',
                'name' => 'Red Velvet Cream Cheese Cupcake',
                'category_id' => $categories['Cupcake & Muffin']->id,
                'price' => 32000.00,
                'stock' => 30,
                'minimum_stock' => 10,
                'unit' => 'pcs',
                'description' => 'Cupcake merah beludru klasik berpadu frosting cream cheese vanila lembut.',
            ],
            [
                'sku' => 'SWB-CUP-002',
                'name' => 'Blueberry Crumble Muffin',
                'category_id' => $categories['Cupcake & Muffin']->id,
                'price' => 29000.00,
                'stock' => 20,
                'minimum_stock' => 10,
                'unit' => 'pcs',
                'description' => 'Muffin vanila isi selai bluberi segar dengan taburan remah mentega garing.',
            ],
            [
                'sku' => 'SWB-COO-001',
                'name' => 'Double Choco Walnut Soft Cookie',
                'category_id' => $categories['Cookies & Treats']->id,
                'price' => 22000.00,
                'stock' => 35,
                'minimum_stock' => 10,
                'unit' => 'pcs',
                'description' => 'Kue kering empuk berlumur cokelat leleh dan renyahnya kacang walnut.',
            ],
            [
                'sku' => 'SWB-BRD-001',
                'name' => 'Artisan French Sourdough Loaf',
                'category_id' => $categories['Bread & Loaf']->id,
                'price' => 48000.00,
                'stock' => 12,
                'minimum_stock' => 10,
                'unit' => 'loaf',
                'description' => 'Roti gandum ragi alami dengan kulit renyah dan tekstur dalam kenyal gurih.',
            ],
        ];

        $products = [];
        foreach ($productsData as $p) {
            $products[] = Product::create($p);
        }

        // 5. Seed Many-to-Many: Product Suppliers
        $products[0]->suppliers()->attach([
            $suppliers[0]->id => ['supply_price' => 15000.00, 'lead_time_days' => 2],
            $suppliers[3]->id => ['supply_price' => 6000.00, 'lead_time_days' => 3],
        ]);
        $products[1]->suppliers()->attach([
            $suppliers[0]->id => ['supply_price' => 16000.00, 'lead_time_days' => 2],
            $suppliers[1]->id => ['supply_price' => 12000.00, 'lead_time_days' => 4],
        ]);
        $products[2]->suppliers()->attach([
            $suppliers[1]->id => ['supply_price' => 120000.00, 'lead_time_days' => 5],
        ]);
        $products[3]->suppliers()->attach([
            $suppliers[2]->id => ['supply_price' => 140000.00, 'lead_time_days' => 7],
        ]);
        $products[4]->suppliers()->attach([
            $suppliers[0]->id => ['supply_price' => 14000.00, 'lead_time_days' => 2],
        ]);
        $products[5]->suppliers()->attach([
            $suppliers[3]->id => ['supply_price' => 11000.00, 'lead_time_days' => 3],
        ]);
        $products[6]->suppliers()->attach([
            $suppliers[1]->id => ['supply_price' => 9000.00, 'lead_time_days' => 4],
        ]);
        $products[7]->suppliers()->attach([
            $suppliers[3]->id => ['supply_price' => 18000.00, 'lead_time_days' => 2],
        ]);

        // 6. Seed Stock Movements (Barang Masuk & Keluar)
        StockMovement::create([
            'reference_no' => 'TRX-IN-20261010-001',
            'product_id' => $products[0]->id,
            'supplier_id' => $suppliers[0]->id,
            'user_id' => $admin->id,
            'type' => 'IN',
            'quantity' => 30,
            'stock_before' => 0,
            'stock_after' => 30,
            'notes' => 'Penerimaan batch fresh bake mentega Parisienne',
            'transaction_date' => '2026-10-10 08:30:00',
        ]);

        StockMovement::create([
            'reference_no' => 'TRX-OUT-20261010-002',
            'product_id' => $products[0]->id,
            'supplier_id' => null,
            'user_id' => $staff->id,
            'type' => 'OUT',
            'quantity' => 6,
            'stock_before' => 30,
            'stock_after' => 24,
            'notes' => 'Distribusi penjualan pagi etalase kafe',
            'transaction_date' => '2026-10-10 11:15:00',
        ]);
    }
}
