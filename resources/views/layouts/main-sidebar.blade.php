@php
    use App\Models\Setting;
@endphp
<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
    <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active" href="{{ route('dashboard') }}">
            @php
                $logo = Setting::get('company_logo');
            @endphp

            <img src="{{ $logo ? asset('settings/' . $logo) : asset('assets/img/brand/logo.png') }}" style="width: 100px; height: 100px; margin-top: -20px;" alt="Company Logo" class="logo-1">
            </a>
        <a class="desktop-logo logo-dark active" href="{{ route('dashboard') }}">
            <img src="{{ $logo ? asset('settings/' . $logo) : asset('assets/img/brand/logo-white.png') }}" alt="Company Logo" class="logo-1">
        </a>
        <a class="logo-icon mobile-logo icon-light active" href="{{ route('dashboard') }}">
            <img src="{{ $logo ? asset('settings/' . $logo) : asset('assets/img/brand/favicon.png') }}" alt="Company Logo" class="logo-1">
        </a>
        <a class="logo-icon mobile-logo icon-dark active" href="{{ route('dashboard') }}">
            <img src="{{ $logo ? asset('settings/' . $logo) : asset('assets/img/brand/favicon-white.png') }}" alt="Company Logo" class="logo-1">
        </a>
    </div>
    <div class="main-sidemenu">
        <div class="app-sidebar__user clearfix">
            <div class="dropdown user-pro-body">
                <div class="">
                    <img alt="{{ Auth::user()->name }}" class="avatar avatar-xl brround rounded-circle" src="{{ Auth::user()->profile_photo_path ? asset('settings/' . Auth::user()->profile_photo_path) : asset('assets/img/brand/user.png') }}" style="width: 60px; height: 60px; object-fit: cover;">
                    <span class="avatar-status profile-status bg-green"></span>
                </div>
                <div class="user-info">
                    <h4 class="font-weight-semibold mt-3 mb-0">{{ Auth::user()->name }}</h4>
                    <span class="mb-0 text-muted">{{ auth()->user()->getRoleNames()->first() }}</span>
                </div>
            </div>
        </div>

        <ul class="side-menu">
            <!-- Profile Section -->
            <!-- <li class="slide">
                <a class="side-menu__item" href="{{ route('profile.index') }}">
                    <i class="side-menu__icon fas fa-user"></i>
                    <span class="side-menu__label">{{ __('messages.profile') }}</span>
                </a>
            </li> -->

            <!-- Dashboard Section -->
            <li class="side-item side-item-category">{{ __('messages.main_navigation') }}</li>
            <li class="slide">
                <a class="side-menu__item" href="{{ route('dashboard') }}">
                    <i class="side-menu__icon fas fa-tachometer-alt"></i>
                    <span class="side-menu__label">{{ __('messages.dashboard') }}</span>
                </a>
            </li>



            <!-- Car Rental Management -->
            <li class="side-item side-item-category">{{ __('messages.vehicle_management') }}</li>

            <!-- Cars -->
            <li class="slide">
                <a class="side-menu__item" href="{{ route('cars.index') }}">
                    <i class="side-menu__icon fas fa-car"></i>
                    <span class="side-menu__label">{{ __('messages.cars') }}</span>
                </a>
            </li>

            <!-- Drivers -->
            <li class="slide">
                <a class="side-menu__item" href="{{ route('driver.index') }}">
                    <i class="side-menu__icon fas fa-id-card"></i>
                    <span class="side-menu__label">{{ __('messages.drivers') }}</span>
                </a>
            </li>

            <!-- Customers -->
            <li class="slide">
                <a class="side-menu__item" href="{{ route('customers.index') }}">
                    <i class="side-menu__icon fas fa-users"></i>
                    <span class="side-menu__label">{{ __('messages.customers') }}</span>
                </a>
            </li>

            <!-- Rentals -->
            <li class="slide">
                <a class="side-menu__item" href="{{ route('rentals.index') }}">
                    <i class="side-menu__icon fas fa-handshake"></i>
                    <span class="side-menu__label">{{ __('messages.rentals') }}</span>
                </a>
            </li>

            <!-- Routes -->
            <li class="slide">
                <a class="side-menu__item" href="{{ route('routes.index') }}">
                    <i class="side-menu__icon fas fa-route"></i>
                    <span class="side-menu__label">{{ __('messages.routes') }}</span>
                </a>
            </li>

            <!-- Financial Management -->
            <li class="side-item side-item-category">{{ __('messages.financial_management') }}</li>

            <!-- Accounts -->
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon fas fa-wallet"></i>
                    <span class="side-menu__label">{{ __('messages.accounts') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('accounts.index') }}">{{ __('messages.all_transactions') }}</a></li>
                    <!-- <li><a class="slide-item" href="{{ route('accounts.income') }}">{{ __('messages.income') }}</a></li>
                    <li><a class="slide-item" href="{{ route('accounts.expenses') }}">{{ __('messages.expenses') }}</a></li> -->
                    <!-- <li><a class="slide-item" href="{{ route('accounts.totals') }}">{{ __('messages.totals') }}</a></li> -->
                </ul>
            </li>


            <!-- Maintenance Section -->
            <!-- <li class="side-item side-item-category">{{ __('messages.maintenance_management') }}</li>
               -->

            <!-- Maintenance -->
            <!-- <li class="slide">
                <a class="side-menu__item" href="{{ route('maintenance.index') }}">
                    <i class="side-menu__icon fas fa-tools"></i>
                    <span class="side-menu__label">{{ __('messages.maintenance_records') }}</span>
                </a>
            </li>  -->

            <!-- Spare Parts -->
            <!-- <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon fas fa-cogs"></i>
                    <span class="side-menu__label">{{ __('messages.spare_parts') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('spare-parts.index') }}">{{ __('messages.spare_parts_inventory') }}</a></li>
                    <li><a class="slide-item" href="{{ route('spare-part-sales.index') }}">{{ __('messages.spare_parts_sales') }}</a></li>
                    <li><a class="slide-item" href="{{ route('maintenance.spare_parts_profit_report') }}">{{ __('messages.profit_report') }}</a></li>
                </ul>
            </li> -->

            <!-- Settings & Administration -->
            <li class="side-item side-item-category">{{ __('messages.system_settings') }}</li>

            <!-- Users and Roles -->

            <!-- Reports Section -->
            <!-- <li class="side-item side-item-category">{{ __('messages.reports') }}</li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon fas fa-chart-bar"></i>
                    <span class="side-menu__label">{{ __('messages.reports') }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item {{ request()->routeIs('reports.rentals') ? 'active' : '' }}" href="{{ route('reports.rentals') }}">{{ __('messages.rental_reports') }}</a></li>
                    <li><a class="slide-item {{ request()->routeIs('reports.third-party-cars') ? 'active' : '' }}" href="{{ route('reports.third-party-cars') }}">{{ __('messages.third_party_cars_reports') }}</a></li>
                    <li><a class="slide-item {{ request()->routeIs('reports.car-types') ? 'active' : '' }}" href="{{ route('reports.car-types') }}">{{ __('messages.car_types_reports') }}</a></li>
                    <li><a class="slide-item {{ request()->routeIs('reports.monthly-revenue') ? 'active' : '' }}" href="{{ route('reports.monthly-revenue') }}">{{ __('messages.monthly_revenue_reports') }}</a></li>
                </ul>
            </li> -->

            <!-- System Settings -->
            <li class="slide">
                <a class="side-menu__item" href="{{ route('setting.index') }}">
                    <i class="side-menu__icon fas fa-cog"></i>
                    <span class="side-menu__label">{{ __('messages.system_settings') }}</span>
                </a>
            </li>
            @role('superadmin')

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon fas fa-users-cog"></i>
                    <span class="side-menu__label">{{ __('messages.users_and_roles') }}</span>
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
