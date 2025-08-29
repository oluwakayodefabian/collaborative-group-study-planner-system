<?php

namespace App\Notifications;

use App\Models\StudySession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class UpcomingStudySession extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public StudySession $studySession)
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
            ->subject('Reminder: Upcoming Study Session')
            ->line('Your study session "' . $this->studySession->session_title . '" starts soon.')
            ->line('Start time: ' . $this->studySession->start_time->format('H:i A'))
            ->action('View Session', route('user.study-groups.sessions.show', [$this->studySession->group, $this->studySession->id]))
            // ->action('View Session', url('user/study-groups/' . $this->studySession->group->id . '/sessions/' . $this->studySession->id))
            ->line('Be prepared!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subject' => 'Upcoming Study Session',
            'message' => 'Your study session "' . $this->studySession->session_title . '" starts soon.',
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Upcoming Session',
            'body' => 'Your session "' . $this->studySession->session_title . '" starts soon.',
            'url' => route('user.study-groups.sessions.show', [$this->studySession->group, $this->studySession]),
        ]);
    }
}
