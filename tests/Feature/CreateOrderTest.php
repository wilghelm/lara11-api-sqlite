<?php

use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Використовуємо RefreshDatabase для очищення та міграції бази даних перед кожним тестом.
uses(RefreshDatabase::class);

it('creates a new order successfully', function () {
    // Створюємо користувача для авторизації
    $user = User::factory()->create();

    // Дані нового замовлення
    $orderData = [
        'product_name' => 'Laptop',
        'amount' => 1500.00,
        'status' => 'новий',
    ];

    // Виконуємо POST-запит як авторизований користувач
    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/orders', $orderData);

    // Перевірка, що відповідь має статус 201 (створено)
    $response->assertStatus(201);

    // Перевіряємо наявність замовлення в базі даних з очікуваними значеннями
    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'product_name' => $orderData['product_name'],
        'amount' => $orderData['amount'],
        'status' => $orderData['status'],
    ]);

    // Перевірка JSON-відповіді на наявність правильних даних
    $response->assertJson([
        'product_name' => $orderData['product_name'],
        'amount' => $orderData['amount'],
        'status' => $orderData['status'],
    ]);
});
