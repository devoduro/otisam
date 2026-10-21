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
                <span class="brand-text">{{ config('app.name') }}<span class="brand-sub">Admin console</span></span>
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

        @canany(['tutorials.manage', 'resources.manage', 'resources.publish', 'resources.moderate', 'takedowns.manage'])
            <div class="menu-group-title">Content</div>
        @endcanany
        @can('tutorials.manage')
            <a href="{{ route('admin.videos.index') }}" class="nav-item {{ request()->routeIs('admin.videos.*') ? 'active' : '' }}">
                <div class="nav-item-content">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
                    <span>Video lessons</span>
                </div>
            </a>
            @php $pendingTeacherContent = \App\Http\Controllers\Admin\ContentModerationController::pendingVideos()->count(); @endphp
            <a href="{{ route('admin.moderation.index') }}" class="nav-item {{ request()->routeIs('admin.moderation.*') ? 'active' : '' }}">
                <div class="nav-item-content">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>
                    <span>Teacher content</span>
                </div>
                @if($pendingTeacherContent > 0)
                    <span class="nav-badge badge-warn" title="Teacher videos waiting for review">{{ $pendingTeacherContent }}</span>
                @endif
            </a>
            @php $pendingLessonPlans = \App\Models\LessonPlans\LessonPlan::where('status', 'pending')->count(); @endphp
            <a href="{{ route('admin.lesson-plans.index') }}" class="nav-item {{ request()->routeIs('admin.lesson-plans.*') ? 'active' : '' }}">
                <div class="nav-item-content">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="2" width="8" height="4" rx="1"></rect><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><line x1="9" y1="12" x2="15" y2="12"></line><line x1="9" y1="16" x2="13" y2="16"></line></svg>
                    <span>Lesson plans</span>
                </div>
                @if($pendingLessonPlans > 0)
                    <span class="nav-badge badge-warn" title="Lesson plans waiting for review">{{ $pendingLessonPlans }}</span>
                @endif
            </a>
            @php $openQaReports = \App\Models\Community\Report::openPostCount(); @endphp
            <a href="{{ route('admin.qa.index') }}" class="nav-item {{ request()->routeIs('admin.qa.*') ? 'active' : '' }}">
                <div class="nav-item-content">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    <span>Q&amp;A reports</span>
                </div>
                @if($openQaReports > 0)
                    <span class="nav-badge badge-warn" title="Reported Q&amp;A posts waiting for review">{{ $openQaReports }}</span>
                @endif
            </a>
        @endcan

        @canany(['resources.manage', 'resources.publish', 'resources.moderate', 'takedowns.manage'])
            @php
                $isLibrary = request()->routeIs('admin.library.*');
                $openTakedowns = auth()->user()->can('takedowns.manage') ? \App\Models\Library\TakedownRequest::where('status', 'open')->count() : 0;
            @endphp
            <div class="nav-group">
                <div class="nav-item nav-dropdown-toggle {{ $isLibrary ? 'active' : '' }}" role="button" tabindex="0"
                     aria-expanded="{{ $isLibrary ? 'true' : 'false' }}" aria-controls="library-menu">
                    <div class="nav-item-content">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                        <span>Library</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($openTakedowns > 0)<span class="nav-badge badge-warn" title="Open takedown requests">{{ $openTakedowns }}</span>@endif
                        <svg class="nav-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </div>
                <div id="library-menu" class="nav-dropdown {{ $isLibrary ? 'show' : '' }}">
                    @canany(['resources.manage', 'resources.publish', 'resources.moderate'])
                        <a href="{{ route('admin.library.resources.index') }}" class="nav-sub-item {{ request()->routeIs('admin.library.resources.index', 'admin.library.resources.edit', 'admin.library.resources.create') ? 'active' : '' }}">Resources</a>
                    @endcanany
                    @can('resources.bulk-import')
                        <a href="{{ route('admin.library.resources.bulk') }}" class="nav-sub-item {{ request()->routeIs('admin.library.resources.bulk') ? 'active' : '' }}">Bulk upload</a>
                    @endcan
                    @can('resources.manage')
                        <a href="{{ route('admin.library.settings') }}" class="nav-sub-item {{ request()->routeIs('admin.library.settings') ? 'active' : '' }}">Categories &amp; settings</a>
                    @endcan
                    @can('takedowns.manage')
                        <a href="{{ route('admin.library.takedowns.index') }}" class="nav-sub-item {{ request()->routeIs('admin.library.takedowns.*') ? 'active' : '' }}">
                            Takedown requests
                            @if($openTakedowns > 0)<span class="nav-badge badge-warn ms-auto">{{ $openTakedowns }}</span>@endif
                        </a>
                    @endcan
                </div>
            </div>
        @endcanany

        @canany(['plans.manage', 'coupons.manage', 'payments.view', 'subscriptions.adjust'])
            @php $isCommerce = request()->routeIs('admin.commerce.*'); @endphp
            <div class="menu-group-title">Finance</div>
            <div class="nav-group">
                <div class="nav-item nav-dropdown-toggle {{ $isCommerce ? 'active' : '' }}" role="button" tabindex="0"
                     aria-expanded="{{ $isCommerce ? 'true' : 'false' }}" aria-controls="commerce-menu">
                    <div class="nav-item-content">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                        <span>Subscriptions</span>
                    </div>
                    <svg class="nav-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
                <div id="commerce-menu" class="nav-dropdown {{ $isCommerce ? 'show' : '' }}">
                    @can('payments.view')
                        <a href="{{ route('admin.commerce.payments.index') }}" class="nav-sub-item {{ request()->routeIs('admin.commerce.payments.*') ? 'active' : '' }}">Payments</a>
                    @endcan
                    @canany(['subscriptions.adjust', 'payments.view'])
                        <a href="{{ route('admin.commerce.subscriptions.index') }}" class="nav-sub-item {{ request()->routeIs('admin.commerce.subscriptions.*') ? 'active' : '' }}">Subscribers</a>
                    @endcanany
                    @can('plans.manage')
                        <a href="{{ route('admin.commerce.plans.index') }}" class="nav-sub-item {{ request()->routeIs('admin.commerce.plans.*') ? 'active' : '' }}">Plans</a>
                    @endcan
                    @can('coupons.manage')
                        <a href="{{ route('admin.commerce.coupons.index') }}" class="nav-sub-item {{ request()->routeIs('admin.commerce.coupons.*') ? 'active' : '' }}">Coupons</a>
                    @endcan
                </div>
            </div>
        @endcanany

        @canany(['curriculum.view-admin', 'curriculum.manage'])
            @php $isCurriculum = request()->routeIs('admin.curriculum.*'); @endphp
            <div class="menu-group-title">Academics</div>
            <div class="nav-group">
                <div class="nav-item nav-dropdown-toggle {{ $isCurriculum ? 'active' : '' }}" role="button" tabindex="0"
                     aria-expanded="{{ $isCurriculum ? 'true' : 'false' }}" aria-controls="curriculum-menu">
                    <div class="nav-item-content">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                        <span>Curriculum</span>
                    </div>
                    <svg class="nav-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
                <div id="curriculum-menu" class="nav-dropdown {{ $isCurriculum ? 'show' : '' }}">
                    <a href="{{ route('admin.curriculum.versions.index') }}" class="nav-sub-item {{ request()->routeIs('admin.curriculum.versions.*', 'admin.curriculum.tree.*') ? 'active' : '' }}">Versions &amp; topics</a>
                    <a href="{{ route('admin.curriculum.subjects.index') }}" class="nav-sub-item {{ request()->routeIs('admin.curriculum.subjects.*') ? 'active' : '' }}">Subjects</a>
                    <a href="{{ route('admin.curriculum.programmes.index') }}" class="nav-sub-item {{ request()->routeIs('admin.curriculum.programmes.*') ? 'active' : '' }}">Programmes</a>
                    <a href="{{ route('admin.curriculum.levels.index') }}" class="nav-sub-item {{ request()->routeIs('admin.curriculum.levels.*') ? 'active' : '' }}">Levels &amp; classes</a>
                </div>
            </div>
        @endcanany

        @canany(['audit.view', 'settings.manage'])
            <div class="menu-group-title">System</div>
        @endcanany
        @can('settings.manage')
            <a href="{{ route('admin.settings.homepage') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <div class="nav-item-content">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    <span>Homepage</span>
                </div>
            </a>
        @endcan
        @can('audit.view')
            <a href="{{ route('admin.audit.index') }}" class="nav-item {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
                <div class="nav-item-content">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    <span>Audit log</span>
                </div>
            </a>
        @endcan
    </div>

    <div class="sidebar-footer d-flex align-items-center gap-1">
        <a href="{{ route('password.change') }}" class="sidebar-user flex-grow-1" title="Change password">
            <span class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <span class="who"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->roles->pluck('name')->first() ?? 'Staff' }}</small></span>
        </a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn border-0 bg-transparent" title="Log out" aria-label="Log out"><i class="fas fa-sign-out-alt" aria-hidden="true"></i></button>
        </form>
    </div>
</nav>
