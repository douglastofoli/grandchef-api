<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_it_creates_a_order_successfully(): void
    {
        $product1 = Product::factory()->create(['name' => 'Produto 1']);
        $product2 = Product::factory()->create(['name' => 'Produto 2']);

        $response = $this->postJson('/api/orders', [
            'products' => [
                [
                    'product_id' => $product1['id'],
                    'price' => 5.5,
                    'quantity' => 2
                ],
                [
                    'product_id' => $product2['id'],
                    'price' => 8.9,
                    'quantity' => 3        
                ]
            ]
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', [
            'name' => 'Produto 1',
            'name' => 'Produto 2'        
        ]);
        $this->assertDatabaseHas('orders', [
            'status' => 'open'     
        ]);
    }

    public function test_it_list_all_orders_successfully(): void 
    {
        $order = Order::factory()
                            ->hasAttached(Product::factory()->count(3), ['price' => 5.5, 'quantity' => 3])
                            ->create();
    
        $response = $this->getJson('/api/orders');

        $response->assertStatus(200);
        $response->assertContent('{"current_page":1,"data":[{"id":2,"status":"open","total_price":"20.00","products":[{"product_id":3,"name":"Refrigerante","price":"5.50","quantity":3},{"product_id":4,"name":"Refrigerante","price":"5.50","quantity":3},{"product_id":5,"name":"Refrigerante","price":"5.50","quantity":3}]}],"first_page_url":"http:\/\/localhost\/api\/orders?page=1","from":1,"last_page":1,"last_page_url":"http:\/\/localhost\/api\/orders?page=1","links":[{"url":null,"label":"&laquo; Previous","active":false},{"url":"http:\/\/localhost\/api\/orders?page=1","label":"1","active":true},{"url":null,"label":"Next &raquo;","active":false}],"next_page_url":null,"path":"http:\/\/localhost\/api\/orders","per_page":10,"prev_page_url":null,"to":1,"total":1}');
    }
}
