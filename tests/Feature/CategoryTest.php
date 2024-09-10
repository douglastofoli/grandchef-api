<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_category_successfully(): void
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Bebidas'     
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', [
            'name' => 'Bebidas'      
        ]);
    }

    public function test_it_fails_to_create_a_category(): void
    {
        $response = $this->postJson('/api/categories', []);

        $response->assertStatus(422);
    }    

    public function test_it_list_all_categories_successfully(): void 
    {
        Category::factory()->create();
    
        $response = $this->getJson('/api/categories');

        $response->assertStatus(200);
        $response->assertContent('{"current_page":1,"data":[{"id":2,"name":"Bebidas","created_at":"2024-09-10T19:17:47.000000Z","updated_at":"2024-09-10T19:17:47.000000Z","products":[]}],"first_page_url":"http:\/\/localhost\/api\/categories?page=1","from":1,"last_page":1,"last_page_url":"http:\/\/localhost\/api\/categories?page=1","links":[{"url":null,"label":"&laquo; Previous","active":false},{"url":"http:\/\/localhost\/api\/categories?page=1","label":"1","active":true},{"url":null,"label":"Next &raquo;","active":false}],"next_page_url":null,"path":"http:\/\/localhost\/api\/categories","per_page":10,"prev_page_url":null,"to":1,"total":1}');
    }
}
