@component('mail::message')
    # Статус заказа изменен

    Заказ #{{ $order->id }}

    **Новый статус:** {{ $order->formatted_status }}
    **Сумма:** {{ $order->total_amount }} ₽

    @component('mail::button', ['url' => route('orders.show', $order->id)])
        Посмотреть заказ
    @endcomponent

    Спасибо,<br>
    {{ config('app.name') }}
@endcomponent
