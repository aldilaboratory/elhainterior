<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::withCount('items')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load(['items.product']);

        return view('customer.orders.show', compact('order'));
    }

    public function invoice(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load(['items.product']);

        $pdf = Pdf::loadView('customer.orders.invoice', [
            'order' => $order,
        ])->setPaper('A4');

        $filename = 'Invoice-'.$order->order_code.'.pdf';
        // return $pdf->download($filename);
        // Atau tampilkan di browser:
        return $pdf->stream($filename);
    }
}
