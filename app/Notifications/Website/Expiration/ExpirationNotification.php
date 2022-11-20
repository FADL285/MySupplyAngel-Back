<?php

namespace App\Notifications\Website\Expiration;

use App\Http\Resources\Api\WebSite\User\SenderResource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpirationNotification extends Notification
{
    use Queueable;
    protected $expiration_id;
    protected $title;
    protected $body;
    protected $action;
    protected $via;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($expiration_id, $action, $via)
    {
        $this->expiration_id = $expiration_id;
        $this->action        = $action;
        $this->via           = $via;

        $this->title = [
            'ar' => trans('website.expiration.notifications.title.'. $this->action, ['expiration_id' => $this->expiration_id], 'ar'),
            'en' => trans('website.expiration.notifications.title.'. $this->action, ['expiration_id' => $this->expiration_id], 'en'),
        ];
        $this->body = [
            'ar' => trans('website.expiration.notifications.body.'. $this->action, ['expiration_id' => $this->expiration_id, 'client_name' => auth('api')->user()->name], 'ar'),
            'en' => trans('website.expiration.notifications.body.'. $this->action, ['expiration_id' => $this->expiration_id, 'client_name' => auth('api')->user()->name], 'en'),
        ];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $this->via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'key'         => "expiration",
            'key_id'      => $this->expiration_id,
            'title'       => $this->title,
            'body'        => $this->body,
            'sender_data' => new SenderResource(auth('api')->user()),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'key'         => "expiration",
            'key_id'      => $this->expiration_id,
            'title'       => $this->title,
            'body'        => $this->body,
            'sender_data' => new SenderResource(auth('api')->user()),
        ]);
    }
}
