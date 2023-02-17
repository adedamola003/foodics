<?php

namespace App\Http\Controllers\Api\V1\User;


use App\Classes\IngredientClass;
use App\Http\Controllers\Api\V1\BaseController;


class IngredientController extends BaseController
{
    private IngredientClass $ingredientClass;

    public function __construct()
    {
        $this->ingredientClass = new IngredientClass();
    }

    public function getIngredients()
    {
        $ingredientsData = $this->ingredientClass->getAllIngredientData();
        return $this->sendResponse($ingredientsData, 'Ingredients data retrieved successfully successfully.', 200);
    }
}
