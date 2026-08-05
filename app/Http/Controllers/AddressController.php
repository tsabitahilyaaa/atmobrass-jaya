<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = auth()->user()->addresses()->orderByDesc('is_default')->latest()->get();

        return view('profile.addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('profile.addresses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => ['required', 'regex:/^[0-9]{1,15}$/', 'max:15'],
            'address' => 'required|string|max:1000',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'is_default' => 'nullable|boolean',
        ], [
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
        ]);

        if ($request->boolean('is_default')) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        $address = auth()->user()->addresses()->create([
            'recipient_name' => $validated['recipient_name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'province' => $validated['province'],
            'postal_code' => $validated['postal_code'],
            'is_default' => $request->boolean('is_default'),
        ]);

        if (! auth()->user()->addresses()->where('is_default', true)->exists()) {
            $address->update(['is_default' => true]);
        }

        return redirect()->route('profile.addresses')->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function edit(Address $address)
    {
        $this->authorizeAddress($address);

        return view('profile.addresses.edit', compact('address'));
    }

    public function update(Request $request, Address $address)
    {
        $this->authorizeAddress($address);

        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => ['required', 'regex:/^[0-9]{1,15}$/', 'max:15'],
            'address' => 'required|string|max:1000',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'is_default' => 'nullable|boolean',
        ], [
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
        ]);

        if ($request->boolean('is_default')) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        $address->update([
            'recipient_name' => $validated['recipient_name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'province' => $validated['province'],
            'postal_code' => $validated['postal_code'],
            'is_default' => $request->boolean('is_default'),
        ]);

        if (! auth()->user()->addresses()->where('is_default', true)->exists()) {
            $address->update(['is_default' => true]);
        }

        return redirect()->route('profile.addresses')->with('success', 'Alamat berhasil diperbarui.');
    }

    public function destroy(Address $address)
    {
        $this->authorizeAddress($address);

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $next = auth()->user()->addresses()->first();
            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        return redirect()->route('profile.addresses')->with('success', 'Alamat berhasil dihapus.');
    }

    public function setDefault(Address $address)
    {
        $this->authorizeAddress($address);

        auth()->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return redirect()->route('profile.addresses')->with('success', 'Alamat utama berhasil diperbarui.');
    }

    private function authorizeAddress(Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
