<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<!-- Mirrored from filedash.laborasyon.com/demos/default/ by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 04 Apr 2025 15:42:33 GMT -->

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - {{ $title }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets') }}/media/image/favicon.png" />

    <!-- Main css -->
    <link rel="stylesheet" href="{{ asset('vendors') }}/bundle.css" type="text/css">

    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;700&amp;display=swap" rel="stylesheet">


    <!-- App css -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/app.min.css" type="text/css">

    <link rel="stylesheet" href="{{ asset('vendors') }}/dataTable/datatables.min.css" type="text/css">

    {{-- font-awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Themify icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

    <!-- resources/views/calendar.blade.php -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    @if (isset($chatPage) && $chatPage)
    @vite('resources/js/app.js')
    @endif
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="preloader-icon"></div>
    </div>
    <!-- ./ Preloader -->

    <!-- The modal -->
    <div class="modal fade" id="notification-modal" tabindex="-1" role="dialog"
        aria-labelledby="notification-modal-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notification-modal-label">Allow Notifications</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Allow notifications from {{ config('app.name') }}?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="allow-notifications">Allow</button>
                    <button type="button" class="btn btn-secondary" id="reject-notifications">Reject</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Layout wrapper -->
    <div class="layout-wrapper">
        <!-- Header -->
        @include('partials.header')
        <!-- ./ Header -->

        <!-- Content wrapper -->
        <div class="content-wrapper">
            <!-- begin::navigation -->
            @include('partials.sidebar')
            <!-- end::navigation -->

            <!-- Content body -->
            <div class="content-body">
                <!-- Content -->
                @yield('content')
                <!-- ./ Content -->

                <!-- Footer -->
                <footer class="content-footer d-print-none">
                    <div>© {{ date('Y') }} {{ config('app.name') }}</div>
                </footer>
                <!-- ./ Footer -->
            </div>
            <!-- ./ Content body -->

            <!-- Sidebar group -->
            <div class="sidebar-group d-print-none">
                <!-- Sidebar - Storage -->
                <div class="sidebar primary-sidebar show" id="storage">
                    <div class="sidebar-header flex-column">
                        <img src="{{ asset('/logo.png') }}" alt="logo" class="img-fluid">
                        <h4 class="text-center">{{ config('app.name') }}</h4>
                    </div>
                    <div class="sidebar-content">

                    </div>
                    <div class="sidebar-footer">
                        <a href="{{route('user.study-groups.index')}}" class="btn btn-lg btn-block btn-outline-primary">
                            <i class="fa fa-book mr-3"></i> View All Study Groups
                        </a>
                    </div>
                </div>
                <!-- ./ Sidebar - Storage -->


                <!-- Sidebar - Settings -->
                <div class="sidebar" id="settings">
                    <div class="sidebar-header">
                        <h4>Settings</h4>
                        <a href="#" class="btn btn-light btn-floating sidebar-close-btn">
                            <i class="ti-angle-right"></i>
                        </a>
                    </div>
                    <div class="sidebar-content">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item pl-0 pr-0">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="customSwitch1" checked>
                                    <label class="custom-control-label" for="customSwitch1">Allow notifications.</label>
                                </div>
                            </li>
                            <li class="list-group-item pl-0 pr-0">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="customSwitch2">
                                    <label class="custom-control-label" for="customSwitch2">Hide user requests</label>
                                </div>
                            </li>
                            <li class="list-group-item pl-0 pr-0">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="customSwitch3" checked>
                                    <label class="custom-control-label" for="customSwitch3">Speed up demands</label>
                                </div>
                            </li>
                            <li class="list-group-item pl-0 pr-0">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="customSwitch4" checked>
                                    <label class="custom-control-label" for="customSwitch4">Hide menus</label>
                                </div>
                            </li>
                            <li class="list-group-item pl-0 pr-0">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="customSwitch5">
                                    <label class="custom-control-label" for="customSwitch5">Remember next visits</label>
                                </div>
                            </li>
                            <li class="list-group-item pl-0 pr-0">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="customSwitch6">
                                    <label class="custom-control-label" for="customSwitch6">Enable report
                                        generation.</label>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- ./ Sidebar - Settings -->
            </div>
            <!-- ./ Sidebar group -->
        </div>
        <!-- ./ Content wrapper -->
    </div>
    <!-- ./ Layout wrapper -->

    {{-- Sound --}}
    <audio id="chatNotificationSound" src="{{ asset('sounds/message-pop-alert.mp3') }}" preload="auto"></audio>

    <!-- Main scripts -->
    <script src="{{ asset('vendors') }}/bundle.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.4/raphael-min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/justgage/1.2.9/justgage.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'>
    </script>
    <!-- Apex chart -->
    <script src="https://apexcharts.com/samples/assets/irregular-data-series.js"></script>
    <script src="{{ asset('vendors') }}/charts/apex/apexcharts.min.js"></script>

    <!-- Dashboard scripts -->
    <script src="{{ asset('assets') }}/js/examples/pages/file-dashboard.js"></script>

    <!-- App scripts -->
    <script src="{{ asset('assets') }}/js/app.min.js"></script>
    <!-- Datatable -->
    <script src="{{ asset('vendors') }}/dataTable/datatables.min.js"></script>
    <!-- Users page examples -->
    <script src="{{ asset('assets') }}/js/examples/pages/user.js"></script>
    <script src="{{ asset('assets') }}/js/examples/datatable.js"></script>
    {{-- <script src="{{ asset('assets') }}/js/examples/charts/justgage.js"></script> --}}

    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

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

    @if(isset($uploadsWeekly) && isset($uploadsMonthly))
    <script>
        const weeklyData = {
            labels: {!! json_encode($uploadsWeekly->pluck('label')) !!},
            datasets: [{
                label: 'Weekly Uploads',
                data: {!! json_encode($uploadsWeekly->pluck('count')) !!},
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.3,
                fill: true
            }]
        };

        const monthlyData = {
            labels: {!! json_encode($uploadsMonthly->pluck('label')) !!},
            datasets: [{
                label: 'Monthly Uploads',
                data: {!! json_encode($uploadsMonthly->pluck('count')) !!},
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.3,
                fill: true
            }]
        };

        const ctx = document.getElementById('uploadChart').getContext('2d');
        let uploadChart = new Chart(ctx, {
            type: 'line',
            data: weeklyData,
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        function showChart(mode) {
            const newData = mode === 'monthly' ? monthlyData : weeklyData;
            uploadChart.data = newData;
            uploadChart.update();
        }
    </script>
    @endif

    @include("toastr_message")

    @yield('scripts')

    @stack('scripts')

</body>

</html>