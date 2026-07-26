<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartCheckoutFlowTest extends TestCase
{
    use RefreshDatabase;
    public function test_customer_cart_checkout_and_order_routes_are_registered_and_protected(): void
    {
        $this->assertTrue(Route::has('cart.index'));
        $this->assertTrue(Route::has('checkout.index'));
        $this->assertTrue(Route::has('orders.index'));

        $this->get('/keranjang')->assertOk();
        $this->get('/checkout')->assertRedirectContains('/login');
        $this->get('/riwayat-pesanan')->assertRedirectContains('/login');
    }

    public function test_add_to_cart_stays_on_product_page_and_sets_cart_link(): void
    {
        $slug = 'test-category-' . uniqid();

        $category = Category::create([
            'name' => 'Test Category',
            'slug' => $slug,
            'description' => 'Testing',
            'icon' => 'box',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product-' . uniqid(),
            'description' => 'Testing product',
            'price' => 150000,
            'stock' => 10,
            'image' => 'https://example.com/image.jpg',
            'is_active' => true,
        ]);

        $response = $this->from('/produk/' . $product->slug)
            ->post('/keranjang/tambah', [
                'product_id' => $product->id,
                'qty' => 1,
            ]);

        $response->assertRedirect('/produk/' . $product->slug);
        $response->assertSessionHas('success', 'Produk berhasil ditambahkan ke keranjang.');
        $response->assertSessionHas('cart_link', route('cart.index'));
    }

    public function test_checkout_requires_numeric_phone_number(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from('/checkout')
            ->post('/checkout', [
                'name' => 'Test User',
                'phone' => 'abc',
                'address' => 'Alamat test',
                'notes' => '',
            ]);

        $response->assertSessionHasErrors('phone');
        $response->assertSessionHasErrors(['phone' => 'Nomor telepon hanya boleh berisi angka.']);
    }
}
