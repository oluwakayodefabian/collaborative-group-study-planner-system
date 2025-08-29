<div class="header d-print-none">
    <div class="header-container">
        <div class="header-body">
            <div class="header-body-left">
                <ul class="navbar-nav">
                    <li class="nav-item navigation-toggler">
                        <a href="#" class="nav-link">
                            <i class="ti-menu"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        {{-- <div class="header-search-form"> --}}
                            <div style="background-color: #ccc; border-radius: 3px;">
                                {{-- @include('partials.search_form') --}}
                                <form method="GET" action="">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <button class="btn">
                                                <i class="ti-search"></i>
                                            </button>
                                        </div>
                                        <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                                            placeholder="Search by filename...">
                                        {{-- <div class="input-group-append">
                                            <button class="btn header-search-close-btn">
                                                <i data-feather="x"></i>
                                            </button>
                                        </div> --}}
                                    </div>
                                </form>

                            </div>
                    </li>
                </ul>
            </div>

            <div class="header-body-right">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="#" class="nav-link mobile-header-search-btn" title="Search">
                            <i class="ti-search"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" title="Dark">
                            <i class="fa fa-moon-o"></i>
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a href="#"
                            class="nav-link {{ Auth::user()->unreadNotifications->count() > 0 ? 'nav-link-notify' : '' }}"
                            title="Notifications" data-toggle="dropdown">
                            <i class="ti-bell"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-big">
                            <div class="bg-primary px-3 py-3">
                                <h6 class="mb-0">Notifications</h6>
                            </div>
                            <div class="dropdown-scroll">
                                <ul class="list-group list-group-flush">
                                    @forelse (Auth::user()->unreadNotifications as $notification)
                                    <li>
                                        <a href="#" class="list-group-item d-flex hide-show-toggler">
                                            <div>
                                                <figure class="avatar mr-3">
                                                    <span
                                                        class="avatar-title bg-secondary-bright text-secondary rounded-circle">
                                                        <i class="ti-alert"></i>
                                                    </span>
                                                </figure>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-0">
                                                    {{ $notification->data['message'] }}
                                                    <i title="Mark as unread" data-toggle="tooltip"
                                                        class="hide-show-toggler-item fa fa-check font-size-11 position-absolute right-0 top-0 mr-3 mt-3"></i>
                                                </p>
                                                <span class="text-muted small">{{
                                                    $notification->created_at->diffForHumans() }}</span>
                                            </div>
                                        </a>
                                    </li>
                                    @empty
                                    <li class="p-2">No notifications</li>
                                    @endforelse

                                </ul>
                            </div>
                            @if (Auth::user()->unreadNotifications->count() > 0)
                            <div class="px-3 py-2 text-right border-top">
                                <ul class="list-inline small">
                                    <li class="list-inline-item mb-0">
                                        <form method="POST" action="{{ route('user.notifications.mark-all-as-read') }}">
                                            @csrf
                                            <a href="{{ route('user.notifications.mark-all-as-read') }}"
                                                onclick="event.preventDefault(); this.closest('form').submit();">Mark
                                                All Read</a>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            @endif
                        </div>
                    </li>

                    {{-- <li class="nav-item dropdown">
                        <a href="#" class="nav-link" title="Settings" data-sidebar-target="#settings">
                            <i class="ti-settings"></i>
                        </a>
                    </li> --}}

                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link profile-nav-link dropdown-toggle" title="User menu"
                            data-toggle="dropdown">
                            <span class="mr-2 d-sm-inline d-none">{{ Auth::user()->name }}</span>
                            <figure class="avatar avatar-sm">
                                <img src="{{ asset('assets') }}/media/image/user/user_avatar.jpeg"
                                    class="rounded-circle" alt="avatar">
                            </figure>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-big">
                            <div class="text-center py-4"
                                data-background-image="{{ asset('assets') }}//media/image/image1.jpg">
                                <figure class="avatar avatar-lg mb-3 border-0">
                                    <img src="{{ asset('assets') }}/media/image/user/user_avatar.jpeg"
                                        class="rounded-circle" alt="image">
                                </figure>
                                <h5 class="mb-0">{{ Auth::user()->name }}</h5>
                            </div>
                            <div class="list-group list-group-flush">
                                {{-- <a href="#" class="list-group-item" data-sidebar-target="#settings">Profile</a>
                                --}}
                                <a href="{{ route('profile.edit') }}" class="list-group-item">Profile</a>
                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}" class="list-group-item text-danger"
                                        onclick="event.preventDefault(); this.closest('form').submit();">Sign Out!</a>
                                </form>
                            </div>
                            <div class="pb-0 p-4 d-none">
                                <div class="mb-4">
                                    <h6 class="d-flex justify-content-between">
                                        Completed Tasks
                                        <span class="float-right">%68</span>
                                    </h6>
                                    <div class="progress" style="height:5px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 68%;"
                                            aria-valuenow="68" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item header-toggler">
                <a href="#" class="nav-link">
                    <i class="ti-arrow-down"></i>
                </a>
            </li>
            <li class="nav-item sidebar-toggler">
                <a href="#" class="nav-link">
                    <i class="ti-cloud"></i>
                </a>
            </li>
        </ul>
    </div>
</div>