<?php

namespace App\Notifications\Website\Job;

use App\Http\Resources\Api\WebSite\User\SenderResource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobNotification extends Notification
{
    use Queueable;

    protected $job_id;
    protected $title;
    protected $body;
    protected $action;
    protected $via;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($job_id, $action, $via)
    {
        $this->job_id = $job_id;
        $this->action    = $action;
        $this->via       = $via;

        $this->title = [
            'ar' => trans('website.job.notifications.title.'. $this->action, ['job_id' => $this->job_id], 'ar'),
            'en' => trans('website.job.notifications.title.'. $this->action, ['job_id' => $this->job_id], 'en'),
        ];
        $this->body = [
            'ar' => trans('website.job.notifications.body.'. $this->action, ['job_id' => $this->job_id, 'client_name' => auth('api')->user()->name], 'ar'),
            'en' => trans('website.job.notifications.body.'. $this->action, ['job_id' => $this->job_id, 'client_name' => auth('api')->user()->name], 'en'),
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
            'key'         => "job",
            'key_id'      => $this->job_id,
            'title'       => $this->title,
            'body'        => $this->body,
            'sender_data' => new SenderResource(auth('api')->user()),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'key'         => "job",
            'key_id'      => $this->job_id,
            'title'       => $this->title,
            'body'        => $this->body,
            'sender_data' => new SenderResource(auth('api')->user()),
        ]);
    }
}
