{{--
    Sidebar from quizapp's partials/admin-sidebar.blade.php. Items appear only when the user holds
    the permission; the routes check the same permissions on the server.
    Items for modules that are not built yet are left out rather than shown as dead links.
--}}
@php
    $isPeople = request()->routeIs('admin.students.*', 'admin.teachers.*', 'admin.staff.*', 'admin.roles.*');
    $pendingTeachers = auth()->user()->can('teachers.verify')
        ? \App\Models\TeacherProfile::where('verification_status', 'pending')->count()
        : 0;
@endphp
<nav class="sidebar" aria-label="Admin navigation">
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            <div class="brand-content-light">
                <div class="brand-icon"><span>{{ mb_substr(config('app.name'), 0, 1) }}</span></div>
                <span class="brand-text">{{ config('app.name') }}</span>
            </div>
            <div class="brand-content-dark">
                <div class="brand-icon"><span>{{ mb_substr(config('app.name'), 0, 1) }}</span></div>
                <span class="brand-text text-white">{{ config('app.name') }}</span>
            </div>
        </a>
        <button class="mobile-close-btn" id="mobile-close" type="button" aria-label="Close menu">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <div class="sidebar-menu">
        <div class="menu-group-title">Main</div>

        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
            <div class="nav-item-content">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                <span>Dashboard</span>
            </div>
        </a>

        @canany(['users.view', 'staff.manage', 'roles.manage'])
            <div class="menu-group-title">People</div>
            <div class="nav-group">
                <div class="nav-item nav-dropdown-toggle {{ $isPeople ? 'active' : '' }}" role="button" tabindex="0"
                     aria-expanded="{{ $isPeople ? 'true' : 'false' }}" aria-controls="people-menu">
                    <div class="nav-item-content">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        <span>Users</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($pendingTeachers > 0)
                            <span class="nav-badge badge-warn" title="Teachers waiting for approval">{{ $pendingTeachers }}</span>
                        @endif
                        <svg class="nav-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </div>
                <div id="people-menu" class="nav-dropdown {{ $isPeople ? 'show' : '' }}">
                    @can('users.view')
                        <a href="{{ route('admin.students.index') }}" class="nav-sub-item {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">Students</a>
                        <a href="{{ route('admin.teachers.index') }}" class="nav-sub-item {{ request()->routeIs('admin.teachers.*') && request('verification') !== 'pending' ? 'active' : '' }}">Teachers</a>
                        @can('teachers.verify')
                            <a href="{{ route('admin.teachers.index', ['verification' => 'pending']) }}" class="nav-sub-item {{ request('verification') === 'pending' ? 'active' : '' }}">
                                Approval queue
                                @if($pendingTeachers > 0)<span class="nav-badge badge-warn ms-auto">{{ $pendingTeachers }}</span>@endif
                            </a>
                        @endcan
                    @endcan
                    @can('staff.manage')
                        <a href="{{ route('admin.staff.index') }}" class="nav-sub-item {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">Staff</a>
                    @endcan
                    @can('roles.manage')
                        <a href="{{ route('admin.roles.index') }}" class="nav-sub-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">Roles &amp; permissions</a>
                    @endcan
                </div>
            </div>
        @endcanany

        @can('audit.view')
            <div class="menu-group-title">System</div>
            <a href="{{ route('admin.audit.index') }}" class="nav-item {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
                <div class="nav-item-content">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    <span>Audit log</span>
                </div>
            </a>
        @endcan
    </div>

    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                <span>Log out</span>
            </button>
        </form>
    </div>
</nav>
