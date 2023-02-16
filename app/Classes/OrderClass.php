<?php

namespace App\Classes;

use App\Jobs\ProcessLowIngredientWarning;
use App\Models\IngredientUsage;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Mockery\CountValidator\Exception;

class OrderClass
{
    private Order $Order;
    private Product $Product;

    public function __construct()
    {
        $this->Order = new Order();
        $this->Product = new Product();
    }
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateOrderRequest(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $messages = [
            'products.*.product_id.exists' => 'Invalid product selected',
        ];
        return Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*.product_id' => 'required|integer|exists:products,id,status,1',
            'products.*.quantity' => 'required|integer|min:1|max:100',
        ], $messages);
    }

    /**
     * @param array $validated
     * @return Collection
     */
    public function getRequestProducts(array $validated): Collection
    {
        $products_id = array_column($validated['products'], 'product_id');
        return $this->Product::with(['ingredients', 'productIngredients.ingredient'])
            ->whereIn('id', $products_id)
            ->get();
    }

    /**
     * @param Collection $requestProducts
     * @return array
     */
    public function getIngredientsData(Collection $requestProducts): array
    {
        $ingredientsData = [];
        foreach ($requestProducts as $product) {
            foreach ($product->productIngredients as $productIngredient) {
                if(!array_key_exists($productIngredient->ingredient_id, $ingredientsData)) {
                    $thisIngredientStock = $productIngredient->ingredient->ingredientBalance();
                    $ingredientsData[$productIngredient->ingredient_id] = [
                        'stock_balance' => $thisIngredientStock,
                        'warning_threshold_balance' => $productIngredient->ingredient->max_stock * ($productIngredient->ingredient->warning_threshold / 100)];
                }
            }
        }
        return $ingredientsData;
    }

    /**
     * @param array $validated
     * @param Collection $requestProducts
     * @param array $ingredientsData
     * @return array
     */
    public function createOrderAndOrderItems(array $validated, Collection $requestProducts, array $ingredientsData): array
    {
        $lowWarningData = [];
        $orderData = ['total_order_price' => 0, 'order' => []];
        try {
            DB::beginTransaction();

            $thisOrder = $this->Order::create(['customer_name' => $validated['customer_name'] ?? 'John Doe', 'order_date' => now(), 'user_id' => auth()->user()->id]);

            foreach ($validated['products'] as $product) {
                $thisOrderItemId = $thisOrder->orderItems()->create([
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity']
                ]);

                $thisProduct = $requestProducts->where('id', $product['product_id'])->first();

                foreach ($thisProduct->productIngredients as $productIngredient) {
                    $thisIngredientStock = $ingredientsData[$productIngredient->ingredient_id]['stock_balance'];
                    $thisProductIngredientQuantity = $productIngredient->quantity * $product['quantity'];
                    $ingredientsData[$productIngredient->ingredient_id]['stock_balance'] = $thisIngredientStock - $thisProductIngredientQuantity;
                    //check if there is enough ingredient for the product
                    if ($ingredientsData[$productIngredient->ingredient_id]['stock_balance'] < 0) {
                        DB::rollBack();
                        return ['status' => true, 'message' => 'Not enough ' . $productIngredient->ingredient->name . ' in stock to make ' . $thisProduct->name, 'data' => []];
                    }
                    //update ingredient usage
                    IngredientUsage::create(['order_item_id' => $thisOrderItemId->id, 'ingredient_id' => $productIngredient->ingredient_id, 'quantity' => $thisProductIngredientQuantity,
                        'balance' => $ingredientsData[$productIngredient->ingredient_id]['stock_balance'], 'unit' => $productIngredient->quantity_unit, 'usage_type' => '0',
                    ]);
                    //check if ingredient stock is below warning threshold
                    if ($ingredientsData[$productIngredient->ingredient_id]['stock_balance'] <= $ingredientsData[$productIngredient->ingredient_id]['warning_threshold_balance']) {
                        $lowWarningData[$productIngredient->ingredient_id] = [
                            'ingredient_id' => $productIngredient->ingredient_id,
                            'ingredient_name' => $productIngredient->ingredient->name,
                            'ingredient_max_stock' => $productIngredient->ingredient->max_stock,
                            'stock_balance' => $ingredientsData[$productIngredient->ingredient_id]['stock_balance'],
                            'stock_level' => ($ingredientsData[$productIngredient->ingredient_id]['stock_balance'] / $productIngredient->ingredient->max_stock) * 100,
                            'warning_threshold' => $productIngredient->ingredient->warning_threshold,
                            'warning_threshold_balance' => $ingredientsData[$productIngredient->ingredient_id]['warning_threshold_balance'],
                            'unit' => $productIngredient->ingredient->stock_unit,
                        ];

                    }
                }
                //get order data
                $orderData['order'][] = [
                    'product' => $thisProduct->name,
                    'quantity' => $product['quantity'],
                    'unit_price' => formatNumber($thisProduct->price),
                    'total_price' => formatNumber($thisProduct->price * $product['quantity']),
                ];
                $orderData['total_order_price'] += $thisProduct->price * $product['quantity'];

            }
            //check if item exists in low warning data array
            if (!empty($lowWarningData)) {
                //send array to the low warning notification job
                ProcessLowIngredientWarning::dispatch($lowWarningData)->delay(now()->addSeconds(10));
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return ['status' => true, 'message' => $e->getMessage(), 'data' => []];
        }
        return ['status' => true, 'message' => 'Order created successfully', 'data' => $orderData];
    }

}
