<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_number',
        'machine_id',
        'user_id',
        'assigned_to',
        'issue_description',
        'photo_path',
        'priority',
        'status',
        'sla_target_hours',
        'started_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedMechanic()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function spareparts()
    {
        return $this->belongsToMany(Sparepart::class, 'ticket_sparepart')
            ->withPivot('qty')
            ->withTimestamps();
    }
}
