<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tire extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'model',
        'type',
        'stock',
    ];


    protected function brand(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
        );
    }

    protected function model(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
        );
    }

    protected function getFullTireNameAttribute(): string
    {
        return $this->brand . " / " . $this->model . " / " . ucfirst($this->type);
    }

    public function carFrontTire(): HasMany
    {
        return $this->hasMany(Car::class, "front_tire_id");
    }

    public function carRearTire(): HasMany
    {
        return $this->hasMany(Car::class, "rear_tire_id");
    }

    public function tireReplacements(): HasMany
    {
        return $this->hasMany(TireReplacement::class);
    }

    public function hasStock(int $quantity = 1): bool
    {
        return $this->stock >= $quantity;
    }

    public function deductStock(int $quantity = 1): bool
    {
        if ($this->hasStock($quantity)) {
            $this->decrement('stock', $quantity);
            return true;
        }

        return false;
    }
}
