<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderStatusChanged extends Notification
{
    use Queueable;

    private $order;
    private $status;

    public function __construct(Order $order, $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Статус вашого замовлення змінено')
            ->greeting('Вітаємо, ' . $notifiable->name)
            ->line('Ваше замовлення "' . $this->order->product_name . '" змінено на статус "' . $this->status . '".')
            ->action('Переглянути замовлення', url('/orders/' . $this->order->id))
            ->line('Дякуємо, що користуєтесь нашими послугами!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->status,
        ];
    }
}
