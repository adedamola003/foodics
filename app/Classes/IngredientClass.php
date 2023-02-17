<?php

namespace App\Classes;


use App\Models\Ingredient;
use App\Models\IngredientUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;


class IngredientClass
{
    private Ingredient $ingredient;

    public function __construct()
    {
        $this->ingredient = new Ingredient();
    }

    /**
     * @return array
     */
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

    /**
     * @param int $id
     * @return array
     */
    public function getIngredientData(int $id): array
    {
        $ingredient = $this->ingredient::find($id);
        if (empty($ingredient)) {
          return  ['status' => false, 'message' => 'Ingredient not found', 'data' => []];
        }
        $ingredientData = [
            'id' => $ingredient->id,
            'name' => $ingredient->name,
            'max_stock_quantity' => $ingredient->max_stock,
            'available_stock' => $ingredient->ingredientBalance(),
            'unit' => $ingredient->stock_unit,
        ];

        return ['status' => true, 'message' => 'Ingredient data retrieved successfully', 'data' => $ingredientData];

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateTopUpIngredientStockRequest(Request $request): \Illuminate\Validation\Validator
    {
        return Validator::make($request->all(), [
            'ingredient_id' => 'required|integer|exists:ingredients,id',
            'quantity' => 'required|integer|min:1|max:1000000',
            'unit' => 'required|string|in:kg,g',
        ]);

    }

    /**
     * @param array $validated
     * @return array
     */
    public function topUpIngredientStock(array $validated): array
    {
        $thisIngredient = $this->ingredient::find($validated['ingredient_id']);
        $thisIngredientInitialBalance = $thisIngredient->ingredientBalance();
        $topUpData = convertIngredientUnitToUsageUnit($validated['quantity'], $validated['unit']);
        $newBalance = $thisIngredientInitialBalance + $topUpData['quantity'];
        $warningThresholdBalance = $thisIngredient->max_stock - $thisIngredient->warning_threshold;

        //update ingredient balance
        $thisIngredientTopUp = IngredientUsage::create([
            'ingredient_id' => $validated['ingredient_id'],
            'quantity' => $topUpData['quantity'],
            'unit' => $topUpData['unit'],
            'type' => '1',
            'balance' => $newBalance,
        ]);

        //remove low warning notification in cache if exists and balance is greater than warning threshold
        if($warningThresholdBalance < $newBalance && Cache::has("has_sent_ingredient_warning_" . $validated['ingredient_id'])){
                Cache::forget("has_sent_ingredient_warning_" . $validated['ingredient_id']);
        }

        $data = [
            'ingredient' => $thisIngredient->name,
            'quantity' => $thisIngredientTopUp->quantity,
            'previous_balance' => $thisIngredientInitialBalance ,
            'new_balance' => $thisIngredientTopUp->balance,
            'unit' => $thisIngredientTopUp->unit,
        ];

        return ['status' => true, 'message' => 'Ingredient stock topped up successfully', 'data' => $data];
    }


}
