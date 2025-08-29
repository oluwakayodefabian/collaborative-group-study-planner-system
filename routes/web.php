<?php

use Minishlink\WebPush\WebPush;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\StudyFileController;
use NotificationChannels\WebPush\PushSubscription;
use App\Http\Controllers\User\StudyGroupController;
use App\Http\Controllers\User\StudySessionController;
use App\Http\Controllers\User\WebPushSubscriptionController;
use App\Models\WebPushSubscription;
use Minishlink\WebPush\Subscription;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->as('user.')->prefix('user')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::controller(StudyGroupController::class)->group(function () {
        Route::get('/study-groups', 'index')->name('study-groups.index');
        // Route::get('/study-groups/create', 'create')->name('study-groups.create');
        Route::post('/study-groups', 'store')->name('study-groups.store');
        Route::post('/study-groups/{studyGroup}/join', 'join')->name('study-groups.join');
        Route::post('/study-groups/{studyGroup}/leave', 'leave')->name('study-groups.leave');
        Route::get('/study-groups/{studyGroup}', 'show')->name('study-groups.show');
        Route::get('/study-groups/{studyGroup}/edit', 'edit')->name('study-groups.edit');
        Route::put('/study-groups/{studyGroup}', 'update')->name('study-groups.update');
        Route::delete('/study-groups/{studyGroup}', 'destroy')->name('study-groups.destroy');
    });

    Route::controller(StudySessionController::class)->group(function () {
        Route::get('/study-groups/{studyGroup}/sessions', 'index')->name('study-groups.sessions.index');
        Route::get('/study-groups/{studyGroup}/sessions/create', 'create')->name('study-groups.sessions.create');
        Route::post('/study-groups/{studyGroup}/sessions', 'store')->name('study-groups.sessions.store');
        Route::get('/study-groups/{studyGroup}/sessions/calendar-events', 'calendarEvents')->name('study-groups.sessions.calendar-events');
        Route::get('/study-groups/{studyGroup}/sessions/{studySession}', 'show')->name('study-groups.sessions.show');
        Route::get('/study-groups/{studyGroup}/sessions/{studySession}/edit', 'edit')->name('study-groups.sessions.edit');
        Route::put('/study-groups/{studyGroup}/sessions/{studySession}', 'update')->name('study-groups.sessions.update');
        Route::patch('/study-groups/{studyGroup}/sessions/{studySession}/toggle-participation', 'toggleParticipation')->name('study-groups.sessions.toggle-participation');
        Route::delete('/study-groups/{studyGroup}/sessions/{studySession}', 'destroy')->name('study-groups.sessions.destroy');
    });

    // Study Files Routes
    Route::controller(StudyFileController::class)->group(function () {
        Route::get('/study-groups/{studyGroup}/library', 'index')->name('study-groups.files.index');
        // Route::get('/study-groups/{studyGroup}/files/create', 'create')->name('study-groups.files.create');
        Route::post('/study-groups/{studyGroup}/files', 'store')->name('study-groups.files.store');
        Route::get('/study-groups/{studyGroup}/files/{studyFile}', 'show')->name('study-groups.files.show');
        Route::get('/study-groups/{studyGroup}/files/{studyFile}/edit', 'edit')->name('study-groups.files.edit');
        Route::put('/study-groups/{studyGroup}/files/{studyFile}', 'update')->name('study-groups.files.update');
        Route::delete('/study-groups/{studyGroup}/files/{studyFile}', 'destroy')->name('study-groups.files.destroy');
        Route::get('/study-groups/{studyGroup}/files/{studyFile}/download', 'download')->name('study-groups.files.download');
        Route::get('/study-groups/{studyGroup}/files/{studyFile}/preview', 'preview')->name('study-groups.files.preview');
        Route::patch('/study-groups/{studyGroup}/files/{studyFile}/rename', 'rename')->name('study-groups.files.rename');
        Route::post('/study-groups/{studyGroup}/files/{studyFile}/share', 'share')->name('study-groups.files.share');
    });

    // Chat Controller
    Route::controller(App\Http\Controllers\User\ChatController::class)->group(function () {
        Route::get('/study-groups/{studyGroup}/chat', 'show')->name('study-groups.chat.show');
        Route::post('/study-groups/{studyGroup}/chat/send', 'send')->name('study-groups.chat.send');
        Route::get('/study-sessions/{studySession}/chat', 'showSessionChat')->name('study-sessions.chat.show');
        Route::post('/study-sessions/{studySession}/chat/send', 'sendChatForStudySession')->name('study-sessions.chat.send');
    });

    Route::controller(App\Http\Controllers\User\NotificationController::class)->group(function () {
        Route::get('/notifications', 'index')->name('notifications.index');
        Route::post('/notifications/mark-as-read', 'markAsRead')->name('notifications.mark-as-read');
        Route::post('/notifications/mark-all-as-read', 'markAllAsRead')->name('notifications.mark-all-as-read');
    });

    Route::post('/push-subscription', [WebPushSubscriptionController::class, 'store'])->name('push-subscription');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// DEBUG Push Notification
Route::get('/test-push/{userId}', function ($userId) {
    // dd(openssl_error_string());
    // OpenSSL / 3.0.11

    $user = \App\Models\User::findOrFail($userId);

    // dd($user->hasSubscriptions());

    $pushSubscriptions = $user->webPushSubscriptions()->get();

    // dd($pushSubscriptions);

    $webPush = new WebPush(fetch_vapid_credentials());

    $result = [];

    foreach ($pushSubscriptions as $subscription) {
        $result  = $webPush->sendOneNotification(
            Subscription::create(json_decode($subscription->data, true)),
            json_encode([
                'title' => 'Test Notification',
                'body' => 'This is a test notification',
                'icon' => '/logo-white.jpg',
                'url' => '/'
            ])
        );
    }

    dd($result);

    // Check for active push subscriptions
    $subscriptions = $user->pushSubscriptions;

    if ($subscriptions->isEmpty()) {
        return response()->json(['error' => 'User has no push subscription'], 400);
    }

    // Log for debugging
    // \Log::info("Sending test push to user {$user->id}");

    $session = \App\Models\StudySession::first(); // You can customize this
    $user->notifyNow(new \App\Notifications\UpcomingStudySession($session));

    // return response()->json(['success' => 'Notification sent']);
    echo "Notification sent";
});




require __DIR__ . '/auth.php';
