<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'passport_id',
        'source_airport',
        'destination_airport',
        'departure_time',
        'aircraft_number',
        'seat',
        'status'
    ];

    /**
     * Relationship: Source Airport (belongs to an Airport).
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'source_airport', 'code');
    }

    /**
     * Relationship: Destination Airport (belongs to an Airport).
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'destination_airport', 'code');
    }
}
