# 🏗️ SYSTEM ARCHITECTURE OVERVIEW

### 🔧 Technologies Used

* **Frontend**: HTML, Bootstrap CSS, jQuery (JavaScript)
* **Backend**: Laravel (PHP Framework)
* **Database**: MySQL (or any Laravel-supported SQL DB)
* **Real-time Notifications & Chat**: Pusher or Laravel Echo + Redis (optional for real-time)

---

## 📦 MODULE BREAKDOWN & FLOW

---

### 1. **User Management**

* **Model**: `User`
* **Key Fields**: `id`, `username`, `email`, `password`, `role`, `created_at`, `updated_at`
* **Features**:

  * Registration with unique username
  * Login/Logout
  * Session-based authentication or Laravel Sanctum if API-based

---

### 2. **Study Group / Study Session Management**

* **Models**: `StudyGroup`, `GroupMembership`, `StudySession`
* **Relationships**:

  * `StudyGroup` has many `StudySessions`
  * `StudyGroup` has many `GroupMemberships`
  * `GroupMembership` belongs to `User` and `StudyGroup`

#### 🗂️ Table: `study_groups`

| Field       | Type               |
| ----------- | ------------------ |
| id          | BIGINT             |
| name        | STRING             |
| creator\_id | FOREIGN KEY (User) |
| description | TEXT               |
| created\_at | TIMESTAMP          |

#### 🗂️ Table: `group_memberships`

| Field      | Type                     |
| ---------- | ------------------------ |
| id         | BIGINT                   |
| group\_id  | FOREIGN KEY (StudyGroup) |
| user\_id   | FOREIGN KEY (User)       |
| joined\_at | TIMESTAMP                |

#### 🗂️ Table: `study_sessions`

| Field          | Type        |
| -------------- | ----------- |
| id             | BIGINT      |
| group\_id      | FOREIGN KEY |
| session\_title | STRING      |
| start\_time    | DATETIME    |
| end\_time      | DATETIME    |
| location       | STRING      |
| created\_at    | TIMESTAMP   |

---

### 3. **File Upload & Study Library**

* **Model**: `StudyFile`
* **Features**:

  * Users upload and manage (delete) files
  * Files are associated with study groups (optional)

#### 🗂️ Table: `study_files`

| Field        | Type                   |
| ------------ | ---------------------- |
| id           | BIGINT                 |
| user\_id     | FOREIGN KEY            |
| group\_id    | FOREIGN KEY (nullable) |
| file\_name   | STRING                 |
| file\_path   | STRING                 |
| uploaded\_at | TIMESTAMP              |

> Storage: Use Laravel's `Storage` system (store in `storage/app/public`) and use symbolic link (`php artisan storage:link`)

---

### 4. **Shared Calendar & Real-Time Notification**

* **Model**: `CalendarEvent` (or reuse `StudySession`)
* **Features**:

  * Calendar view using FullCalendar.js or similar
  * Real-time updates using Pusher or Laravel Echo
  * Notify group members before session starts or when a session is added/modified

---

### 5. **Conflict Resolution Algorithm**

* Check for overlapping `StudySessions` for a user across different `group_memberships`
* Suggest alternative times when conflict is found

#### Example logic:

```php
$overlaps = StudySession::where('start_time', '<', $newSessionEnd)
    ->where('end_time', '>', $newSessionStart)
    ->whereHas('group.members', fn($q) => $q->where('user_id', $userId))
    ->exists();
```

---

### 6. **Group Chatroom**

* **Models**: `GroupMessage`
* **Features**:

  * Chat within study groups
  * Real-time updates with Pusher
  * File sharing in chat (optional)

#### 🗂️ Table: `group_messages`

| Field       | Type        |
| ----------- | ----------- |
| id          | BIGINT      |
| group\_id   | FOREIGN KEY |
| user\_id    | FOREIGN KEY |
| message     | TEXT        |
| has\_file   | BOOLEAN     |
| created\_at | TIMESTAMP   |

---

## 🧭 FRONTEND FLOW (Simplified)

1. **User registers/login**
2. **Dashboard**: Create or join study groups
3. **Group View**:

   * See members
   * Schedule or view sessions (with calendar)
   * Upload study materials
   * Chat with group members

---

## ✅ Security & Best Practices

* Passwords: Hashed with Laravel's bcrypt
* Access control: Policies to ensure users access only their groups/files
* File validation: Only allow certain file types and sizes
* Notifications: Use Laravel Notifications for email/real-time alerts

---

Certainly! To complement your project, I'll provide both a **visual architecture diagram** and a **database Entity-Relationship Diagram (ERD)**. These will help you visualize the system's structure and data relationships.

---

## 🧭 System Architecture Diagram

Here's a high-level overview of your system's architecture:

```
+-------------------+       +-------------------+
|                   |       |                   |
|     Frontend      |       |     Backend       |
|  (HTML, Bootstrap,|       |   (Laravel PHP)   |
|   jQuery)         |       |                   |
+-------------------+       +-------------------+
          |                           |
          |                           |
          v                           v
+-------------------+       +-------------------+
|                   |       |                   |
|  Authentication   |<----->|   User Controller |
|                   |       |                   |
+-------------------+       +-------------------+
          |                           |
          v                           v
+-------------------+       +-------------------+
|                   |       |                   |
|   Study Groups    |<----->| StudyGroup Ctrl   |
|                   |       |                   |
+-------------------+       +-------------------+
          |                           |
          v                           v
+-------------------+       +-------------------+
|                   |       |                   |
|  Study Sessions   |<----->| StudySession Ctrl |
|                   |       |                   |
+-------------------+       +-------------------+
          |                           |
          v                           v
+-------------------+       +-------------------+
|                   |       |                   |
|   File Uploads    |<----->|  FileUpload Ctrl  |
|                   |       |                   |
+-------------------+       +-------------------+
          |                           |
          v                           v
+-------------------+       +-------------------+
|                   |       |                   |
|  Shared Calendar  |<----->| CalendarEvent Ctrl|
|                   |       |                   |
+-------------------+       +-------------------+
          |                           |
          v                           v
+-------------------+       +-------------------+
|                   |       |                   |
|   Chatroom        |<----->| ChatMessage Ctrl  |
|                   |       |                   |
+-------------------+       +-------------------+
          |                           |
          v                           v
+-------------------+       +-------------------+
|                   |       |                   |
|   Notifications   |<----->| Notification Ctrl |
|                   |       |                   |
+-------------------+       +-------------------+
```



**Explanation:**

* **Frontend**: Built using HTML, Bootstrap, and jQuery, it interacts with the backend through HTTP requests.
* **Backend**: Laravel handles routing, controllers, and business logic.
* **Controllers**: Each major feature has its own controller to manage requests and responses.
* **Models**: Represent the data structure and interact with the database.
* **Database**: Stores all persistent data, including users, study groups, sessions, files, calendar events, chat messages, and notifications.

---

## 🗃️ Entity-Relationship Diagram (ERD)

The ERD illustrates how different entities in your system relate to each other:

```
[User] 1---n [GroupMembership] n---1 [StudyGroup] 1---n [StudySession]
   |                                         |
   |                                         |
   |                                         |
   |                                         |
   |                                         v
   |                                     [StudyFile]
   |
   v
[ChatMessage]
```

**Entities and Relationships:**

* **User**:

  * Can be a member of multiple study groups through `GroupMembership`.
  * Can upload multiple `StudyFile`s.
  * Can send multiple `ChatMessage`s.
* **StudyGroup**:

  * Has multiple `GroupMembership`s.
  * Can have multiple `StudySession`s.
* **StudySession**:

  * Belongs to a `StudyGroup`.
  * Can have multiple `StudyFile`s associated.
* **StudyFile**:

  * Uploaded by a `User`.
  * Associated with a `StudySession`.
* **ChatMessage**:

  * Sent by a `User`.
  * Associated with a `StudyGroup`.([https://miro.com/][1], [Wikipedia][2], [Stack Overflow][3])

**Note:** This is a simplified representation. For a detailed ERD, consider using tools like [Lucidchart](https://www.lucidchart.com/), [Draw.io](https://app.diagrams.net/), or [MySQL Workbench](https://www.mysql.com/products/workbench/).

---

[1]: https://miro.com/diagramming/software-architecture-diagram/?utm_source=chatgpt.com "Architecture Diagram Tool Built for Collaboration - Miro"
[2]: https://en.wikipedia.org/wiki/List_of_concept-_and_mind-mapping_software?utm_source=chatgpt.com "List of concept- and mind-mapping software"
[3]: https://stackoverflow.com/questions/55022291/laravel-diagram-generator?utm_source=chatgpt.com "Laravel Diagram Generator - Stack Overflow"

