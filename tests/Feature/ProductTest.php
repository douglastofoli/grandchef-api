<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_it_creates_a_product_successfully(): void
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

    public function test_it_list_all_products_successfully(): void 
    {
        Product::factory()->create();
    
        $response = $this->getJson('/api/products');

        $response->assertStatus(200);
        $response->assertContent('{"current_page":1,"data":[{"id":2,"category_id":4,"name":"Refrigerante","price":"5.50","created_at":"2024-09-10T19:17:47.000000Z","updated_at":"2024-09-10T19:17:47.000000Z"}],"first_page_url":"http:\/\/localhost\/api\/products?page=1","from":1,"last_page":1,"last_page_url":"http:\/\/localhost\/api\/products?page=1","links":[{"url":null,"label":"&laquo; Previous","active":false},{"url":"http:\/\/localhost\/api\/products?page=1","label":"1","active":true},{"url":null,"label":"Next &raquo;","active":false}],"next_page_url":null,"path":"http:\/\/localhost\/api\/products","per_page":10,"prev_page_url":null,"to":1,"total":1}');
    }
}
