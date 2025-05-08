<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $activeOrders = Order::where('is_archived', false)
                           ->orderBy('created_at', 'desc')
                           ->get();
                           
        $archivedOrders = Order::where('is_archived', true)
                             ->orderBy('updated_at', 'desc')
                             ->get();
                             
        return view('orders.index', compact('activeOrders', 'archivedOrders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0.1',
            'date' => 'required|date',
            'service_type' => 'required|array',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric',
            'special_instructions' => 'nullable|string',
        ]);

        // Create the order
        $order = Order::create([
            'order_name' => $validated['order_name'],
            'weight' => $validated['weight'],
            'date' => $validated['date'],
            'service_type' => json_encode($validated['service_type']),
            'status' => 'Pending',
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'pending',
            'amount' => $validated['amount'],
            'special_instructions' => $validated['special_instructions'] ?? null,
            'is_archived' => false,
        ]);

        $receiptContent = $this->generateReceiptContent($order);
        
        $receiptFilename = 'receipt_'.$order->id.'_'.time().'.txt';
        Storage::put('receipts/'.$receiptFilename, $receiptContent);

        return Storage::download('receipts/'.$receiptFilename, 'Laundry_Receipt_'.$order->id.'.txt');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Washing,Drying,Ironing,Ready,Completed'
        ]);
        
        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json(['success' => 'Order status updated successfully!']);
    }

    public function markAsPaid(Order $order)
{
    if ($order->status !== 'Completed') {
        return response()->json([
            'error' => 'Order must be completed before marking as paid.'
        ], 422);
    }

    $order->update([
        'payment_status' => 'paid',
        'is_archived' => true
    ]);

    return response()->json([
        'message' => 'Order marked as paid and archived successfully.'
    ]);
}

    // Removed duplicate archiveOrder method to avoid redeclaration error.

    public function archiveOrder($id)
    {
        $order = Order::findOrFail($id);
        
        if ($order->status !== 'Completed' || $order->payment_status !== 'paid') {
            return response()->json([
                'error' => 'Order must be completed and paid before archiving.'
            ], 422);
        }
        
        $order->update(['is_archived' => true]);
        
        return response()->json(['message' => 'Order archived successfully']);
    }

    public function unarchiveOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['is_archived' => false]);
        
        return response()->json(['message' => 'Order restored from archive']);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order);
    }

    protected function generateReceiptContent(Order $order)
    {
        $services = implode(', ', json_decode($order->service_type));
        
        $receipt = "================================\n";
        $receipt .= "        LAUNDRY RECEIPT         \n";
        $receipt .= "================================\n";
        $receipt .= "Order ID: #" . $order->id . "\n";
        $receipt .= "Date: " . $order->date . "\n";
        $receipt .= "Customer: " . $order->order_name . "\n";
        $receipt .= "--------------------------------\n";
        $receipt .= "Services: " . $services . "\n";
        $receipt .= "Weight: " . $order->weight . " kg\n";
        $receipt .= "Payment Method: " . $order->payment_method . "\n";
        $receipt .= "Status: " . $order->status . "\n";
        $receipt .= "Payment Status: " . $order->payment_status . "\n";
        
        if ($order->special_instructions) {
            $receipt .= "Special Instructions: " . $order->special_instructions . "\n";
        }
        
        $receipt .= "--------------------------------\n";
        $receipt .= "Total Amount: $" . number_format($order->amount, 2) . "\n";
        $receipt .= "================================\n";
        $receipt .= "Thank you for your business!\n";
        $receipt .= "================================\n";

        return $receipt;
    }
}