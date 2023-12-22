<div class="main-header">
    <!-- Logo Header -->
    <div class="logo-header"
        @if (auth()->check() && auth()->user()->display_mode == 'night') data-background-color="dark"
        @else
        data-background-color="blue" @endif>

        <a class="logo d-flex align-items-center">
            <img src="{{ asset('images/layout/laco-logo-icon.ico') }}" alt="navbar brand" class="navbar-brand"
                height="50%">
            <h6 class="font-weight-bold text-white mb-0 ml-2">
                LCCM Library System
            </h6>
        </a>

        <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse"
            data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
                <i class="icon-menu"></i>
            </span>
        </button>
        <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
        <div class="nav-toggle">
            <button class="btn btn-toggle toggle-sidebar">
                <i class="icon-menu"></i>
            </button>
        </div>
    </div>
    <!-- End Logo Header -->

    <!-- Navbar Header -->
    <nav class="navbar navbar-header navbar-expand-lg"
        @if (auth()->check() && auth()->user()->display_mode == 'night') data-background-color="dark"
    @else
        data-background-color="blue2" @endif>

        <div class="container-fluid">
            {{-- <div class="collapse" id="search-nav">
                <form class="navbar-left navbar-form nav-search mr-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="submit" class="btn btn-search pr-1">
                                <i class="fa fa-search search-icon"></i>
                            </button>
                        </div>
                        <input type="text" placeholder="Search ..." class="form-control">
                    </div>
                </form>
            </div> --}}
            <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                {{-- <li class="nav-item toggle-nav-search hidden-caret">
                    <a class="nav-link" data-toggle="collapse" href="#search-nav" role="button" aria-expanded="false"
                        aria-controls="search-nav">
                        <i class="fa fa-search"></i>
                    </a>
                </li> --}}
                {{-- <li class="nav-item dropdown hidden-caret">
                    <a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-envelope"></i>
                    </a>
                    <ul class="dropdown-menu messages-notif-box animated fadeIn" aria-labelledby="messageDropdown">
                        <li>
                            <div class="dropdown-title d-flex justify-content-between align-items-center">
                                Messages
                                <a href="#" class="small">Mark all as read</a>
                            </div>
                        </li>
                        <li>
                            <div class="message-notif-scroll scrollbar-outer">
                                <div class="notif-center">
                                    <a href="#">
                                        <div class="notif-img">
                                            <img src="../assets/img/jm_denis.jpg" alt="Img Profile">
                                        </div>
                                        <div class="notif-content">
                                            <span class="subject">Jimmy Denis</span>
                                            <span class="block">
                                                How are you ?
                                            </span>
                                            <span class="time">5 minutes ago</span>
                                        </div>
                                    </a>
                                    <a href="#">
                                        <div class="notif-img">
                                            <img src="../assets/img/chadengle.jpg" alt="Img Profile">
                                        </div>
                                        <div class="notif-content">
                                            <span class="subject">Chad</span>
                                            <span class="block">
                                                Ok, Thanks !
                                            </span>
                                            <span class="time">12 minutes ago</span>
                                        </div>
                                    </a>
                                    <a href="#">
                                        <div class="notif-img">
                                            <img src="../assets/img/mlane.jpg" alt="Img Profile">
                                        </div>
                                        <div class="notif-content">
                                            <span class="subject">Jhon Doe</span>
                                            <span class="block">
                                                Ready for the meeting today...
                                            </span>
                                            <span class="time">12 minutes ago</span>
                                        </div>
                                    </a>
                                    <a href="#">
                                        <div class="notif-img">
                                            <img src="../assets/img/talha.jpg" alt="Img Profile">
                                        </div>
                                        <div class="notif-content">
                                            <span class="subject">Talha</span>
                                            <span class="block">
                                                Hi, Apa Kabar ?
                                            </span>
                                            <span class="time">17 minutes ago</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="see-all" href="javascript:void(0);">See all messages<i
                                    class="fa fa-angle-right"></i> </a>
                        </li>
                    </ul>
                </li> --}}
                {{-- <li class="nav-item dropdown hidden-caret">
                    <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bell"></i>
                        <span class="notification">4</span>
                    </a>
                    <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
                        <li>
                            <div class="dropdown-title">You have 4 new notification</div>
                        </li>
                        <li>
                            <div class="notif-scroll scrollbar-outer">
                                <div class="notif-center">
                                    <a href="#">
                                        <div class="notif-icon notif-primary"> <i class="fa fa-patron-plus"></i> </div>
                                        <div class="notif-content">
                                            <span class="block">
                                                New patron registered
                                            </span>
                                            <span class="time">5 minutes ago</span>
                                        </div>
                                    </a>
                                    <a href="#">
                                        <div class="notif-icon notif-success"> <i class="fa fa-comment"></i> </div>
                                        <div class="notif-content">
                                            <span class="block">
                                                Rahmad commented on Admin
                                            </span>
                                            <span class="time">12 minutes ago</span>
                                        </div>
                                    </a>
                                    <a href="#">
                                        <div class="notif-img">
                                            <img src="../assets/img/profile2.jpg" alt="Img Profile">
                                        </div>
                                        <div class="notif-content">
                                            <span class="block">
                                                Reza send messages to you
                                            </span>
                                            <span class="time">12 minutes ago</span>
                                        </div>
                                    </a>
                                    <a href="#">
                                        <div class="notif-icon notif-danger"> <i class="fa fa-heart"></i> </div>
                                        <div class="notif-content">
                                            <span class="block">
                                                Farrah liked Admin
                                            </span>
                                            <span class="time">17 minutes ago</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="see-all" href="javascript:void(0);">See all notifications<i
                                    class="fa fa-angle-right"></i> </a>
                        </li>
                    </ul>
                </li> --}}
                {{-- <li class="nav-item dropdown hidden-caret">
                    <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                        <i class="fas fa-layer-group"></i>
                    </a>
                    <div class="dropdown-menu quick-actions quick-actions-info animated fadeIn">
                        <div class="quick-actions-header">
                            <span class="title mb-1">Quick Actions</span>
                            <span class="subtitle op-8">Shortcuts</span>
                        </div>
                        <div class="quick-actions-scroll scrollbar-outer">
                            <div class="quick-actions-items">
                                <div class="row m-0">
                                    <a class="col-6 col-md-4 p-0" href="#">
                                        <div class="quick-actions-item">
                                            <i class="flaticon-file-1"></i>
                                            <span class="text">Generated Report</span>
                                        </div>
                                    </a>
                                    <a class="col-6 col-md-4 p-0" href="#">
                                        <div class="quick-actions-item">
                                            <i class="flaticon-database"></i>
                                            <span class="text">Create New Database</span>
                                        </div>
                                    </a>
                                    <a class="col-6 col-md-4 p-0" href="#">
                                        <div class="quick-actions-item">
                                            <i class="flaticon-pen"></i>
                                            <span class="text">Create New Post</span>
                                        </div>
                                    </a>
                                    <a class="col-6 col-md-4 p-0" href="#">
                                        <div class="quick-actions-item">
                                            <i class="flaticon-interface-1"></i>
                                            <span class="text">Create New Task</span>
                                        </div>
                                    </a>
                                    <a class="col-6 col-md-4 p-0" href="#">
                                        <div class="quick-actions-item">
                                            <i class="flaticon-list"></i>
                                            <span class="text">Completed Tasks</span>
                                        </div>
                                    </a>
                                    <a class="col-6 col-md-4 p-0" href="#">
                                        <div class="quick-actions-item">
                                            <i class="flaticon-file"></i>
                                            <span class="text">Create New Invoice</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li> --}}
                {{-- <li class="nav-item dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#"
                        aria-expanded="false">
                        <div class="avatar-sm">
                            <img src="../assets/img/profile.jpg" alt="..." class="avatar-img rounded-circle">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-user animated fadeIn">
                        <div class="dropdown-user-scroll scrollbar-outer">
                            <li>
                                <div class="user-box">
                                    <div class="avatar-lg"><img src="../assets/img/profile.jpg" alt="image profile"
                                            class="avatar-img rounded"></div>
                                    <div class="u-text">
                                        <h4>Hizrian</h4>
                                        <p class="text-muted">hello@example.com</p><a href="profile.html"
                                            class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">My Profile</a>
                                <a class="dropdown-item" href="#">My Balance</a>
                                <a class="dropdown-item" href="#">Inbox</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">Account Setting</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">Logout</a>
                            </li>
                        </div>
                    </ul>
                </li> --}}
            </ul>
        </div>
    </nav>
    <!-- End Navbar -->
</div>

<!-- Sidebar -->
<div class="sidebar sidebar-style-2" @if (auth()->check() && auth()->user()->display_mode == 'night') data-background-color="dark" @endif>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <div class="user">
                <div class="avatar-sm float-left mr-2">
                    <img src="
                        @auth
@php
                                $image = optional(auth()->user()->images()->latest()->first())->file_name;
                            @endphp
                            @if ($image != null)
                                 {{ asset('storage/images/patrons/' . $image) }}
                            @else
                                {{-- {{ Storage::url('images/patrons/default.jpg') }} --}}
                                {{ Avatar::create(auth()->user()->first_name . ', ' . auth()->user()->last_name)->setFontFamily('Lato')->toBase64() }}
                            @endif @endauth

                        @guest
{{ asset('storage/images/patrons/guest.jpg') }} @endguest
                    "
                        alt="Patron Image" class="avatar-img rounded-circle">
                </div>
                <div class="info">
                    <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
                        <span>
                            @auth
                                {{ auth()->user()->last_name . ', ' . auth()->user()->first_name }}
                            @else
                                Hello, Guest!
                            @endauth
                            <span class="user-level">
                                @auth
                                    {{ Str::title(auth()->user()->temp_role) }}
                                @else
                                    Guest
                                @endauth
                            </span>
                            @auth
                                <span class="caret"></span>
                            @endauth
                        </span>
                    </a>
                    <div class="clearfix"></div>

                    @auth
                        <div class="collapse in {{ Route::currentRouteName() == 'settings.index' || Route::currentRouteName() == 'profile' ? 'show' : '' }}"
                            id="collapseExample">
                            <ul class="nav">
                                <li>
                                    <a href="{{ route('profile') }}">
                                        <span
                                            class="link-collapse {{ request()->path() == 'profile' ? 'active' : '' }}">Profile</span>
                                    </a>
                                </li>
                                @if (auth()->user()->temp_role == 'librarian')
                                    <li>
                                        <a href="{{ route('settings.index') }}">
                                            <span
                                                class="link-collapse {{ request()->path() == 'settings' ? 'active' : '' }}">Settings</span>
                                        </a>
                                    </li>
                                @endif
                                @if (count(auth()->user()->getRoleNames()) > 1)
                                    <li>
                                        <a href="{{ route('login.as') }}">
                                            <span class="link-collapse">Switch Role</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endauth
                </div>
            </div>
            <ul class="nav nav-primary">
                @auth
                    <li class="nav-item {{ Route::currentRouteName() == 'dashboard.index' ? 'active' : '' }}">
                        <a href="{{ route('dashboard.index') }}" aria-expanded="false">
                            <i class="fas fa-home"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                @endauth
                {{-- <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Librarian</h4>
                    </li> --}}
                @auth
                    @if (auth()->user()->temp_role == 'librarian')
                        <li
                            class="nav-item {{ (Route::currentRouteName() == 'patrons.show' && isset($patronStatus) && $patronStatus == 'active') || Route::currentRouteName() == 'patrons.index' || Route::currentRouteName() == 'registrations.index' || Route::currentRouteName() == 'attendance.index' ? 'active' : '' }}">
                            <a href="{{ route('patrons.index') }}" aria-expanded="false">
                                <i class="fa-solid fa-user"></i>
                                <p>Patrons</p>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->temp_role != 'librarian')
                        <li class="nav-item {{ Route::currentRouteName() == 'attendance.index' ? 'active' : '' }}">
                            <a href="{{ route('attendance.index') }}" aria-expanded="false">
                                <i class="fa-solid fa-user-clock"></i>
                                <p>Attendance</p>
                            </a>
                        </li>
                    @endif
                @endauth
                <li
                    class="nav-item {{ (Route::currentRouteName() == 'collections.show' && isset($collectionStatus) && $collectionStatus == 'active') || Route::currentRouteName() == 'collections.index' || Route::currentRouteName() == 'google.books.api.index' ? 'active' : '' }}">
                    <a href="{{ route('collections.index') }}" aria-expanded="false">
                        <i class="fa-solid fa-book"></i>
                        <p>Collections</p>
                    </a>
                </li>
                @auth
                    <li class="nav-item {{ request()->path() == 'reservations' ? 'active' : '' }}">
                        <a href="{{ route('reservations.index') }}" aria-expanded="false">
                            <i class="fa-regular fa-calendar-check"></i>
                            <p>Reservations</p>
                        </a>
                    </li>
                @endauth
                @auth
                    @if (auth()->user()->temp_role != 'librarian')
                        <li
                            class="nav-item {{ Route::currentRouteName() == 'off.site.circulations.index' || Route::currentRouteName() == 'off.site.circulations.show' ? 'active' : '' }}">
                            <a href="{{ route('off.site.circulations.index') }}" aria-expanded="false">
                                <i class="fa-solid fa-arrows-rotate"></i>
                                <p>Circulations (Off-Site)</p>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->temp_role == 'librarian')
                        <li
                            class="nav-item {{ Route::currentRouteName() == 'off.site.circulations.index' || Route::currentRouteName() == 'off.site.circulations.create' || (Route::currentRouteName() == 'off.site.circulations.show' && isset($offSiteCirculationStatus) && $offSiteCirculationStatus == 'active') || Route::currentRouteName() == 'in.house.circulations.index' ? 'active' : '' }}">
                            <a data-toggle="collapse" href="#circulations">
                                <i class="fa-solid fa-arrows-rotate"></i>
                                <p>Circulations</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="circulations">
                                <ul class="nav nav-collapse">
                                    <li
                                        class="{{ Route::currentRouteName() == 'off.site.circulations.index' || (Route::currentRouteName() == 'off.site.circulations.show' && isset($offSiteCirculationStatus) && $offSiteCirculationStatus == 'active') ? 'active' : '' }}">
                                        <a href="{{ route('off.site.circulations.index') }}">
                                            <span class="sub-item">Off-site</span>
                                        </a>
                                    </li>
                                    <li
                                        class="{{ Route::currentRouteName() == 'in.house.circulations.index' ? 'active' : '' }}">
                                        <a href="{{ route('in.house.circulations.index') }}">
                                            <span class="sub-item">In-house</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif
                @endauth
                @auth
                    <li class="nav-item {{ request()->path() == 'payments' ? 'active' : '' }}">
                        <a href="{{ route('payments.index') }}" aria-expanded="false">
                            <i class="fa-solid fa-peso-sign"></i>
                            <p>Payments</p>
                        </a>
                    </li>
                @endauth
                @auth
                    @if (auth()->user()->temp_role == 'librarian')
                        <li class="nav-item {{ request()->path() == 'reports' ? 'active' : '' }}">
                            <a href="{{ route('reports.index') }}" aria-expanded="false">
                                <i class="fa-solid fa-chart-simple"></i>
                                <p>Reports</p>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->temp_role == 'librarian')
                        <li
                            class="nav-item {{ Route::currentRouteName() == 'imports.index' || (Route::currentRouteName() == 'imports.show' && isset($importStatus) && $importStatus == 'active') ? 'active' : '' }}">
                            <a href="{{ route('imports.index') }}" aria-expanded="false">
                                <i class="fa-solid fa-file-import"></i>
                                <p>Imports</p>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->temp_role == 'librarian')
                        <li class="nav-item {{ Route::currentRouteName() == 'announcements.index' ? 'active' : '' }}">
                            <a href="{{ route('announcements.index') }}" aria-expanded="false">
                                <i class="fa-solid fa-bullhorn"></i>
                                <p>Announcements</p>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->temp_role != 'librarian')
                        <li class="nav-item {{ request()->path() == 'shelf-items' ? 'active' : '' }}">
                            <a href="{{ route('shelf.items.index') }}" aria-expanded="false">
                                <i class="fa-solid fa-swatchbook"></i>
                                <p>Shelf Items</p>
                            </a>
                        </li>
                    @endif
                    <li class="nav-item {{ Route::currentRouteName() == 'help.index' ? 'active' : '' }}">
                        <a href="{{ route('help.index') }}" aria-expanded="false">
                            <i class="fa-solid fa-circle-info"></i>
                            <p>Help</p>
                        </a>
                    </li>
                    <li class="nav-item {{ Route::currentRouteName() == 'online.resources.index' ? 'active' : '' }}" aria-expanded="false">
                        <a href="{{ route('online.resources.index') }}">
                            <i class="fa-solid fa-globe"></i>
                            <p>Online resources</p>
                        </a>
                    </li>
                    @if (auth()->user()->temp_role == 'librarian')
                        <li
                            class="nav-item {{ str_contains(request()->url(), 'archive') || (Route::currentRouteName() == 'patrons.show' && isset($patronStatus) && $patronStatus == 'archived') || (Route::currentRouteName() == 'collections.show' && isset($collectionStatus) && $collectionStatus == 'archived') || (Route::currentRouteName() == 'off.site.circulations.show' && isset($offSiteCirculationStatus) && $offSiteCirculationStatus == 'archived') || Route::currentRouteName() == 'reports.archive' || (Route::currentRouteName() == 'imports.show' && isset($importStatus) && $importStatus == 'archived') ? 'active' : '' }}">
                            <a data-toggle="collapse" href="#archive">
                                <i class="fa-solid fa-trash-can"></i>
                                <p>Archive</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="archive">
                                <ul class="nav nav-collapse">
                                    <li
                                        class="{{ (Route::currentRouteName() == 'patrons.show' && isset($patronStatus) && $patronStatus == 'archived') || Route::currentRouteName() == 'patrons.archive' ? 'active' : '' }}">
                                        <a href="{{ route('patrons.archive') }}">
                                            <span class="sub-item">Patrons</span>
                                        </a>
                                    </li>
                                    <li class="{{ Route::currentRouteName() == 'attendance.archive' ? 'active' : '' }}">
                                        <a href="{{ route('attendance.archive') }}">
                                            <span class="sub-item">Attendance</span>
                                        </a>
                                    </li>
                                    <li
                                        class="{{ (Route::currentRouteName() == 'collections.show' && isset($collectionStatus) && $collectionStatus == 'archived') || Route::currentRouteName() == 'collections.archive' ? 'active' : '' }}">
                                        <a href="{{ route('collections.archive') }}">
                                            <span class="sub-item">Collections</span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->path() == 'reservations-archive' ? 'active' : '' }}">
                                        <a href="{{ route('reservations.archive') }}">
                                            <span class="sub-item">Reservations</span>
                                        </a>
                                    </li>
                                    <li
                                        class="{{ Route::currentRouteName() == 'off.site.circulations.archive' || (Route::currentRouteName() == 'off.site.circulations.show' && isset($offSiteCirculationStatus) && $offSiteCirculationStatus == 'archived') ? 'active' : '' }}">
                                        <a href="{{ route('off.site.circulations.archive') }}">
                                            <span class="sub-item">Circulations (Off-site)</span>
                                        </a>
                                    </li>
                                    <li
                                        class="{{ request()->path() == 'in-house-circulations-archive' ? 'active' : '' }}">
                                        <a href="{{ route('in.house.circulations.archive') }}">
                                            <span class="sub-item">Circulations (In-house)</span>
                                        </a>
                                    </li>
                                    <li class="{{ Route::currentRouteName() == 'payments.archive' ? 'active' : '' }}">
                                        <a href="{{ route('payments.archive') }}">
                                            <span class="sub-item">Payments</span>
                                        </a>
                                    </li>
                                    <li class="{{ Route::currentRouteName() == 'reports.archive' ? 'active' : '' }}">
                                        <a href="{{ route('reports.archive') }}">
                                            <span class="sub-item">Reports</span>
                                        </a>
                                    </li>
                                    <li
                                        class="{{ (Route::currentRouteName() == 'imports.show' && isset($importStatus) && $importStatus == 'archived') || Route::currentRouteName() == 'imports.archive' ? 'active' : '' }}">
                                        <a href="{{ route('imports.archive') }}">
                                            <span class="sub-item">Imports</span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->path() == 'announcements-archive' ? 'active' : '' }}">
                                        <a href="{{ route('announcements.archive') }}">
                                            <span class="sub-item">Announcements</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif
                @endauth
                <li class="mx-4 mt-2">
                    <form action="{{ auth()->check() ? route('logout') : route('login') }}"
                        method="{{ auth()->check() ? 'POST' : 'GET' }}" id="form-logout">
                        @csrf
                        <a href="" class="btn btn-outline-primary btn-block" id="btn-logout">
                            @auth
                                <span class="btn-label mr-2"> <i class="fa-solid fa-right-from-bracket"></i>
                                @else
                                    <span class="btn-label mr-2"> <i class="fa-solid fa-right-to-bracket"></i>
                                    @endauth
                                </span>{{ auth()->check() ? 'Logout' : 'Login' }}</a>
                    </form>
                    @auth
                        <form action="{{ route('toggle.display.mode') }}" method="post" id="display-mode-form">
                            @csrf
                            <div class="form-group pl-0">
                                <div class="selectgroup selectgroup-secondary selectgroup-pills">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="display_mode" value="day"
                                            class="selectgroup-input display-mode-input"
                                            {{ auth()->user()->display_mode == 'day' ? 'checked' : '' }}>
                                        <span class="selectgroup-button selectgroup-button-icon"><i
                                                class="fa fa-sun"></i></span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="display_mode" value="night"
                                            class="selectgroup-input display-mode-input"
                                            {{ auth()->user()->display_mode == 'night' ? 'checked' : '' }}>
                                        <span class="selectgroup-button selectgroup-button-icon"><i
                                                class="fa fa-moon"></i></span>
                                    </label>
                                </div>
                            </div>
                        </form>
                    @endauth

                    {{-- <a type="button" class="btn btn-outline-primary btn-block dropdown-toggle text-start" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button" aria-disabled="true">
                        Logout
                    </a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
                        <a class="dropdown-item" href="#">Logout</a>
                        <a class="dropdown-item" href="#">Switch role</a>
                        {{-- <div class="dropdown-divider"></div> --}}
                    {{-- <a class="dropdown-item" href="#">Something else here</a> --}}
            </ul>
            </li>

            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->
