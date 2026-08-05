<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
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

    public function test_checkout_uses_saved_address_id_and_fills_order_from_selected_address(): void
    {
        $user = User::factory()->create();

        $address = Address::create([
            'user_id' => $user->id,
            'recipient_name' => 'Test User',
            'phone' => '081234567890',
            'address' => 'Jl. Saved Address 123',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '40111',
            'is_default' => true,
        ]);

        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category-' . uniqid(),
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

        $this->actingAs($user)
            ->post('/keranjang/tambah', [
                'product_id' => $product->id,
                'qty' => 1,
            ]);

        $response = $this->actingAs($user)
            ->from('/checkout')
            ->post('/checkout', [
                'address_id' => $address->id,
                'payment_amount' => 150000,
                'notes' => '',
            ]);

        $response->assertRedirect('/riwayat-pesanan');
        $response->assertSessionHas('success');

        $order = Order::query()->latest('id')->first();
        $this->assertNotNull($order);
        $this->assertSame($address->id, $order->address_id);
        $this->assertSame($address->recipient_name, $order->shipping_name);
        $this->assertSame($address->phone, $order->shipping_phone);
        $this->assertSame($address->city, $order->shipping_city);
        $this->assertSame($address->postal_code, $order->shipping_postal);
        $this->assertSame($address->address, $order->shipping_address);
    }

    public function test_checkout_requires_saved_address_id_and_payment_amount(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from('/checkout')
            ->post('/checkout', [
                'notes' => '',
            ]);

        $response->assertSessionHasErrors('address_id');
        $response->assertSessionHasErrors('payment_amount');
    }
}
