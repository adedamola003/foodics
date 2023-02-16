<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static firstOrCreate(array $array, array $ingredient)
 * @method static find($ingredient_id)
 * @method static lockForUpdate()
 */
class Ingredient extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'max_stock',
        'stock_unit',
        'warning_threshold',
    ];

    public function ingredientUsages(): HasMany
    {
        return $this->hasMany(IngredientUsage::class);
    }

    public function ingredientBalance(): float
    {
        $balance = $this->ingredientUsages()->orderBy('created_at', 'desc')->pluck('balance')->first();
        if (empty($balance)) {
            $balance = 0;
        }
        return $balance;
    }
}
