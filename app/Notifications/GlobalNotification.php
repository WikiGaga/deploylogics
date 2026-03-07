<?php

namespace App\Notifications;

use App\Models\TblNotificationSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class GlobalNotification extends Notification
{
    use Queueable;

    public $model;
    public $title;
    public $message;
    public $url;
    public $data;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($model, $title, $message, $url = '', $data = [])
    {
        $this->model = $model;
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
         $setting = TblNotificationSetting::where('key', $this->model)->first();

        if (! $setting) {
            // default fallback
            return ['database'];
        }

        $channels = [];
        $channels[] = 'database';
        $channels[] = WebPushChannel::class;
        $channels[] = 'broadcast';

        if ($setting->mail_status === 'active' && !empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        // For old Laravel you may use "nexmo"
        // For newer Laravel the official channel name is "vonage"
        if ($setting->sms_status === 'active' && !empty($notifiable->phone)) {
            $channels[] = 'vonage';
        }

        // Custom channel classes
        if ($setting->whatsapp_status === 'active' && !empty($notifiable->phone)) {
            $channels[] = '';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return null;
        // Check the Settings for the modal if the email notification is enabled or not
        if (TblNotificationSetting::where('key', $this->model)->where('mail_status', 'active')->exists()) {
                // $notificationSetting = TblNotificationSetting::where('model', $this->model)->where('mail_status', 'active')->first();
                // $message = str_replace('{message}', $this->message, $notificationSetting->template);

                return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url($this->url))
                    ->line('Thank you for using our application!');
        }else{
            return null;
        }
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Approved!')
            ->icon('/approved-icon.png')
            ->body('Your account was approved!')
            ->action('View account', 'view_account')
            ->options(['TTL' => 1000]);
            // ->data(['id' => $notification->id])
            // ->badge()
            // ->dir()
            // ->image()
            // ->lang()
            // ->renotify()
            // ->requireInteraction()
            // ->tag()
            // ->vibrate()
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
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url
        ];
    }

    /**
    * Get the broadcastable representation of the notification.
    *
    * @param  mixed  $notifiable
    * @return BroadcastMessage
    */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'data' => $this->data
        ]);
    }
}
