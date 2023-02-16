<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\IngredientUsage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //seed to Ingredient table
        $ingredients = [
            [
                'name' => 'Beef',
                'max_stock' => 20000,
                'stock_unit' => 'g',
                'warning_threshold' => 50,
            ],
            [
                'name' => 'Cheese',
                'max_stock' => 5000,
                'stock_unit' => 'g',
                'warning_threshold' => 50,
            ],
            [
                'name' => 'Onion',
                'max_stock' => 1000,
                'stock_unit' => 'g',
                'warning_threshold' => 50,
            ],
            [
                'name' => 'Potato',
                'max_stock' => 10000,
                'stock_unit' => 'g',
                'warning_threshold' => 50,
            ],
        ];

        foreach ($ingredients as $ingredient) {
            //insert ingredient
            $thisIngredient  = Ingredient::firstOrCreate(['name' => $ingredient['name']], $ingredient);
            //insert available stock for each ingredient
            //$ingredientUsageValue = convertIngredientUnitToUsageUnit($thisIngredient->max_stock, $thisIngredient->stock_unit);
            IngredientUsage::firstOrCreate(['ingredient_id' =>  $thisIngredient->id], [
                'ingredient_id' => $thisIngredient->id,
                'quantity' => $thisIngredient->max_stock,
                'balance' => $thisIngredient->max_stock,
                'unit' => $thisIngredient->stock_unit,
                'usage_type' => '1',
            ]);

        }
    }
}
