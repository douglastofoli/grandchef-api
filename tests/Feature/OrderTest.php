<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_it_can_create_an_order(): void
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

    public function test_it_can_not_create_an_order(): void
    {
        $product1 = Product::factory()->create(['name' => 'Produto 1']);

        $response = $this->postJson('/api/orders', [
            'products' => [
                [
                    'product_id' => $product1['id'],
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(400);
    }

    public function test_it_can_retrieve_an_order_list(): void 
    {
        Order::factory()
                ->hasAttached(Product::factory()->count(3), ['price' => 5.5, 'quantity' => 3])
                ->create();
    
        $response = $this->getJson('/api/orders');

        $response->assertStatus(200);
    }

    public function test_it_can_retrieve_an_order(): void 
    {
        $order = Order::factory()
                ->hasAttached(Product::factory()->count(3), ['price' => 5.5, 'quantity' => 3])
                ->create();
    
        $response = $this->getJson('/api/orders/' . $order['id']);

        $response->assertStatus(200);
    }

    public function test_it_can_update_an_order_status(): void 
    {
        
        $order = Order::factory()
                ->hasAttached(Product::factory()->count(3), ['price' => 5.5, 'quantity' => 3])
                ->create();

        $response = $this->putJson('/api/orders/' . $order['id'], [
            'status' => 'completed'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $order['id'],
            'status' => 'completed'
        ]);
    }

    public function test_it_can_not_update_an_order_status(): void 
    {
        
        $order = Order::factory()
                ->hasAttached(Product::factory()->count(3), ['price' => 5.5, 'quantity' => 3])
                ->create();

        $response = $this->putJson('/api/orders/' . $order['id'], [
            'status' => 'invalid_status'
        ]);

        $response->assertStatus(400);
        $response->assertContent('{"message":{"status":["The selected status is invalid."]}}');
    }
}
