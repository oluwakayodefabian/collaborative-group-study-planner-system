<div class="navigation">
    <div class="logo">
        <a href={{ route('user.dashboard') }}>
            <img src="{{ asset('/logo.png') }}" alt="logo" class="img-fluid" style="width: 50px">
        </a>
    </div>
    <ul>
        <li>
            <a href="{{route('user.dashboard')}}" @class(['active'=> request()->routeIs('user.dashboard')])>
                <i class="nav-link-icon ti-pie-chart"></i>
                <span class="nav-link-label">Dashboard</span>
                <span class="badge badge-danger badge-small">2</span>
            </a>
        </li>
        <li>
            <a href="{{ route('user.study-groups.index') }}" @class(['active'=>
                request()->routeIs('user.study-groups.index')])>
                <i class="nav-link-icon fas fa-users-cog"></i>
                <span class="nav-link-label">Study Groups</span>
                {{-- <span class="badge badge-warning">{{ $total_groups }}</span> --}}
            </a>
        </li>
        {{-- <li>
            <a href="{{ route('user-management.index') }}" @class(['active'=>
                request()->routeIs('user-management.index')])>
                <i class="nav-link-icon fas fa-users-cog"></i>
                <span class="nav-link-label">Users</span>
                <span class="badge badge-warning">{{ $total_users }}</span>
            </a>
        </li> --}}

        <li>
            <a href="{{ route('profile.edit') }}" @class(['active'=> request()->routeIs('profile.edit')])>
                <i class="nav-link-icon ti-settings"></i>
                <span class="nav-link-label">Profile</span>
            </a>
        </li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}" class="nav-link-label"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                    <i class="nav-link-icon fas fa-sign-out"></i>
                    Sign Out!
                </a>
            </form>

        </li>
    </ul>
</div>