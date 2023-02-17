<?php

namespace App\Classes;


use App\Models\Ingredient;


class IngredientClass
{
    private Ingredient $ingredient;

    public function __construct()
    {
        $this->ingredient = new Ingredient();
    }

    public function getAllIngredientData()
    {
        $allIngredientsData = $this->ingredient::get();
        $ingredientsData = [];

        foreach ($allIngredientsData as $ingredient) {
            $ingredientsData[] = [
                'id' => $ingredient->id,
                'name' => $ingredient->name,
                'max_stock_quantity' => $ingredient->max_stock,
                'available_stock' => $ingredient->ingredientBalance(),
                'unit' => $ingredient->stock_unit,
            ];
        }

        return $ingredientsData;
    }
}
