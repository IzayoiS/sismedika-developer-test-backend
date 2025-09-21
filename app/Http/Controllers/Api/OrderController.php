<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['table', 'user', 'items.food'])->get();
        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'table_id' => 'required|exists:restaurant_tables,id'
        ]);

        $table = Table::findOrFail($data['table_id']);
        if ($table->status === 'occupied') {
            return response()->json(['message' => 'Table already occupied'], 400);
        }

        if ($table->status === 'inactive') {
            return response()->json(['message' => 'Table inactive, cannot be used'], 400);
        }

        $order = Order::create([
            'table_id' => $table->id,
            'user_id' => $request->user()->id,
            'status' => 'open',
            'opened_at' => now()
        ]);

        $table->update(['status' => 'occupied']);

        return response()->json([
            'status' => 'success',
            'message' => 'Order created successfully',
            'datta' => $order,
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Order::with(['table', 'user', 'items.food'])->findOrFail($id);
        return response()->json($order);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function addItem(Request $request, $id)
    {
        $order = Order::where('status', 'open')->findOrFail($id);

        $data = $request->validate([
            'food_id' => 'required|exists:foods,id',
            'qty' => 'required|integer|min:1'
        ]);

        $food = Food::findOrFail($data['food_id']);

        DB::transaction(function () use ($order, $food, $data) {
            $subtotal = $food->price * $data['qty'];

            OrderItem::create([
                'order_id' => $order->id,
                'food_id' => $food->id,
                'qty' => $data['qty'],
                'price' => $food->price,
                'subtotal' => $subtotal
            ]);

            $order->total_price += $subtotal;
            $order->save();
        });

        return response()->json($order->load('items.food'));
    }

    public function closeOrder($id)
    {
        $order = Order::with('items')->where('status', 'open')->findOrFail($id);

        $total = $order->items->sum('subtotal');
        $order->update([
            'status' => 'closed',
            'total_price' => $total,
            'closed_at' => now(),
        ]);

        $order->table->update(['status' => 'available']);

        return response()->json(['message' => 'Order closed', 'order' => $order]);
    }

    public function receipt($id)
    {
        $order = Order::with(['items.food', 'table', 'user'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.receipt', compact('order'));

        return $pdf->download("receipt-{$order->id}.pdf");
    }

}
