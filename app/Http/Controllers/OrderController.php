<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\OrderStatusChanged;

/**
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Отримати список замовлень користувача",
     *     description="Повертає всі замовлення, створені авторизованим користувачем",
     *     tags={"Orders"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Список замовлень",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Order"))
     *     )
     * )
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->paginate(10);
        return response()->json($orders);
    }


    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Створити нове замовлення",
     *     security={{"sanctum":{}}},
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_name","amount"},
     *             @OA\Property(property="product_name", type="string", example="Laptop"),
     *             @OA\Property(property="amount", type="number", format="float", example=1500.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Замовлення створено",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     )
     * )
     */
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


    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Отримати деталі замовлення",
     *     tags={"Orders"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID замовлення"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Деталі замовлення",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     )
     * )
     */
    public function show($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        return response()->json($order);
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     summary="Оновити замовлення",
     *     tags={"Orders"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID замовлення"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="product_name", type="string", example="Laptop"),
     *             @OA\Property(property="amount", type="number", format="float", example=1600.00),
     *             @OA\Property(property="status", type="string", example="в обробці")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Замовлення оновлено",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'product_name' => 'string|max:255',
            'amount' => 'numeric|min:0',
            'status' => 'in:новий,в обробці,відправлений,доставлений',
        ]);

        $order->update($request->only(['product_name', 'amount', 'status']));

        if ($order->wasChanged('status')) {
            $order->user->notify(new OrderStatusChanged($order, $order->status));
        }
        //$order->user->notify(new OrderStatusChanged($order, $order->status));


        return response()->json($order);
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     summary="Видалити замовлення",
     *     tags={"Orders"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID замовлення"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Замовлення видалено",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Order deleted successfully")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully']);
    }
}
