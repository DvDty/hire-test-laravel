<?php

namespace App\Models;

use App\Enums\MaintenanceRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'user_id',
        'status',
        'scheduled_date',
        'completed_date',
    ];

    protected $casts = [
        'status' => MaintenanceRequestStatus::class,
        'scheduled_date' => 'datetime',
        'completed_date' => 'datetime',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tireReplacements(): HasMany
    {
        return $this->hasMany(TireReplacement::class);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => MaintenanceRequestStatus::COMPLETED,
            'completed_date' => now(),
        ]);
    }
}