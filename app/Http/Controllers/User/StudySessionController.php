<?php

namespace App\Http\Controllers\User;

use App\Models\StudyGroup;
use App\Models\StudySession;
use Illuminate\Http\Request;
use Minishlink\WebPush\WebPush;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Minishlink\WebPush\Subscription;

class StudySessionController extends Controller
{
    public function index(StudyGroup $studyGroup)
    {
        // fetch only sessions that has not passed
        $sessions = $studyGroup->studySessions()->with('group', 'creator')->where('end_time', '>', \Carbon\Carbon::now())->get();

        $formattedSessions = $sessions->map(function ($session) use ($studyGroup) {
            return [
                'title' => $session->session_title,
                'start' => $session->start_time->toIso8601String(),
                'end'   => $session->end_time->toIso8601String(),
                'url'   => route('user.study-groups.sessions.show', [$studyGroup, $session]),
            ];
        });

        return view('user.study-groups.sessions.index', [
            'title' => 'Study Sessions',
            'events' => $formattedSessions,
            'studySessions' => $sessions,
            'group' => $studyGroup
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(StudyGroup $studyGroup)
    {
        Gate::authorize('create', $studyGroup);
        $data = ['title' => 'Create Study Session', 'studyGroup' => $studyGroup];

        return view('user.study-groups.sessions.create', $data);
    }

    /**
     * Store a newly created resource (study session) in storage.
     */
    public function store(Request $request, StudyGroup $studyGroup)
    {
        Gate::authorize('create', $studyGroup);
        $request->validate([
            'session_title' => 'required|unique:study_sessions,session_title',
            'start_time'    => 'required|date',
            'end_time'      => 'required|date|after:start_time',
            'description'   => 'nullable|string|max:255',
        ]);

        //* Check for schedule conflict
        $conflict = $studyGroup->studySessions()->where('study_group_id', $request->group_id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                    });
            })->exists();

        if ($conflict) {
            return back()->with('error', 'Another session is already scheduled within that time range.');
        }

        try {
            $session = $studyGroup->studySessions()->create(
                [
                    ...$request->all(),
                    'creator_id' => $request->user()->id,
                    'participants' => [$request->user()->id],
                ]
            );

            $webPush = new WebPush(fetch_vapid_credentials());

            // Send notification to all group members
            $studyGroup->members()->each(function ($member) use ($studyGroup, $session, $webPush) {
                if ($member->id == $session->creator_id) {
                    return;
                }
                $member->notify(new \App\Notifications\NewStudySessionNotification($studyGroup, $session));

                // Send Web Push Notifications instantly
                foreach ($member->webPushSubscriptions as $subscription) {
                    $webPush->sendOneNotification(
                        Subscription::create(json_decode($subscription->data, true)),
                        json_encode([
                            'title' => 'New Study Session',
                            'body' => $session->session_title,
                            'icon' => '/logo-white.jpg',
                            'url' => route('user.study-groups.sessions.show', [$studyGroup, $session]),
                        ])
                    );
                }

                //Or push to queue
                // \App\Jobs\SendSessionCreatedPushNotification::dispatch($member, $session, $studyGroup, $webPush);
            });

            return redirect(route('user.study-groups.sessions.index', $studyGroup))->with('success', 'Study session created successfully');
        } catch (\Throwable $th) {
            Log::channel('study_session')->error($th);
            return back()->with('error', 'Something went wrong');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StudyGroup $studyGroup, StudySession $studySession)
    {
        // Abort if session has passed
        abort_if($studySession->end_time < \Carbon\Carbon::now(), 404, 'This study session has already passed');

        $data = ['title' => 'Study Session', 'group' => $studyGroup, 'session' => $studySession->load('creator:id,username')];

        return view('user.study-groups.sessions.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudySession $studySession)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudySession $studySession)
    {
        Gate::authorize('update', $studySession);

        $request->validate(
            [
                'session_title' => 'required',
                'start_time'    => 'required|date',
                'end_time'      => 'required|date|after:start_time',
                'description'   => 'nullable|string|max:255',
            ]
        );

        $studySession->update($request->all());

        return redirect(route('user.study-groups.sessions.show', $studySession->group, $studySession))->with('success', 'Study session updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudyGroup $studyGroup, StudySession $studySession)
    {
        Gate::authorize('delete', $studySession);
        $studySession->delete();

        return redirect(route('user.study-groups.sessions.index', [$studyGroup, $studySession]))->with('success', 'Study session deleted successfully');
    }

    public function toggleParticipation($group, $sessionId)
    {
        $session = StudySession::findOrFail($sessionId);
        $user = request()->user();
        $userId = $user->id;

        $participants = $session->participants ?? [];
        $isParticipating = in_array($userId, $participants);
        $message = $isParticipating
            ? 'You have left the study session'
            : 'You have joined the study session';

        // Update participants list
        if ($isParticipating) {
            $participants = array_values(array_filter($participants, fn($id) => $id != $userId));
        } else {
            $participants[] = $userId;

            // Notify existing participants (excluding current user)
            $otherParticipantIds = array_filter($participants, fn($id) => $id != $userId);
            if (!empty($otherParticipantIds)) {
                /**
                 * NOTE: Uncomment the line dispatching the queued job if you want to queue the web push notification
                 * Dispatch a queued job to send a web push notification to other participants
                 * If you're on shared hosting and cannot run php artisan queue:work persistently, you can:
                 * 1. Use a database queue driver. This can be set from your env file: QUEUE_CONNECTION=database
                 * 2. Add to your cPanel cron job every minute: php /home/your_user/your_project/artisan queue:work --stop-when-empty
                 *
                 */
                // \App\Jobs\SendSessionJoinPushNotification::dispatch($userId, $session->id, $otherParticipantIds);

                $users = \App\Models\User::with('webPushSubscriptions')->findMany($otherParticipantIds);
                $webPush = new WebPush(fetch_vapid_credentials());

                foreach ($users as $participant) {
                    foreach ($participant->webPushSubscriptions as $subscription) {
                        $webPush->sendOneNotification(
                            Subscription::create(json_decode($subscription->data, true)),
                            json_encode([
                                'title' => 'New Participant',
                                'body'  => $user->username . " has joined the study session: {$session->session_title}",
                                'icon'  => '/logo-white.jpg',
                                'url'   => route('user.study-groups.sessions.show', [$group, $session]),
                            ])
                        );
                    }
                }
                $webPush->flush();
            }
        }

        $session->participants = array_values($participants);
        $session->save();

        return back()->with('success', $message);
    }


    public function toggleParticipationOld($group, $session)
    {

        $session = StudySession::find($session);

        $participants = $session->participants ?? [];
        $userId = request()->user()->id;

        $message = '';

        if (in_array($userId, $participants)) {
            // Leave session
            $participants = array_filter($participants, fn($id) => $id != $userId);
            $message = 'You have left the study session';
        } else {
            // Join session
            $participants[] = $userId;
            $message = 'You have joined the study session';

            // Send a web push notifications to other participants
            $webPush = new WebPush(fetch_vapid_credentials());

            $existing_participant_ids = array_filter($session->participants, fn($id) => $id != $userId);

            foreach ($existing_participant_ids as $participantId) {
                $participant = \App\Models\User::find($participantId);
                foreach ($participant->webPushSubscriptions as $subscription) {
                    $webPush->sendOneNotification(
                        Subscription::create(json_decode($subscription->data, true)),
                        json_encode([
                            'title' => 'New Participant',
                            'body'  => request()->user()->username . " has joined the study session: $session->session_title",
                            'icon'  => '/logo-white.jpg',
                            'url'   => route('user.study-groups.sessions.show', [$group, $session]),
                        ])
                    );
                }
            }
        }

        $session->participants = array_values($participants);
        $session->save();

        return back()->with('success', $message);
    }

    public function calendarEvents($studyGroupId)
    {
        $studyGroup = StudyGroup::find($studyGroupId);
        $sessions = $studyGroup->studySessions()->with('group')->where('end_time', '>', \Carbon\Carbon::now())->get();

        $events = $sessions->map(function ($session) {
            return [
                'title' => $session->session_title,
                'start' => $session->start_time->toIso8601String(),
                'end'   => $session->end_time->toIso8601String(),
                'url'   => route('user.study-groups.sessions.show', [$session->group, $session]),
            ];
        });

        return response()->json($events);
    }
}
