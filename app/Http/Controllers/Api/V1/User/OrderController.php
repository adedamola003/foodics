<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Classes\OrderClass;
use App\Http\Controllers\Api\V1\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends BaseController
{
    private OrderClass $orderClass;

    public function __construct()
    {
        //$this->middleware('auth:sanctum');
        $this->orderClass = new OrderClass();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function createOrder(Request $request): JsonResponse
    {
        //validate order request
        $validator = $this->orderClass->validateOrderRequest($request);

        //check if validation failed
        if ($validator->fails()){
            return $this->sendError('Validation Error.', $validator->messages());
        }

        //get validated request data
        $validated = $validator->validated();

        ////get all products for the order from database
        $requestProducts = $this->orderClass->getRequestProducts($validated);

        //get all Ingredients from requestProducts collection
        $ingredientsData = $this->orderClass->getIngredientsData($requestProducts);

        //Create order and order items for the order
        $createOrder = $this->orderClass->createOrderAndOrderItems($validated, $requestProducts, $ingredientsData);
        //check if order created successfully
        if ($createOrder['status']){
            //return success response
            return $this->sendResponse($createOrder['data'], 'Order created successfully.', 201);
        }else{
            //return error response
            return $this->sendError('Order creation failed.', $createOrder['message']);
        }

    }

}
