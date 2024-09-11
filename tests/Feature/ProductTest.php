<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_it_can_create_a_product(): void
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/products', [
            'name' => 'Refrigerante',
            'price' => 5.5,
            'category_id' => $category['id']     
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', [
            'name' => 'Refrigerante'     
        ]);
    }

    public function test_it_can_not_create_a_product(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Refrigerante',
            'price' => 5.5,
            'category_id' => 100    
        ]);

        $response->assertStatus(400);
    }

    public function test_it_can_retrieve_a_product_list(): void 
    {
        $product = Product::factory()->create();
    
        $response = $this->getJson('/api/products');

        $response->assertStatus(200);
        $response->assertContent('{"current_page":1,"data":[{"id":'. $product["id"] .',"category_id":' . $product["category_id"] . ',"name":"Refrigerante","price":"5.50","created_at":"2024-09-10T19:17:47.000000Z","updated_at":"2024-09-10T19:17:47.000000Z"}],"first_page_url":"http:\/\/localhost\/api\/products?page=1","from":1,"last_page":1,"last_page_url":"http:\/\/localhost\/api\/products?page=1","links":[{"url":null,"label":"&laquo; Previous","active":false},{"url":"http:\/\/localhost\/api\/products?page=1","label":"1","active":true},{"url":null,"label":"Next &raquo;","active":false}],"next_page_url":null,"path":"http:\/\/localhost\/api\/products","per_page":10,"prev_page_url":null,"to":1,"total":1}');
    }
}
