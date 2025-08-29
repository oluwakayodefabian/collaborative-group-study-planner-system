<?php

namespace App\Jobs;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSessionCreatedPushNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public $member, public $session, public $studyGroup, public \Minishlink\WebPush\WebPush $webPush)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // $webPush = new WebPush(fetch_vapid_credentials());
        // Send Web Push Notifications
        foreach ($this->member->webPushSubscriptions as $subscription) {
            try {
                $this->webPush->sendOneNotification(
                    Subscription::create(json_decode($subscription->data, true)),
                    json_encode([
                        'title' => 'New Study Session',
                        'body' => $this->session->session_title,
                        'icon' => '/logo-white.jpg',
                        'url' => route('user.study-groups.sessions.show', [$this->studyGroup, $this->session]),
                    ])
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::channel('study_session')->error("WebPush failed for user {$this->member->id}: {$e->getMessage()}");
            }
        }

        $this->webPush->flush();
    }
}
