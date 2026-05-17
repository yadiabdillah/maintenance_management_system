<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'stock',
        'min_stock',
    ];

    /**
     * Get the transactions log for this sparepart.
     */
    public function transactions()
    {
        return $this->hasMany(SparepartTransaction::class);
    }
}
