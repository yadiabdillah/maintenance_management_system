<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparepartTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'sparepart_id',
        'type',
        'qty',
        'supplier_name',
        'unit_price',
        'remarks',
        'created_by',
    ];

    /**
     * Get the sparepart that belongs to this transaction.
     */
    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
}
