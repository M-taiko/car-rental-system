<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
    <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active" href="{{ route('index') }}">
            <img src="{{ asset('assets/img/brand/logo.png') }}" class="main-logo" style="width: 188px; height: 68px;" alt="logo">
        </a>
        <a class="desktop-logo logo-dark active" href="{{ route('index') }}">
            <img src="{{ asset('assets/img/brand/logo-white.png') }}" class="main-logo dark-theme" alt="logo">
        </a>
        <a class="logo-icon mobile-logo icon-light active" href="{{ route('index') }}">
            <img src="{{ asset('assets/img/brand/favicon.png') }}" class="logo-icon" alt="logo">
        </a>
        <a class="logo-icon mobile-logo icon-dark active" href="{{ route('index') }}">
            <img src="{{ asset('assets/img/brand/favicon-white.png') }}" class="logo-icon dark-theme" alt="logo">
        </a>
    </div>
    <div class="main-sidemenu">
        <div class="app-sidebar__user clearfix">
            <div class="dropdown user-pro-body">
                <div class="">
                    <img alt="user-img" class="avatar avatar-xl brround" src="{{ asset('assets/img/faces/6.jpg') }}">
                    <span class="avatar-status profile-status bg-green"></span>
                </div>
                <div class="user-info">
                    <h4 class="font-weight-semibold mt-3 mb-0">{{ Auth::user()->name }}</h4>
                    <span class="mb-0 text-muted">{{ auth()->user()->getRoleNames()->first() }}</span>
                </div>
            </div>
        </div>

        <ul class="side-menu">
            <!-- Main Section -->
            <li class="side-item side-item-category">{{ __('messages.main') }}</li>
            <li class="slide">
                <a class="side-menu__item" href="{{ route('index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none"/>
                        <path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/>
                        <path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/>
                    </svg>
                    <span class="side-menu__label">{{ __('messages.index') }}</span>
                    <span class="badge badge-success side-badge">1</span>
                </a>
            </li>

            <!-- General Section -->
            @canany(['view-bikes', 'view-rentals', 'view-maintenance', 'view-spare-parts', 'view-spare-part-sales', 'view-expenses', 'view-accounts'])
                <li class="side-item side-item-category">{{ __('messages.general') }}</li>

                <!-- Bikes -->
                @can('view-bikes')
                    <li class="slide">
                        <a class="side-menu__item" href="{{ route('bikes.index') }}">
                            <i class="side-menu__icon fas fa-bicycle"></i>
                            <span class="side-menu__label">{{ __('messages.bikes') }}</span>
                        </a>
                    </li>
                @endcan

                <!-- Rentals -->
                @can('view-rentals')
                    <li class="slide">
                        <a class="side-menu__item" href="{{ route('rentals.index') }}">
                            <i class="side-menu__icon fas fa-handshake"></i>
                            <span class="side-menu__label">{{ __('messages.rentals') }}</span>
                        </a>
                    </li>
                @endcan

                <!-- Maintenance -->
                @can('view-maintenance')
                    <li class="slide">
                        <a class="side-menu__item" href="{{ route('maintenance.index') }}">
                            <i class="side-menu__icon fas fa-tools"></i>
                            <span class="side-menu__label">{{ __('messages.maintenance') }}</span>
                        </a>
                    </li>
                @endcan

                <!-- Spare Parts -->
                @can('view-spare-parts')
                    <li class="slide">
                        <a class="side-menu__item" href="{{ route('spare-parts.index') }}">
                            <i class="side-menu__icon fas fa-tools"></i>
                            <span class="side-menu__label">{{ __('messages.spare_parts') }}</span>
                        </a>
                    </li>
                @endcan

                <!-- Spare Part Sales -->
                @can('view-spare-part-sales')
                    <li class="slide">
                        <a class="side-menu__item" href="{{ route('spare-part-sales.index') }}">
                            <i class="side-menu__icon fas fa-shopping-cart"></i>
                            <span class="side-menu__label">{{ __('messages.spare_part_sales') }}</span>
                        </a>
                    </li>
                @endcan

                <!-- Expenses -->
                @can('view-expenses')
                    <li class="slide">
                        <a class="side-menu__item" href="{{ route('expenses.index') }}">
                            <i class="side-menu__icon fas fa-money-bill-wave"></i>
                            <span class="side-menu__label">{{ __('messages.expenses') }}</span>
                        </a>
                    </li>
                @endcan

                <!-- Accounts -->
                @can('view-accounts')
                    <li class="slide">
                        <a class="side-menu__item" href="{{ route('accounts.index') }}">
                            <i class="side-menu__icon fas fa-wallet"></i>
                            <span class="side-menu__label">{{ __('messages.accounts') }}</span>
                        </a>
                    </li>
                @endcan
            @endcanany

            <!-- Users and Roles -->
            @role('superadmin')
                <li class="side-item side-item-category">{{ __('messages.users_and_roles') }}</li>
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="side-menu__icon fe fe-user fa-2x"></i>
                        <span class="side-menu__label">{{ __('messages.users') }}</span>
                        <i class="angle fe fe-chevron-down"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a class="slide-item" href="{{ route('users.index') }}">{{ __('messages.users') }}</a></li>
                        <li><a class="slide-item" href="{{ route('roles.index') }}">{{ __('messages.roles') }}</a></li>
                    </ul>
                </li>
            @endrole
        </ul>
    </div>
</aside>
<!-- main-sidebar -->
