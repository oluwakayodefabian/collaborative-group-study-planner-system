<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - {{ $title ?? 'Login' }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets') }}/media/image/favicon.png" />

    <!-- Plugin styles -->
    <link rel="stylesheet" href="{{ asset('vendors/bundle.css') }}" type="text/css">

    <!-- App styles -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/app.min.css" type="text/css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="form-membership" style="background: url({{ asset('assets') }}/media/image/image1.jpg)">
    <!-- begin::preloader-->
    <div class="preloader">
        <div class="preloader-icon"></div>
    </div>
    <!-- end::preloader -->

    @yield('auth-content')

    <!-- Plugin scripts -->
    <script src="{{ asset('vendors/bundle.js') }}"></script>

    <!-- App scripts -->
    <script src="{{ asset('assets') }}/js/app.min.js"></script>

    @include("toastr_message")
    {{-- @include("sweetalert_message") --}}

    <script>
        // console.info(Notification.permission);
        // if (Notification.permission !== 'granted') {
        //     Notification.requestPermission().then((result) => {
        //             console.log(result);
        //             });
        // }

        // const greeting = new Notification('Hi, How are you?', {
        //     body: 'Hello World',
        //     icon: '{{ asset('assets') }}/media/image/favicon.png',
        //     vibrate: true
        // });
    </script>
</body>

</html>