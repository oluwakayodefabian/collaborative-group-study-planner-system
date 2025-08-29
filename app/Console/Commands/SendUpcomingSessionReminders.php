<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendUpcomingSessionReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-upcoming-session-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for sessions starting soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = \Carbon\Carbon::now();
        $window = $now->copy()->addMinutes(15); //* Notify 15 minutes before session starts

        $sessions = \App\Models\StudySession::whereBetween('start_time', [$now, $window])->get();
        $webPush = new \Minishlink\WebPush\WebPush(fetch_vapid_credentials());

        foreach ($sessions as $session) {
            $participantIds = $session->participants;

            //* Get participants and their push subscriptions in one query
            $participants = \App\Models\User::with('webPushSubscriptions')
                ->whereIn('id', $participantIds)
                ->get()
                ->keyBy('id'); // Easier lookup by user_id

            // Who has already been notified
            $alreadyNotifiedUsers = DB::table('session_user_notifications')
                ->where('study_session_id', $session->id)
                ->pluck('user_id')
                ->toArray();

            foreach ($participants as $participant) {
                // Laravel notification (email or database)
                if (!in_array($participant->id, $alreadyNotifiedUsers)) {
                    $participant->notify(new \App\Notifications\UpcomingStudySession($session));
                    $participant->markNotifiedFor($session); //* Log notification

                    //* Web push notifications
                    foreach ($participant->webPushSubscriptions as $subscription) {
                        try {
                            $webPush->sendOneNotification(
                                \Minishlink\WebPush\Subscription::create(json_decode($subscription->data, true)),
                                json_encode([
                                    'title' => $session->session_title,
                                    'body' => 'Your study session "' . $session->session_title . '" starts soon.',
                                    'icon' => '/logo-white.jpg',
                                    'url' => route('user.study-groups.sessions.show', [$session->study_group_id, $session->id]),
                                ])
                            );
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::error('Push failed: ' . $e->getMessage(), ['user_id' => $participant->id]);
                        }
                    }
                }
            }
        }

        $webPush->flush(); //* Ensure all messages are sent
        \Illuminate\Support\Facades\Log::channel('study_session')->info('Sent notifications for ' . count($sessions) . ' sessions.');
        $this->info('Sent notifications for ' . count($sessions) . ' sessions.');
    }


    public function handleOld()
    {
        $now = \Carbon\Carbon::now();
        $window = $now->copy()->addMinutes(15); //* Notify 15 minutes before session starts

        $sessions = \App\Models\StudySession::whereBetween('start_time', [$now, $window])->get();
        // $sessions = \App\Models\StudySession::get();

        $pushSubscriptions = \App\Models\WebPushSubscription::get();

        $webPush = new \Minishlink\WebPush\WebPush(fetch_vapid_credentials());

        foreach ($sessions as $session) {
            $participants = \App\Models\User::with('webPushSubscriptions')->whereIntegerInRaw('id', $session->participants)->get();
            $alreadyNotifiedUsers = \Illuminate\Support\Facades\DB::table('session_user_notifications')
                ->select(('user_id'))
                ->where('study_session_id', $session->id)
                ->pluck('user_id')->toArray();

            foreach ($participants as $participant) {
                // $participantPushSubscriptions = $participant->webPushSubscriptions;
                if (!$participant->hasBeenNotifiedFor($session)) {
                    $participant->notify(new \App\Notifications\UpcomingStudySession($session));
                    $participant->markNotifiedFor($session); // logs into pivot table
                }
            }

            foreach ($pushSubscriptions as $subscription) {
                // Check if user has been notified before
                if (in_array($subscription->user_id, $alreadyNotifiedUsers)) {
                    continue;
                }
                $webPush->sendOneNotification(
                    \Minishlink\WebPush\Subscription::create(json_decode($subscription->data, true)),
                    json_encode([
                        'title' => $session->session_title,
                        'body' => 'Your study session "' . $session->session_title . '" starts soon.',
                        'icon' => '/logo-white.jpg',
                        'url' => route('user.study-groups.sessions.show', [$session->study_group_id, $session->id])
                        // 'url' => url('user/study-groups/' . $session->group->id . '/sessions/' . $session->id)
                    ])
                );
            }
        }
        $webPush->flush(); // Ensures delivery
        $this->info('Sent notifications for ' . count($sessions) . ' sessions');
    }
}
