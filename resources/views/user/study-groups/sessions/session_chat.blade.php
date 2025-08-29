@extends('layouts.user_layout')

@section('content')
<div class="content">
    <div class="page-header d-flex justify-content-between">
        <h2 class="mb-4">Study Session Chat Room</h2>
    </div>

    <div class="container">
        <h4>Chatroom - {{ $studySession->session_title }}</h4>

        <div class="card">
            <div class="card-body" id="chat-box" style="height: 400px; overflow-y: auto; background: #f9f9f9;">
                @foreach ($messages as $msg)
                @php
                $isMe = $msg->user_id === Auth::user()->id;
                $alignClass = $isMe ? 'ml-auto text-right' : 'mr-auto';
                $bgClass = $isMe ? 'bg-primary text-white' : 'bg-light';
                $ext = $msg->file_path ? strtolower(pathinfo($msg->file_path, PATHINFO_EXTENSION)) : null;
                $url = $msg->file_path ? asset('storage/' . $msg->file_path) : null;
                @endphp

                <div class="media mb-3 p-2 rounded {{ $bgClass }} d-flex {{ $alignClass }}" style="max-width: 75%;">
                    @if (!$isMe)
                    <img src="{{ asset('assets/media/image/user/user_avatar.jpeg') }}" class="mr-2 rounded-circle"
                        alt="Avatar" width="40" height="40">
                    @endif

                    <div class="media-body">
                        <h6 class="mt-0 mb-1">
                            {{ $msg->user->username }}
                            <small class="{{ $isMe ? 'text-white' : 'text-muted' }}">{{ $msg->created_at->format('H:i')
                                }}</small>
                        </h6>

                        @if ($msg->message)
                        <p class="mb-1">{{ $msg->message }}</p>
                        @endif

                        @if ($msg->file_path)
                        @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                        <img src="{{ $url }}" alt="Shared image" class="img-fluid rounded" style="max-height: 200px;">
                        @elseif ($ext === 'pdf')
                        <embed src="{{ $url }}" type="application/pdf" width="100%" height="300px" class="mt-2" />
                        @else
                        <a href="{{ $url }}" target="_blank" class="d-block mt-2 text-white">📎 Download {{
                            basename($msg->file_path) }}</a>
                        @endif
                        @endif
                    </div>

                    @if ($isMe)
                    <img src="{{ asset('assets/media/image/user/user_avatar.jpeg') }}" class="ml-2 rounded-circle"
                        alt="Avatar" width="40" height="40">
                    @endif
                </div>
                @endforeach
            </div>

            <div class="card-footer">
                <form id="chat-form">
                    @csrf
                    <div class="input-group mb-2">
                        <input type="text" name="message" class="form-control" placeholder="Type a message...">
                        <input type="file" name="file" class="form-control ml-2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">Send</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@auth
@push('scripts')
<script>
    $(function() {
        let chatBox         = $('#chat-box');
        let studySession    = "{{ $studySession->id }}";
        let USER_ID         =  "{{ Auth::user()->id }}";

        function scrollToBottom() {
            chatBox.scrollTop($('#chat-box')[0].scrollHeight);
        }

        function displayMessage(data) {
            let isMe        = data.sender_id == USER_ID;
            let alignClass  = isMe ? 'ml-auto text-right' : 'mr-auto';
            let bgClass     = isMe ? 'bg-primary text-white' : 'bg-light';
            let preview     = '';
            if (data.file_url) {
                const ext       = data.file_name.split('.').pop().toLowerCase();
                const isImage   = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
                const isPDF     = ext === 'pdf';

                if (isImage) {
                    preview = `<br><img src="${data.file_url}" alt="Shared image" class="img-fluid rounded mt-1"
                    style="max-height: 200px;">`;
                } else if (isPDF) {
                    preview = `<br><embed src="${data.file_url}" type="application/pdf" width="100%" height="300px" class="mt-1" />`;
                } else {
                    preview = `<br><a href="${data.file_url}" target="_blank" class="d-block mt-1">📎 Download ${data.file_name}</a>`;
                }
            }

            let chat = `
            <div class="media mb-3 p-2 rounded ${bgClass} d-flex ${alignClass}" style="background-color: max-width: 75%;">`;

                if (!isMe) {
                    chat += `
                    <img src="{{ asset('assets/media/image/user/user_avatar.jpeg') }}" class="mr-2 rounded-circle" alt="Avatar"
                        width="40" height="40">
                    `;
                }

                chat += `<div class="media-body">
                    <h6 class="mt-0 mb-1">
                        ${data.username}
                        <small class="${ isMe ? 'text-white' : 'text-muted' }">${data.timestamp}</small>
                    </h6>
                    <p class="mb-1">${data.message || ''}${preview}</p>
                </div>`;

                if (isMe) {
                    chat += `
                    <img src="{{ asset('assets/media/image/user/user_avatar.jpeg') }}" class="ml-2 rounded-circle" alt="Avatar"
                        width="40" height="40">
                    `;
                }

                chat +=`
            </div>`;

            chatBox.append(chat);
            scrollToBottom();
        }

        let subscribedChannel = null; // Track the currently subscribed channel
        // Dynamically subscribe to the study group channel
        if (subscribedChannel) {
            subscribedChannel.unsubscribe(); // Unsubscribe from the previous channel
        }

        subscribedChannel = window.Echo.channel(`session.${studySession}`);
        subscribedChannel.listen("NewSessionMessageEvent", (event) => {
         console.log("New message received:", event);

            //Append the message dynamically
            displayMessage(event);
            if (event.sender_id != USER_ID) {
            const audio = document.getElementById('chatNotificationSound');
            if (audio) audio.play();
            }
        });


        $('#chat-form').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: "{{ route('user.study-sessions.chat.send', $studySession) }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    displayMessage(data);
                    $('#chat-form')[0].reset();
                },
                error: function() {
                    alert('Error sending message');
                }
            });
        });

        scrollToBottom();
    });
</script>
@endpush
@endauth