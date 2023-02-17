<?php

namespace App\Http\Controllers\Api\V1\User;


use App\Classes\IngredientClass;
use App\Http\Controllers\Api\V1\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class IngredientController extends BaseController
{
    private IngredientClass $ingredientClass;

    public function __construct()
    {
        $this->ingredientClass = new IngredientClass();
    }

    /**
     * @return JsonResponse
     */
    public function getIngredients(): \Illuminate\Http\JsonResponse
    {
        $ingredientsData = $this->ingredientClass->getAllIngredientData();
        return $this->sendResponse($ingredientsData, 'Ingredients data retrieved successfully successfully.', 200);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function getIngredient(int $id): \Illuminate\Http\JsonResponse
    {
        $ingredientData = $this->ingredientClass->getIngredientData($id);
        if(!$ingredientData['status']) {
            return $this->sendError($ingredientData['message'], $ingredientData['data']);
        }

        return $this->sendResponse($ingredientData['data'], 'Ingredient data retrieved successfully successfully.', 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function topUpIngredientStock(Request $request): JsonResponse
    {
        //validate request
        $validator = $this->ingredientClass->validateTopUpIngredientStockRequest($request);
        if ($validator->fails()){
            return $this->sendError('Validation Error.', $validator->messages());
        }
        $validated = $validator->validated();

        //top up ingredient stock
        $topUpIngredientStock = $this->ingredientClass->topUpIngredientStock($validated);
        if(!$topUpIngredientStock['status']) {
            return $this->sendError($topUpIngredientStock['message'], $topUpIngredientStock['data']);
        }

        return $this->sendResponse($topUpIngredientStock['data'], 'Ingredient stock topped up successfully.', 201);
    }
}
