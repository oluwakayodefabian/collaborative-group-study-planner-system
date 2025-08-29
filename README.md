# Collaborative Group Study Planner

---
Here’s a comprehensive documentation for the **Group Chatroom** feature of your “Collaborative Group Study Planner System”.

---

# 📚 Group Chatroom Feature Documentation

## 📌 Overview

The **Group Chatroom** feature facilitates real-time communication between students within a study group. It supports:

* Sending and receiving text messages
* Sharing files (e.g., PDFs, DOCX, images)
* Previewing shared files inline (images, PDFs)
* Differentiating between messages from the current user and others
* Real-time updates using **Laravel Echo** + **Pusher**

---

## 🏗️ Feature Architecture

**Backend:**

* Laravel Blade Views
* Eloquent Models: `StudyGroup`, `ChatMessage`, `User`
* Broadcasting: Laravel Events & Channels
* File Storage: Laravel Filesystem (public disk)

**Frontend:**

* Bootstrap 4 UI (media cards for chat messages)
* JavaScript for AJAX message sending and file previews
* Laravel Echo & Pusher for real-time updates

---

## 🧱 Database Models

### `chat_messages` Table

| Column      | Type      | Description                    |
| ----------- | --------- | ------------------------------ |
| id          | BIGINT    | Primary Key                    |
| user\_id    | BIGINT    | Sender (foreign key to users)  |
| group\_id   | BIGINT    | Study group ID                 |
| message     | TEXT      | Message content                |
| file\_path  | VARCHAR   | Optional path to uploaded file |
| created\_at | TIMESTAMP | Sent time                      |
| updated\_at | TIMESTAMP | Updated time                   |

---

## 💬 Chat UI Layout

Each message is rendered using **Bootstrap 4 media cards**, styled based on sender:

* **Left-aligned** for other users
* **Right-aligned** for the logged-in user
* Includes:

  * Username and timestamp
  * Message text
  * File preview or download link (if shared)

Example:

```blade
<div class="media mb-3 {{ $msg->user_id === auth()->id() ? 'justify-content-end text-right' : '' }}">
    @if ($msg->user_id !== auth()->id())
        <img src="/images/default-avatar.png" class="mr-2 rounded-circle" alt="Avatar" width="40">
    @endif

    <div class="media-body p-2 bg-light rounded" style="max-width: 75%;">
        <h6 class="mt-0">{{ $msg->user->username }} <small class="text-muted">{{ $msg->created_at->format('H:i') }}</small></h6>
        <p>{{ $msg->message }}</p>

        @if ($msg->file_path)
            @php
                $extension = pathinfo($msg->file_path, PATHINFO_EXTENSION);
            @endphp
            @if (in_array($extension, ['png', 'jpg', 'jpeg', 'gif']))
                <img src="{{ asset('storage/' . $msg->file_path) }}" class="img-fluid rounded" />
            @elseif($extension === 'pdf')
                <embed src="{{ asset('storage/' . $msg->file_path) }}" type="application/pdf" width="100%" height="200px" />
            @else
                <a href="{{ asset('storage/' . $msg->file_path) }}" target="_blank">📎 Download Attachment</a>
            @endif
        @endif
    </div>
</div>
```

---

## 🔄 Real-Time Updates with Laravel Echo

### Event: `NewGroupMessage`

```php
class NewGroupMessage implements ShouldBroadcast
{
    public $message;

    public function __construct($message)
    {
        $this->message = $message->load('user');
    }

    public function broadcastOn()
    {
        return new PrivateChannel('group.' . $this->message->group_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'user' => $this->message->user->username,
            'message' => $this->message->message,
            'file_url' => $this->message->file_path ? asset('storage/' . $this->message->file_path) : null,
            'created_at' => $this->message->created_at->format('H:i'),
        ];
    }
}
```

### JavaScript Listener

```js
Echo.private('group.' + groupId)
    .listen('NewGroupMessage', (e) => {
        const messageHTML = `
            <div class="media mb-3 p-2 rounded bg-light d-flex mr-auto" style="max-width: 75%;">
                <img src="/images/default-avatar.png" class="mr-2 rounded-circle" width="40">
                <div class="media-body">
                    <h6 class="mt-0">${e.user} <small class="text-muted">${e.created_at}</small></h6>
                    <p>${e.message}</p>
                    ${e.file_url ? `<a href="${e.file_url}" target="_blank">📎 Download</a>` : ''}
                </div>
            </div>`;
        document.getElementById('chat-box').insertAdjacentHTML('beforeend', messageHTML);
    });
```

---

## 📨 Sending Messages via AJAX

```js
$('#chat-form').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        url: '/groups/' + groupId + '/chat',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function() {
            $('#chat-message').val('');
            $('#chat-file').val('');
        }
    });
});
```

---

## 📂 File Upload Handling

**Controller logic:**

```php
if ($request->hasFile('file')) {
    $path = $request->file('file')->store('chat_files', 'public');
}

ChatMessage::create([
    'user_id' => auth()->id(),
    'group_id' => $groupId,
    'message' => $request->message,
    'file_path' => $path ?? null,
]);
```

---

## 🔐 Security & Access Control

* Only users who are **members of the group** can access the group chat
* Channel authorization in `routes/channels.php`:

```php
Broadcast::channel('group.{groupId}', function ($user, $groupId) {
    return $user->groups()->where('group_id', $groupId)->exists();
});
```

---

## 🧪 Testing Checklist

| Test Case                                  | Status |
| ------------------------------------------ | ------ |
| User can send text message                 | ✅      |
| User can attach file to message            | ✅      |
| File appears with correct preview/download | ✅      |
| Message sent shows instantly via Echo      | ✅      |
| Messages display aligned based on sender   | ✅      |
| Only group members can access chat         | ✅      |

---

## 📦 Future Enhancements (Optional)

* Message reactions (👍, 😂, etc.)
* Typing indicators
* Message editing or deletion
* Audio notes or screen sharing
* Pagination or lazy loading of past messages

---


# NOTIFICATION FEATURE
This section will cover a comprehensive breakdown of the notification feature that will be implemented including the tools and APIs that were utilized.

TOOLS BEING CONSIDERED
1. Laravel WebPush (via laravel-notification-channels/webpush) OR PHP web-push package with the push api (pushEvent) in browser.
    - For the laravel webpush, while setting it up, when it got to the point of generating the VAPID keys with the following command:
    ```bash
    php artisan webpush:vapid
    ```
    I was getting some errors, so, I decided to use a manual approach by visiting [VAPIDKEYS](https://vapidkeys.com/). It is a Secure VAPID Key Generator that helps you easily generate secure keys for VAPID.

    ### ERROR GOTTEN FROM THE WEBPUSH PHP PACKAGE
    As stated earlier, i could not generate the VAPID keys. The error I was getting was `RunTimeException: Unable to create the key.` | ` Unable to create local key`.

    Since I was on windows, the fix was to add the openssl.conf to my environmental variables
    
    **STEPS**

    1. Click on the START button
    1. Click on CONTROL PANEL
    1. Click on SYSTEM AND SECURITY
    1. Click on SYSTEM
    1. Click on ADVANCED SYSTEM SETTINGS
    1. Click on ENVIRONMENT VARIABLES
    1. Under "System Variables" click on "NEW"
    1. Enter the "Variable name" OPENSSL_CONF
    1. Enter the "Variable value".

    Wamp - C:\wamp\bin\apache\Apache2.2.17\conf\openssl.cnf

    Xampp - C:\xampp\apache\conf\openssl.cnf

    1. Click "OK" and close all the windows and RESTART your computer.

    The OPENSSL should be correctly working. With this, I stopped getting the aforementioned errors and I was able to send push notifications.

    [SOLUTION FROM STACKOVERFLOW](https://stackoverflow.com/questions/17272809/openssl-pkey-export-and-cannot-get-key-from-parameter-1/18869750#18869750)

2. Browser Notification API

The Notifications API allows web pages or applications to send notifications that are displayed outside the page at the system level. This enables web apps to send information to users even if the application is idle or in the background. Notifications can be used for various purposes, such as alerting users about new messages, reminders, or updates.



---

# 📣 Browser Notification Feature Documentation

## 📘 Overview

This feature enables real-time **push notifications** in the browser for authenticated users. It’s primarily used to **notify users about upcoming study sessions**. Notifications are sent using the Laravel Notification system with the [php-webpush](https://github.com/web-push-libs/web-push-php/tree/master) package and handled in the browser by a **Service Worker**.

---

## 🛠️ Technologies & Packages Used

* Laravel Notifications
* [`laravel-notification-channels/webpush`](https://github.com/laravel-notification-channels/webpush)
* VAPID keys (for push message authentication)
* Service Worker API (in `service-worker.js`)
* Browser Push APIs (via `Notification` and `PushManager`)
* JavaScript for frontend subscription logic

---

## 🔐 Prerequisites

* HTTPS domain (required for service workers & push API)
* Laravel app with authentication
* Compatible browsers (Chrome, Firefox, Edge, Safari)

---

## ⚙️ Step-by-Step Setup

### 1. Install PHP WebPush Package

```bash
composer require minishlink/web-push
```

### 3. Generate VAPID Keys

```php
use Minishlink\WebPush\VAPID;
var_dump(VAPID::createVapidKeys()); // store the keys afterwards
```

Update your `.env`:

```env
VAPID_PUBLIC_KEY=your_generated_public_key
VAPID_PRIVATE_KEY=your_generated_private_key
```

---

### 4. Configure `config/mywebpush.php`

Ensure it's configured as:

```php
'vapid' => [
    'subject' => 'mailto:admin@yourdomain.com',
    'public_key' => env('VAPID_PUBLIC_KEY'),
    'private_key' => env('VAPID_PRIVATE_KEY'),
],
```

---

### 5. Add Push Subscriptions Table

Run the migration to add `web_push_subscriptions` table:

```bash
php artisan migrate
```


---

### 7. Send Push Message
```php
 $pushSubscriptions = $user->webPushSubscriptions()->get();

    $webPush = new WebPush([
        "VAPID" => [
            'subject' => env('VAPID_SUBJECT'),
            'publicKey' => env('VAPID_PUBLIC_KEY'),
            'privateKey' => env('VAPID_PRIVATE_KEY')
        ]
    ]);

    foreach ($pushSubscriptions as $subscription) {
        $webPush->sendOneNotification(
            Subscription::create(json_decode($subscription->data, true)),
            json_encode([
                'title' => 'Test Notification',
                'body' => 'This is a test notification',
                'icon' => '/logo-white.jpg',
                'url' => '/'
            ])
        );
    }

```

---

### 8. Register Service Worker

In `public/service-worker.js`:

```js
self.addEventListener('push', function (event) {
  let data = {};
  try {
    data = event.data ? event.data.json() : {};
  } catch (e) {
    console.error('Invalid push payload:', e);
  }

  event.waitUntil(
    self.registration.showNotification(data.title, {
      body: data.body,
      icon: '/logo-white.jpg',
      data: { url: data.url }
    })
  );
});

self.addEventListener('notificationclick', function (event) {
  event.notification.close();
  event.waitUntil(
    clients.openWindow(event.notification.data.url)
  );
});
```

---

### 9. Add Subscription Logic to Frontend

In a Blade template or JS file:

```html
<script>
     $(document).ready(function() {
            navigator.serviceWorker.register("{{ asset('service-worker.js') }}");

            function urlBase64ToUint8Array(base64String) {
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');

                const rawData = window.atob(base64);
                const outputArray = new Uint8Array(rawData.length);

                for (var i = 0; i < rawData.length; ++i) {
                    outputArray[i]=rawData.charCodeAt(i);
                }

                return outputArray;
         }

            function askForPermission()
            {
                Notification.requestPermission().then((permission) => {
                    if(permission == 'granted') {
                        navigator.serviceWorker.ready.then((registration) => {
                            registration.pushManager.subscribe({
                                userVisibleOnly: true,
                                applicationServerKey: urlBase64ToUint8Array("{{ config('mywebpush.vapid.public_key') }}")
                                }).then((subscription) => {
                                    console.log(subscription);
                                    // save subscription on the server
                                    $.ajax({
                                        url: "{{ route('user.push-subscription') }}",
                                        method: 'POST',
                                        data: {
                                            'subscription': JSON.stringify(subscription)
                                        },
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function(response) {
                                            console.log(response);
                                        },
                                        error: function(error) {
                                            console.log(error);
                                        }
                                    });
                                });
                        })
                    }
                });
            }

            if (Notification.permission !== 'granted') {
                $('#notification-modal').modal('show')
                // Handle allow button click
                $('#allow-notifications').on('click', function() {
                    // Request permission for notifications
                    askForPermission();
                    // Close the modal
                    $('#notification-modal').modal('hide');
                });

                // Handle reject button click
                $('#reject-notifications').on('click', function() {
                    // Close the modal
                    $('#notification-modal').modal('hide');
                });
            }
        })
</script>
```

---

### 10. Create a Route and Save Subscriptions

```php
public function store(Request $request)
    {
        // $request->validate([
        //     'endpoint' => 'required',
        // ]);
        // $request->user()->updatePushSubscription(endpoint: $request->endpoint, contentEncoding: 'aes128gcm');

        WebPushSubscription::create([
            'user_id' => $request->user()->id,
            'data' => $request->subscription,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Push Notification Subscription Saved']);
    }
```

---

## 🧪 Testing Notifications

Send a manual notification via route or tinker:

```php

```

Or dispatch in a job/command:

```php

```

---

## 🛠️ Debugging Tips

| Issue                                             | Fix                                                                                                                |
| ------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------ |
| `Cannot read properties of null (reading 'json')` | Ensure the payload is not empty, use `try/catch` in `service-worker.js`                                            |
| No notification shown                             | Check if user has a valid push subscription in DB                                                                  |
| Payload not delivered                             | Ensure VAPID keys are valid, and the `data()` in `toWebPush()` is plain JSON (no models/objects) |
| Notification URL doesn't open                     | Make sure `event.notification.data.url` is correctly set and accessible                                            |

---

## 🔒 Security Notes

* Always verify user identity before saving push subscriptions
* Never pass sensitive data in the payload
* Use HTTPS in production (push won’t work otherwise)

---

## ✅ Summary

| Task                       | Status |
| -------------------------- | ------ |
| Generate VAPID Keys        | ✅      |
| Configure Service Worker   | ✅      |
| Save Subscriptions         | ✅      |
| Trigger Notifications      | ✅      |
| Handle Notification Clicks | ✅      |
| Debug Payload Handling     | ✅      |

---

# Scheduling Tasks using Laravel Task Scheduling Feature
Laravel's command scheduler offers a fresh approach to managing scheduled tasks on your server. The scheduler allows you to fluently and expressively define your command schedule within your Laravel application itself. When using the scheduler, only a single cron entry is needed on your server.

___
We want the application to send a notification when Study session is about to start in X minutes. Let's say in 15 minutes.

To achieve this, a schedule will be set up to run maybe every minute or daily. The job of the schedule will be to run a custom artisan command: `SendUpcomingSessionReminders` that sends a notification to the all the participants of a study sessions.

### TYPES OF NOTIFICATION SENT
- Email notification
- Database notification (in-app notification)
- Push notification (Browser-specific)

In Laravel, scheduled tasks are defined in the routes/console file.

# OTHER FEATURES
Users should also receive notifications whenever a study session is created in a group they belong to. ✅

Here's a complete documentation on **"Step-by-Step Queuing for Push Notifications in Laravel"** — tailored to your app setup with **Web Push**, **notification fallbacks**, and **job dispatching**.

---

# 📄 Step-by-Step Queuing for Push Notifications in Laravel

This guide walks you through implementing **queued push notifications** using Laravel Jobs and adding **fallback notification logic** for cases where Web Push is unavailable.

---

## 📌 Use Case

When a user joins a study session, notify all other participants using:

1. Web Push notifications (primary channel)
2. Laravel notifications (fallback: email + in-app)

---

## 🔧 Prerequisites

* Laravel Queues configured (database/redis)
* [Minishlink/WebPush](https://github.com/web-push-libs/web-push-php) library installed
* A `web_push_subscriptions` table and model
* Users have a `webPushSubscriptions()` relationship
* A `StudySession` model with a `participants` JSON field (user IDs)

---

## ✅ Step 1: Create the Notification Job

```bash
php artisan make:job SendSessionJoinPushNotification
```

### `app/Jobs/SendSessionJoinPushNotification.php`

```php
use App\Models\User;
use App\Models\StudySession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class SendSessionJoinPushNotification implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public int $userId,
        public int $sessionId,
        public array $participantIds
    ) {}

    public function handle(): void
    {
        $user = User::find($this->userId);
        $session = StudySession::find($this->sessionId);
        $participants = User::with('webPushSubscriptions')->findMany($this->participantIds);
        $webPush = new WebPush(fetch_vapid_credentials());

        foreach ($participants as $participant) {
            $notified = false;

            foreach ($participant->webPushSubscriptions as $subscription) {
                try {
                    $webPush->sendOneNotification(
                        Subscription::create(json_decode($subscription->data, true)),
                        json_encode([
                            'title' => 'New Participant',
                            'body' => "{$user->username} has joined the study session: {$session->session_title}",
                            'icon' => '/logo-white.jpg',
                            'url' => route('user.study-groups.sessions.show', [$session->study_group_id, $session->id]),
                        ])
                    );
                    $notified = true;
                } catch (\Exception $e) {
                    \Log::warning("WebPush failed for user {$participant->id}: " . $e->getMessage());
                }
            }

            if (!$notified || $participant->webPushSubscriptions->isEmpty()) {
                $participant->notify(new \App\Notifications\GenericNotification(
                    'New Participant',
                    "{$user->username} has joined the study session: {$session->session_title}",
                    route('user.study-groups.sessions.show', [$session->study_group_id, $session->id])
                ));
            }
        }

        $webPush->flush();
    }
}
```

---

## ✅ Step 2: Create a Fallback Notification

```bash
php artisan make:notification SendSessionJoinFallbackNotification
```

### `app/Notifications/SendSessionJoinFallbackNotification.php`

```php
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class GenericNotification extends Notification
{
    public function __construct(public $title, public $body, public $url) {}

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->title)
            ->line($this->body)
            ->action('View Session', $this->url);
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
        ];
    }
}
```

---

## ✅ Step 3: Dispatch the Job on Participation Toggle

In your controller method (e.g. `toggleParticipation()`):

```php
use App\Jobs\SendSessionJoinPushNotification;

public function toggleParticipation($group, $sessionId)
{
    $session = StudySession::findOrFail($sessionId);
    $userId = request()->user()->id;
    $participants = $session->participants ?? [];

    if (in_array($userId, $participants)) {
        $participants = array_values(array_filter($participants, fn($id) => $id != $userId));
        $message = 'You have left the study session';
    } else {
        $participants[] = $userId;
        $message = 'You have joined the study session';

        // Queue the job (notify others)
        $otherParticipants = array_filter($session->participants, fn($id) => $id != $userId);
        SendSessionJoinPushNotification::dispatch($userId, $session->id, $otherParticipants);
    }

    $session->participants = $participants;
    $session->save();

    return back()->with('success', $message);
}
```

---

## ✅ Step 4: Configure the Queue

Use database queue for shared hosting (if Redis isn’t available).

### `.env`

```env
QUEUE_CONNECTION=database
```

### Create Queue Tables

```bash
php artisan queue:table
php artisan migrate
```

---

## ✅ Step 5: Run the Queue Worker

For shared hosting:

* Use a daemon runner if allowed (e.g., `php artisan queue:work`)
* Or set up a fallback route that triggers `Artisan::call('queue:work --once')` manually (as a last resort)
* Or offload queue processing to a background API or external queue processor (e.g., Laravel Forge, Cloud Tasks)

---

## ✅ Optional: Add Web UI for Fallback Notifications

In your Blade template:

```blade
@foreach(auth()->user()->notifications as $notification)
    <div class="alert alert-info">
        <strong>{{ $notification->data['title'] }}</strong><br>
        {{ $notification->data['body'] }}<br>
        <a href="{{ $notification->data['url'] }}">View</a>
    </div>
@endforeach
```

---

## ✅ Conclusion

You’ve now implemented a **fully queued** and **resilient** push notification system:

* Queues prevent delays in the main request
* Web Push is primary
* Fallback ensures delivery via email/database

