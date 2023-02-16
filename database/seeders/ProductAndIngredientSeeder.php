<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductIngredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAndIngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //seed to Product, ingredient and product ingredient tables
        $products = [
            [
                'name' => 'Burger',
                'price' => 7.89,
                'status' => 1,
                'ingredients' => [
                    [
                        'name' => 'Beef',
                        'quantity' => 150,
                        'unit' => 'g',
                    ],
                    [
                        'name' => 'Cheese',
                        'quantity' => 30,
                        'unit' => 'g',
                    ],
                    [
                        'name' => 'Onion',
                        'quantity' => 20,
                        'unit' => 'g',
                    ],
                ],
            ],
            [
                'name' => 'Fries',
                'price' => 3.99,
                'status' => 1,
                'ingredients' => [
                    [
                        'name' => 'Potato',
                        'quantity' => 100,
                        'unit' => 'g',
                    ],
                    [
                        'name' => 'Onion',
                        'quantity' => 20,
                        'unit' => 'g',
                    ],
                ],
            ],
        ];
        //get all ingredients from database
        $allIngredients = Ingredient::all();
        foreach ($products as $product) {
            $thisProduct = Product::firstOrCreate(['name' => $product['name']], [
                'name' => $product['name'],
                'price' => $product['price'],
                'status' => '1',
            ]);
            foreach ($product['ingredients'] as $ingredient) {
                $ingredientId = $allIngredients->where('name', $ingredient['name'])->first()->id;
                ProductIngredient::firstOrCreate(['product_id' => $thisProduct->id, 'ingredient_id' => $ingredientId],
                    [
                    'product_id' => $thisProduct->id,
                    'ingredient_id' => $ingredientId,
                    'quantity' => $ingredient['quantity'],
                    'quantity_unit' => $ingredient['unit'],
                ]);
            }
        }
    }
}
