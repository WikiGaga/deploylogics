<?php

namespace App\Notifications;

use App\Models\TblNotificationSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class GlobalNotification extends Notification
{
    use Queueable;

    public $model;
    public $url;
    public $data;

    protected $setting;

    public function __construct($model, $url, $data = [])
    {
        $this->model = $model;
        $this->url = $url;
        $this->data = $data;
    }

    protected function getSetting()
    {
        if (!$this->setting) {
            $this->setting = TblNotificationSetting::where('key', $this->model)->first();
        }

        return $this->setting;
    }

    protected function getTitle()
    {
        return optional($this->getSetting())->title ?? 'Notification';
    }

    protected function getMessage()
    {
        $message = optional($this->getSetting())->message ?? 'You have a new notification.';

        foreach ($this->data as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }

        return $message;
    }

    public function via($notifiable)
    {
        $setting = $this->getSetting();

        if (!$setting) {
            return ['database'];
        }

        $channels = ['database'];

        if ($setting->push_notification_status === 'active') {
            $channels[] = WebPushChannel::class;
            $channels[] = 'broadcast';
        }

        if ($setting->mail_status === 'active' && !empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        // if ($setting->sms_status === 'active' && !empty($notifiable->phone)) {
        //     $channels[] = 'vonage';
        // }

        // if ($setting->whatsapp_status === 'active' && !empty($notifiable->phone)) {
        //     $channels[] = WhatsAppChannel::class;
        // }

        return $channels;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->getTitle())
            ->line($this->getMessage())
            ->action('View Details', url($this->url));
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title($this->getTitle())
            ->icon('/images/malek-al-pizza.png')
            ->body($this->getMessage())
            ->action('View', $this->url)
            ->options(['TTL' => 1000])
            ->vibrate([100, 50, 100]);
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'url' => $this->url,
            'data' => $this->data,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'url' => $this->url,
            'data' => $this->data,
        ]);
    }
}