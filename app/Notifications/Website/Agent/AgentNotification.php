<?php

namespace App\Notifications\Website\Agent;

use App\Http\Resources\Api\WebSite\User\SenderResource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgentNotification extends Notification
{
    use Queueable;
    protected $agent_id;
    protected $title;
    protected $body;
    protected $action;
    protected $via;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($agent_id, $action, $via)
    {
        $this->agent_id  = $agent_id;
        $this->action    = $action;
        $this->via       = $via;

        $this->title = [
            'ar' => trans('website.agent.notifications.title.'. $this->action, ['agent_id' => $this->agent_id], 'ar'),
            'en' => trans('website.agent.notifications.title.'. $this->action, ['agent_id' => $this->agent_id], 'en'),
        ];
        $this->body = [
            'ar' => trans('website.agent.notifications.body.'. $this->action, ['agent_id' => $this->agent_id, 'client_name' => auth('api')->user()->name], 'ar'),
            'en' => trans('website.agent.notifications.body.'. $this->action, ['agent_id' => $this->agent_id, 'client_name' => auth('api')->user()->name], 'en'),
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
            'key'         => "agent",
            'key_id'      => $this->agent_id,
            'title'       => $this->title,
            'body'        => $this->body,
            'sender_data' => new SenderResource(auth('api')->user()),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'key'         => "agent",
            'key_id'      => $this->agent_id,
            'title'       => $this->title,
            'body'        => $this->body,
            'sender_data' => new SenderResource(auth('api')->user()),
        ]);
    }
}
