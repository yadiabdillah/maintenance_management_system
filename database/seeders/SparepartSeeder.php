<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sparepart;
use App\Models\SparepartTransaction;

class SparepartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Jarum Juki DBx1 Size 14
        $part1 = Sparepart::create([
            'sku' => 'SP-JK-DBX1-14',
            'name' => 'Jarum Jahit Juki DBx1 Size 14',
            'stock' => 95,
            'min_stock' => 20,
        ]);

        SparepartTransaction::create([
            'sparepart_id' => $part1->id,
            'type' => 'IN',
            'qty' => 100,
            'supplier_name' => 'Hong Lin Sewing Machine Pte Ltd',
            'unit_price' => 15000.00,
            'remarks' => 'Stok awal pembelian pabrik utama',
            'created_by' => 'System',
            'created_at' => now()->subDays(5),
        ]);

        SparepartTransaction::create([
            'sparepart_id' => $part1->id,
            'type' => 'OUT',
            'qty' => 5,
            'remarks' => 'Dipakai mekanik Budi untuk mesin PM-LGD1-120400-04681',
            'created_by' => 'System',
            'created_at' => now()->subDays(2),
        ]);

        // 2. Heating Element Okurma JC-16G
        $part2 = Sparepart::create([
            'sku' => 'SP-OP-JC16G-HT',
            'name' => 'Heating Element Okurma JC-16G',
            'stock' => 5,
            'min_stock' => 2,
        ]);

        SparepartTransaction::create([
            'sparepart_id' => $part2->id,
            'type' => 'IN',
            'qty' => 5,
            'supplier_name' => 'PT Indohotama Sejati',
            'unit_price' => 1200000.00,
            'remarks' => 'Pemesanan khusus perbaikan heater press',
            'created_by' => 'System',
            'created_at' => now()->subDays(10),
        ]);

        // 3. Su Lee Straight Knife Cutter Blade 10"
        $part3 = Sparepart::create([
            'sku' => 'SP-SL-SES629-BL',
            'name' => 'Su Lee Straight Knife Cutter Blade 10"',
            'stock' => 13,
            'min_stock' => 5,
        ]);

        SparepartTransaction::create([
            'sparepart_id' => $part3->id,
            'type' => 'IN',
            'qty' => 15,
            'supplier_name' => 'Hong Lin Sewing Machine Pte Ltd',
            'unit_price' => 95000.00,
            'remarks' => 'Restock pisau cadangan garmen',
            'created_by' => 'System',
            'created_at' => now()->subDays(6),
        ]);

        SparepartTransaction::create([
            'sparepart_id' => $part3->id,
            'type' => 'OUT',
            'qty' => 2,
            'remarks' => 'Penggantian pisau tumpul pada mesin PM-LGD1-120200-00124',
            'created_by' => 'System',
            'created_at' => now()->subDays(1),
        ]);

        // 4. Dinamo Jack M5
        $part4 = Sparepart::create([
            'sku' => 'SP-JK-M5-MT',
            'name' => 'Intelligent Process Sewing Machine Motor Jack M5',
            'stock' => 2,
            'min_stock' => 1,
        ]);

        SparepartTransaction::create([
            'sparepart_id' => $part4->id,
            'type' => 'IN',
            'qty' => 2,
            'supplier_name' => 'PT Techindo Solusi Cemerlang',
            'unit_price' => 3200000.00,
            'remarks' => 'Cadangan Dinamo Jack M5 baru',
            'created_by' => 'System',
            'created_at' => now()->subDays(4),
        ]);
        
        // 5. Tension Spring Typical (LOW STOCK ALERT DEMO)
        $part5 = Sparepart::create([
            'sku' => 'SP-TY-TS-01',
            'name' => 'Thread Tension Spring Typical GC6',
            'stock' => 3,
            'min_stock' => 10, // LOW STOCK ALERT triggered!
        ]);

        SparepartTransaction::create([
            'sparepart_id' => $part5->id,
            'type' => 'IN',
            'qty' => 15,
            'supplier_name' => 'PT Indohotama Sejati',
            'unit_price' => 4500.00,
            'remarks' => 'Restock pegas tension jahit',
            'created_by' => 'System',
            'created_at' => now()->subDays(8),
        ]);

        SparepartTransaction::create([
            'sparepart_id' => $part5->id,
            'type' => 'OUT',
            'qty' => 12,
            'remarks' => 'Pembagian massal untuk penggantian di Line A, B, dan C',
            'created_by' => 'System',
            'created_at' => now()->subDays(3),
        ]);
    }
}
