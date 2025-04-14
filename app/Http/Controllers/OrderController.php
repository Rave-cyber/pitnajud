<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{

    public function index()
{
    $orders = Order::all(); // Fetch all orders
    return view('orders.index', compact('orders'));
}
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'order_name' => 'required|string|max:255',
            'weight' => 'required|numeric',
            'date' => 'required|date',
            'service_type' => 'required|array',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        // Create a new order
        Order::create([
            'order_name' => $request->order_name,
            'weight' => $request->weight,
            'date' => $request->date,
            'service_type' => json_encode($request->service_type),
            'status' => 'Pending', // Default status
            'payment_method' => $request->payment_method,
            'amount' => $request->amount,
        ]);

        // Redirect back with a success message
        return redirect()->route('orders.index')->with('success', 'Order created successfully!');
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in progress,completed'
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Order status updated successfully!');
    }

}