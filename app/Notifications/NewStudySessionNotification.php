<?php

namespace App\Notifications;

use App\Models\StudyGroup;
use App\Models\StudySession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewStudySessionNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public StudyGroup $group, public StudySession $session)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Study Session')
            ->line('A new study session has been created in ' . $this->group->name)
            ->line('Check it out! ' . $this->session->session_title)
            ->action('View Study Session', route('user.study-groups.sessions.show', [$this->group, $this->session->id]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subject' => 'New Study Session',
            'message' => 'A new study session has been created in ' . $this->group->name,
        ];
    }
}
