<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\SparepartTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SparepartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sparepart::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Filter low stock
        if ($request->boolean('low_stock')) {
            $query->whereColumn('stock', '<=', 'min_stock');
        }

        $spareparts = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('spareparts.index', compact('spareparts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('spareparts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sku' => 'required|unique:spareparts,sku',
            'name' => 'required|string',
            'min_stock' => 'required|integer|min:0',
        ]);

        Sparepart::create([
            'sku' => $request->input('sku'),
            'name' => $request->input('name'),
            'min_stock' => $request->input('min_stock'),
            'stock' => 0, // Inital stock must be 0, adjusted via IN transactions
        ]);

        return redirect()->route('spareparts.index')
            ->with('success', 'Master data sparepart berhasil dibuat. Hubungi gudang untuk mencatat stok masuk pertama.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sparepart $sparepart)
    {
        $transactions = $sparepart->transactions()->latest()->paginate(10);
        return view('spareparts.show', compact('sparepart', 'transactions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sparepart $sparepart)
    {
        return view('spareparts.edit', compact('sparepart'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sparepart $sparepart)
    {
        $request->validate([
            'sku' => 'required|unique:spareparts,sku,' . $sparepart->id,
            'name' => 'required|string',
            'min_stock' => 'required|integer|min:0',
        ]);

        $sparepart->update($request->only(['sku', 'name', 'min_stock']));

        return redirect()->route('spareparts.index')->with('success', 'Master data sparepart berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sparepart $sparepart)
    {
        $sparepart->delete();
        return redirect()->route('spareparts.index')->with('success', 'Sparepart berhasil dihapus.');
    }

    /**
     * Search spareparts for Select2 AJAX.
     */
    public function search(Request $request)
    {
        $search = $request->input('term', '');
        $page = $request->input('page', 1);
        $perPage = 10;

        $query = Sparepart::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $total = $query->count();
        $spareparts = $query->orderBy('name')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $results = $spareparts->map(function ($sp) {
            return [
                'id' => $sp->id,
                'text' => "{$sp->name} ({$sp->sku})",
                'stock' => $sp->stock,
                'sku' => $sp->sku,
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $total,
            ],
        ]);
    }

    /**
     * Record stock transaction (IN or OUT)
     */
    public function adjustStock(Request $request, Sparepart $sparepart)
    {
        $request->validate([
            'type' => 'required|in:IN,OUT',
            'qty' => 'required|integer|min:1',
            'supplier_name' => 'required_if:type,IN|nullable|string',
            'unit_price' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $type = $request->input('type');
        $qty = $request->input('qty');

        try {
            DB::transaction(function () use ($request, $sparepart, $type, $qty) {
                if ($type === 'OUT') {
                    if ($sparepart->stock < $qty) {
                        throw new \Exception("Stok tidak mencukupi! Stok saat ini: {$sparepart->stock} pcs, permintaan: {$qty} pcs.");
                    }
                    $sparepart->decrement('stock', $qty);
                } else {
                    $sparepart->increment('stock', $qty);
                }

                SparepartTransaction::create([
                    'sparepart_id' => $sparepart->id,
                    'type' => $type,
                    'qty' => $qty,
                    'supplier_name' => $type === 'IN' ? $request->input('supplier_name') : null,
                    'unit_price' => $type === 'IN' ? $request->input('unit_price') : null,
                    'remarks' => $request->input('remarks'),
                    'created_by' => auth()->user()->name ?? 'System',
                ]);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['qty' => $e->getMessage()])->withInput();
        }

        return redirect()->route('spareparts.show', $sparepart->id)
            ->with('success', 'Transaksi keluar-masuk stok berhasil dicatat.');
    }
}
