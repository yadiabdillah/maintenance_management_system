<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Machine::query();

        // Filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('fa_tag_no', 'like', "%{$search}%")
                  ->orWhere('fa_desc', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('sect_code')) {
            $query->where('sect_code', $request->input('sect_code'));
        }

        if ($request->filled('loc_code')) {
            $query->where('loc_code', $request->input('loc_code'));
        }

        if ($request->filled('condition_status')) {
            $query->where('condition_status', $request->input('condition_status'));
        }

        $machines = $query->latest()->paginate(10)->withQueryString();
        
        // Fetch unique options for filters
        $sections = Machine::whereNotNull('sect_code')->where('sect_code', '!=', '')->distinct()->pluck('sect_code');
        $locations = Machine::whereNotNull('loc_code')->where('loc_code', '!=', '')->distinct()->pluck('loc_code');

        return view('machines.index', compact('machines', 'sections', 'locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('machines.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fa_tag_no' => 'required|unique:machines,fa_tag_no',
            'fa_desc' => 'required',
            'sect_code' => 'required',
            'loc_code' => 'required',
        ]);

        $data = $request->all();
        $data['create_by'] = auth()->user()->name ?? 'System';
        $data['create_date'] = now()->toDateTimeString();

        Machine::create($data);

        return redirect()->route('machines.index')->with('success', 'Mesin berhasil ditambahkan secara manual.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Machine $machine)
    {
        return view('machines.show', compact('machine'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Machine $machine)
    {
        return view('machines.edit', compact('machine'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Machine $machine)
    {
        $request->validate([
            'fa_tag_no' => 'required|unique:machines,fa_tag_no,' . $machine->id,
            'fa_desc' => 'required',
            'sect_code' => 'required',
            'loc_code' => 'required',
        ]);

        $data = $request->all();
        $data['last_modify_by'] = auth()->user()->name ?? 'System';
        $data['last_modify_date'] = now()->toDateTimeString();

        $machine->update($data);

        return redirect()->route('machines.index')->with('success', 'Data mesin berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Machine $machine)
    {
        $machine->delete();
        return redirect()->route('machines.index')->with('success', 'Mesin berhasil dihapus.');
    }

    /**
     * Bulk Import from TSV Data (Excel copy-paste).
     */
    public function import(Request $request)
    {
        $request->validate([
            'tsv_data' => 'required|string',
        ]);

        $rawData = $request->input('tsv_data');
        
        // Split by lines
        $lines = preg_split('/\r\n|\r|\n/', trim($rawData));
        
        if (count($lines) < 2) {
            return back()->withErrors(['tsv_data' => 'Data tidak valid atau terlalu sedikit baris. Harap sertakan baris header.']);
        }

        // Parse header row
        $headers = explode("\t", array_shift($lines));
        $mappedFields = [];
        
        $headerMap = [
            'onsite' => 'onsite',
            'fatagno' => 'fa_tag_no',
            'familydesc' => 'family_desc',
            'suppcode' => 'supp_code',
            'suppname' => 'supp_name',
            'suppinvoice' => 'supp_invoice',
            'localimport' => 'local_import',
            'linkdoc' => 'link_doc',
            'bcdoc' => 'bc_doc',
            'acqdate' => 'acq_date',
            'refsage' => 'ref_sage',
            'sagecoa' => 'sage_coa',
            'physictagno' => 'physic_tag_no',
            'fadesc' => 'fa_desc',
            'fasubdesc' => 'fa_sub_desc',
            'faunit' => 'fa_unit',
            'acqcost' => 'acq_cost',
            'deptcode' => 'dept_code',
            'sectcode' => 'sect_code',
            'subsectcode' => 'sub_sect_code',
            'loccode' => 'loc_code',
            'linecode' => 'line_code',
            'serialnumber' => 'serial_number',
            'crosschecksn' => 'cross_check_sn',
            'livetime' => 'live_time',
            'conditionstatus' => 'condition_status',
            'remark' => 'remark',
            'qrcodeimage' => 'qr_code_image',
            'assetimage1' => 'asset_image1',
            'assetimage2' => 'asset_image2',
            'assetimage3' => 'asset_image3',
            'createby' => 'create_by',
            'createdate' => 'create_date',
            'lastmodifyby' => 'last_modify_by',
            'lastmodifydate' => 'last_modify_date',
            'flagsync' => 'flag_sync',
            'assignee' => 'assignee',
            'lastupdatelog' => 'last_update_log',
            'uniqid' => 'uniq_id',
        ];

        foreach ($headers as $index => $header) {
            $normalized = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', trim($header)));
            if (isset($headerMap[$normalized])) {
                $mappedFields[$index] = $headerMap[$normalized];
            }
        }

        // Ensure we have the critical identifier column
        if (!in_array('fa_tag_no', $mappedFields)) {
            return back()->withErrors(['tsv_data' => 'Header FATagNo tidak ditemukan. Harap pastikan baris pertama berisi nama header kolom Excel yang benar.']);
        }

        $insertedCount = 0;
        $updatedCount = 0;

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            $row = explode("\t", $line);
            $machineData = [];

            foreach ($mappedFields as $index => $columnName) {
                if (isset($row[$index])) {
                    $machineData[$columnName] = trim($row[$index]);
                }
            }

            if (empty($machineData['fa_tag_no'])) continue;

            // Perform DB UPSERT based on the unique fa_tag_no
            $existing = Machine::where('fa_tag_no', $machineData['fa_tag_no'])->first();
            if ($existing) {
                $existing->update($machineData);
                $updatedCount++;
            } else {
                Machine::create($machineData);
                $insertedCount++;
            }
        }

        return redirect()->route('machines.index')
            ->with('success', "Proses sinkronisasi data berhasil! Ditambahkan: {$insertedCount} mesin baru, Diperbarui: {$updatedCount} mesin.");
    }
}
