<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->paginate(10); // Фільтрує замовлення поточного користувача
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $order = Order::create([
            'user_id' => Auth::id(),
            'product_name' => $request->product_name,
            'amount' => $request->amount,
            'status' => 'новий',
        ]);

        return response()->json($order, 201);
    }

    public function show($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        return response()->json($order);
    }

    public function update(Request $request, $id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'product_name' => 'string|max:255',
            'amount' => 'numeric|min:0',
            'status' => 'in:новий,в обробці,відправлений,доставлений',
        ]);

        $order->update($request->only(['product_name', 'amount', 'status']));

        return response()->json($order);
    }

    public function destroy($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully']);
    }
}
