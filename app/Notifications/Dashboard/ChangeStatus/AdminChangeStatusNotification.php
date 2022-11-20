<?php

namespace App\Notifications\Dashboard\ChangeStatus;

use App\Http\Resources\Api\Dashboard\User\SenderResource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminChangeStatusNotification extends Notification
{
    use Queueable;
    protected $model_id;
    protected $model;
    protected $title;
    protected $body;
    protected $status;
    protected $via;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($model_id, $model, $status, $via)
    {
        $this->model_id = $model_id;
        $this->model    = $model;
        $this->status   = $status;
        $this->via      = $via;

        $this->title = [
            'ar' => trans('dashboard.change_status.' . $this->status . '.title', ['model_id' => $this->model_id], 'ar'),
            'en' => trans('dashboard.change_status.' . $this->status . '.title', ['model_id' => $this->model_id], 'en'),
        ];
        $this->body = [
            'ar' => trans('dashboard.change_status.' . $this->status . '.body', ['model_id' => $this->model_id], 'ar'),
            'en' => trans('dashboard.change_status.' . $this->status . '.body', ['model_id' => $this->model_id], 'en'),
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
        //
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
            'key'         => $this->model,
            'status'      => $this->status,
            'key_id'      => $this->model_id,
            'title'       => $this->title,
            'body'        => $this->body,
            'sender_data' => new SenderResource(auth('api')->user()),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'key'         => $this->model,
            'status'      => $this->status,
            'key_id'      => $this->model_id,
            'title'       => $this->title,
            'body'        => $this->body,
            'sender_data' => new SenderResource(auth('api')->user()),
        ]);
    }
}
