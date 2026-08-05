<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserPreferenceFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_shows_recommended_section_for_guest_with_session_preferences(): void
    {
        $category = Category::create([
            'name' => 'Engsel',
            'slug' => 'engsel',
            'description' => 'Kategori engsel.',
            'icon' => 'fa-hammer',
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Engsel Pintu Rumah Premium',
            'slug' => 'engsel-pintu-rumah-premium',
            'description' => 'Engsel untuk pintu rumah berkualitas.',
            'price' => 150000,
            'stock' => 10,
            'image' => 'https://example.com/product.jpg',
            'is_active' => true,
        ]);

        $response = $this->withSession(['guest_preferences' => ['Pintu Rumah']])->get(route('home'));

        $response->assertOk();
        $response->assertSee('Recommended For You');
    }

    public function test_login_transfers_guest_preferences_to_user_database(): void
    {
        $user = User::factory()->create([
            'email' => 'customer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $response = $this->withSession(['guest_preferences' => ['Pintu Rumah', 'Furniture']])
            ->post(route('login.post'), [
                'email' => 'customer@example.com',
                'password' => 'password123',
            ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('user_preferences', [
            'user_id' => $user->id,
            'preference' => 'Pintu Rumah',
        ]);
        $this->assertDatabaseHas('user_preferences', [
            'user_id' => $user->id,
            'preference' => 'Furniture',
        ]);
    }
}
