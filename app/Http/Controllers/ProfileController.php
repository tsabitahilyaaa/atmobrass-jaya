<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $statusOptions = [
            'all' => 'Semua',
            'pending' => 'Belum Dibayar',
            'paid' => 'Dibayar',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $activeStatus = $request->input('status', 'all');
        $ordersQuery = $user->orders()->with('items')->latest('ordered_at');

        if ($activeStatus !== 'all' && array_key_exists($activeStatus, $statusOptions)) {
            $ordersQuery->where('status', $activeStatus);
        }

        $orders = $ordersQuery->paginate(8)->withQueryString();

        $counts = $user->orders()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $orderSummary = [
            'pending' => $counts['pending'] ?? 0,
            'paid' => $counts['paid'] ?? 0,
            'processing' => $counts['processing'] ?? 0,
            'shipped' => $counts['shipped'] ?? 0,
            'completed' => $counts['completed'] ?? 0,
        ];

        $addresses = $user->addresses()->orderByDesc('is_default')->latest()->get();
        $defaultAddress = $addresses->first();

        return view('profile.index', compact(
            'user',
            'orders',
            'statusOptions',
            'activeStatus',
            'orderSummary',
            'addresses',
            'defaultAddress'
        ));
    }

    public function edit()
    {
        $user = auth()->user();
        $defaultAddress = $user->addresses()->where('is_default', true)->first();

        return view('profile.edit', compact('user', 'defaultAddress'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $hasDefaultAddress = $user->addresses()->where('is_default', true)->exists();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'regex:/^[0-9]{1,15}$/', 'max:15'],
            'address' => $hasDefaultAddress ? 'nullable|string|max:1000' : 'required|string|max:1000',
            'city' => $hasDefaultAddress ? 'nullable|string|max:255' : 'required|string|max:255',
            'province' => $hasDefaultAddress ? 'nullable|string|max:255' : 'required|string|max:255',
            'postal_code' => $hasDefaultAddress ? 'nullable|string|max:20' : 'required|string|max:20',
        ], [
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        $addressData = [
            'recipient_name' => $validated['name'],
            'phone' => $validated['phone'] ?? $user->phone,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'province' => $validated['province'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
        ];

        if ($addressData['address'] || $addressData['city'] || $addressData['province'] || $addressData['postal_code']) {
            $defaultAddress = $user->addresses()->where('is_default', true)->first();

            if (! $defaultAddress) {
                $user->addresses()->update(['is_default' => false]);
                $defaultAddress = new Address(['is_default' => true]);
                $defaultAddress->user()->associate($user);
            }

            $defaultAddress->fill($addressData);
            $defaultAddress->is_default = true;
            $defaultAddress->save();

            $user->addresses()->where('id', '!=', $defaultAddress->id)->update(['is_default' => false]);
        }

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function password()
    {
        return view('profile.password');
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.'])->withInput();
        }

        $user->update(['password' => Hash::make($request->input('password'))]);

        return redirect()->route('profile')->with('success', 'Password berhasil diperbarui.');
    }

    public function show(Order $order)
    {
        $this->authorizeOrder($order);

        $statusSteps = [
            'pending' => 'Pembayaran',
            'paid' => 'Diproses',
            'processing' => 'Dikirim',
            'shipped' => 'Dalam Pengiriman',
            'completed' => 'Selesai',
        ];

        return view('profile.orders.show', compact('order', 'statusSteps'));
    }

    public function reorder(Order $order)
    {
        $this->authorizeOrder($order);

        if ($order->status !== 'completed') {
            return redirect()->back()->with('error', 'Hanya pesanan selesai yang dapat dibeli lagi.');
        }

        $cart = auth()->user()->cart()->firstOrCreate(['user_id' => auth()->id()]);
        $messages = [];
        $addedCount = 0;

        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);

            if (! $product || ! $product->is_active || $product->stock < 1) {
                $messages[] = "Produk \"{$item->product_name}\" tidak tersedia saat ini.";
                continue;
            }

            $quantityToAdd = min($item->quantity, $product->stock);
            $cartItem = $cart->items()->where('product_id', $product->id)->first();

            if ($cartItem) {
                $cartItem->update(['quantity' => $cartItem->quantity + $quantityToAdd]);
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantityToAdd,
                ]);
            }

            $addedCount += $quantityToAdd;

            if ($quantityToAdd < $item->quantity) {
                $messages[] = "Hanya { $quantityToAdd } dari {$item->product_name} ditambahkan karena stok terbatas.";
            }
        }

        if ($addedCount === 0) {
            return redirect()->route('profile')->with('error', 'Tidak ada produk yang dapat ditambahkan ke keranjang. Silakan coba lagi nanti.');
        }

        $successMessage = 'Produk dari pesanan berhasil ditambahkan ke keranjang.';
        if (! empty($messages)) {
            $successMessage .= ' ' . implode(' ', $messages);
        }

        return redirect()->route('cart.index')->with('success', $successMessage);
    }

    private function authorizeOrder(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
