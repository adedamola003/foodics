<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    public function loginUser(){

        $response = $this->post('/api/v1/auth/login', [
            'email' => 'admin@foodics.com',
            'password' => 'P@55w0rd@Foodics',
        ]);

        return $response['data']['accessToken'];

    }
   //test that an authenticated user cannot create orders
    public function test_authenticated_user_cannot_create_order()
    {
        //get a random product
        $product = Product::inRandomOrder()->first();
        $response = $this->post('/api/v1/order/create', [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);


        $response->assertStatus(401);
        $response->assertUnauthorized();
        $this->assertEquals(false, $response['success']);
        $this->assertEquals('Unauthenticated', $response['message']);
    }

    //test that an authenticated user can create orders
    public function test_authenticated_user_can_create_orders()
    {
        //get a random product
        $product = Product::inRandomOrder()->first();


        $token = $this->loginUser();
        $response = $this->withHeader('Authorization', 'Bearer '.$token)->post('/api/v1/order/create', [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $this->assertAuthenticated();
        $response->assertStatus(201);
        $this->assertEquals(true, $response['success']);
        $this->assertEquals("Order created successfully.", $response['message']);
    }




}
