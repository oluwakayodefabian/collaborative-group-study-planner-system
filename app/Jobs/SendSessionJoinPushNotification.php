<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\StudySession;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSessionJoinPushNotification implements ShouldQueue
{
    use Queueable;

    protected $userId;
    protected $sessionId;
    protected $participantIds;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $sessionId, array $participantIds)
    {
        $this->userId = $userId;
        $this->sessionId = $sessionId;
        $this->participantIds = $participantIds;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::find($this->userId);
        $session = \App\Models\StudySession::find($this->sessionId);

        $users = User::with('webPushSubscriptions')->findMany($this->participantIds);
        $webPush = new WebPush(fetch_vapid_credentials());

        foreach ($users as $participant) {
            $notified = false;

            // Try web push first
            foreach ($participant->webPushSubscriptions as $subscription) {
                try {
                    $webPush->sendOneNotification(
                        Subscription::create(json_decode($subscription->data, true)),
                        json_encode([
                            'title' => 'New Participant',
                            'body'  => $user->username . " has joined the study session: {$session->session_title}",
                            'icon'  => '/logo-white.jpg',
                            'url'   => route('user.study-groups.sessions.show', [$session->study_group_id, $session->id]),
                        ])
                    );
                    $notified = true;
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::channel('study_session')->error("WebPush failed for user {$participant->id}: {$e->getMessage()}");
                }
            }

            //* Fallback: Notify via Laravel (can be email, database, or both depending on channel)
            if (!$notified || $participant->webPushSubscriptions->isEmpty()) {
                $participant->notify(new \App\Notifications\SendSessionJoinFallbackNotification(
                    title: 'New Participant',
                    body: $user->username . " has joined the study session: {$session->session_title}",
                    url: route('user.study-groups.sessions.show', [$session->study_group_id, $session->id])
                ));
            }
        }

        $webPush->flush();
    }
}
