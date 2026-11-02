<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StockAlertNotification extends Notification
{
    use Queueable;

    private $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     * Database only, so the bell icon in the header shows the alert
     * and a missing mail setup can never break a sale.
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Mail version (kept in case you set up SMTP later and add 'mail' to via()).
     */
    public function toMail($notifiable)
    {
        $url = url(route('purchases.edit', $this->data->id));
        return (new MailMessage)
                    ->greeting('Hello!')
                    ->line('The Product below is running out of stock.')
                    ->line("Product's name is ".$this->data->product ." is only ".$this->data->quantity." left in quantity")
                    ->line("Please update the product's quantity or make a new purchase.")
                    ->action('View Product', $url)
                    ->line('Thank you!');
    }

    /**
     * Data saved in the notifications table (this is what the header reads).
     */
    public function toArray($notifiable)
    {
        return [
            'product_name' => $this->data->product,
            'quantity'     => $this->data->quantity,
            'image'        => $this->data->image,
        ];
    }
}