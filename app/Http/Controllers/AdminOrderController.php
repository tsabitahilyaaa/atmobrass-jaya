<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->latest()->paginate(10)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,completed,cancelled',
        ]);

        // Tidak boleh mengubah status menjadi "paid"
        // jika pembayaran belum diverifikasi
        if ($request->status == 'paid' && $order->payment_status != 'verified') {
            return back()->with(
                'error',
                'Pembayaran harus diverifikasi terlebih dahulu.'
            );
        }

        $order->update([
            'status' => $request->status,
        ]);

        return back()->with(
            'success',
            'Status pesanan diperbarui.'
        );
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Pesanan berhasil dihapus.');
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }
    public function verify($id)
    {
        $order = Order::findOrFail($id);

        $order->update([
            'payment_status' => 'verified',
            'status' => 'paid',
        ]);

        return back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }
    
    public function reject($id)
    {
        $order = Order::findOrFail($id);

        $order->update([
            'payment_status' => 'rejected',
        ]);

        return back()->with('success', 'Pembayaran ditolak.');
    }
}