<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('app.name')}}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .hero {
            /* background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1500&q=80') center center no-repeat; */
            background: linear-gradient(to right, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8)), url('/students.jpg') center center no-repeat;
            background-size: cover;
            padding: 80px 0;
            color: #fff;
            height: 70vh;
            display: flex;
            flex-direction: column;
            justify-content: center;

            /* .container {
                height: 100%;

            } */
        }

        .feature-icon {
            font-size: 2.5rem;
            color: #007bff;
        }

        .testimonials {
            background: #f1f1f1;
            padding: 60px 0;
        }

        .faq .card-header {
            background: #fff;
        }

        footer {
            background: #343a40;
            color: #fff;
            padding: 30px 0;
        }
    </style>
</head>

<body>
    <!-- Hero Section -->
    <section class="hero text-center">
        <div class="container">
            {{-- <img src="{{ asset('/logo.png') }}" alt="Logo" class="img-fluid"> --}}
            <h1 class="display-3"> {{config('app.name')}}</h1>
            <p class="lead">Boost student engagement with shared calendars, conflict-free sessions, and real-time
                discussions.</p>
            @auth
            <a href="{{ route('user.dashboard') }}" class="btn btn-primary btn-lg mt-3">Go to Dashboard</a>
            @else
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg mt-3">Get Started</a>
            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg mt-3">Login</a>
            @endauth
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Key Features</h2>
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="feature-icon mb-3">📅</div>
                    <h5>Shared Calendar</h5>
                    <p>Coordinate group study sessions with a unified schedule for all participants.</p>
                </div>
                <div class="col-md-4">
                    <div class="feature-icon mb-3">⚠️</div>
                    <h5>Conflict Detection</h5>
                    <p>Smart alerts help avoid timing conflicts among users in real-time.</p>
                </div>
                <div class="col-md-4">
                    <div class="feature-icon mb-3">💬</div>
                    <h5>Built-in Chat</h5>
                    <p>Collaborate and communicate instantly through group discussion rooms.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Video -->
    <section class="text-center py-5 bg-light">
        <div class="container">
            <h2 class="mb-4">See It In Action</h2>
            <div class="embed-responsive embed-responsive-16by9 mb-4">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/dQw4w9WgXcQ"
                    allowfullscreen></iframe>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials text-center">
        <div class="container">
            <h2 class="mb-5">What Students Say</h2>
            <div class="row">
                <div class="col-md-4 shadow">
                    <blockquote class="blockquote">
                        <p class="mb-0">"A game changer for our group study routines!"</p>
                        <footer class="blockquote-footer text-white">Ada, 300L CSC</footer>
                    </blockquote>
                </div>
                <div class="col-md-4 shadow">
                    <blockquote class="blockquote">
                        <p class="mb-0">"No more clashing schedules. Love it!"</p>
                        <footer class="blockquote-footer text-white">Jide, 200L SEN</footer>
                    </blockquote>
                </div>
                <div class="col-md-4 shadow">
                    <blockquote class="blockquote">
                        <p class="mb-0">"The chat and notifications are 🔥"</p>
                        <footer class="blockquote-footer text-white">Fatima, 100L CYB</footer>
                    </blockquote>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="faq py-5">
        <div class="container">
            <h2 class="text-center mb-4">Frequently Asked Questions</h2>
            <div id="accordion">
                <div class="card">
                    <div class="card-header" id="faq1">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#collapse1">
                                Is the planner free to use?
                            </button>
                        </h5>
                    </div>
                    <div id="collapse1" class="collapse" data-parent="#accordion">
                        <div class="card-body">
                            Yes, it's free for all students registered on the platform.
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="faq2">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#collapse2">
                                Can I create study sessions?
                            </button>
                        </h5>
                    </div>
                    <div id="collapse2" class="collapse" data-parent="#accordion">
                        <div class="card-body">
                            Absolutely. You can study sessions that other study group members can join.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form -->
    <section class="py-5 bg-light" id="contact">
        <div class="container">
            <h2 class="text-center mb-4">Get in Touch</h2>
            <form method="POST" action="#">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control" placeholder="Full Name">
                    </div>
                    <div class="form-group col-md-6">
                        <input type="email" class="form-control" placeholder="Email">
                    </div>
                </div>
                <div class="form-group">
                    <textarea class="form-control" rows="4" placeholder="Your Message"></textarea>
                </div>
                <div class="text-center">
                    <button class="btn btn-primary" type="submit">Send Message</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <p>&copy; {{ date('Y') }} {{config('app.name')}}. All rights reserved.</p>
        </div>
    </footer>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>