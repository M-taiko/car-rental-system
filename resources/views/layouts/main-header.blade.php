<!-- main-header opened -->
<div class="main-header sticky side-header nav nav-item">
    <div class="container-fluid">
        <div class="main-header-left">
            <div class="responsive-logo">
                <a href="{{ url('/') }}">
                    <img src="{{ isset($settings['company_logo']) ? asset('storage/settings/' . $settings['company_logo']) : asset('assets/img/brand/logo.png') }}" alt="Company Logo" class="logo-1">
                </a>
            </div>
            <div class="app-sidebar__toggle" data-toggle="sidebar">
                <a class="open-toggle" href="#"><i class="header-icon fe fe-align-left"></i></a>
                <a class="close-toggle" href="#"><i class="header-icons fe fe-x"></i></a>
            </div>
        </div>

        <div class="main-header-right">
            <!-- Language Switcher -->
            <ul class="nav">
                <li class="">
                    <div class="dropdown nav-item">
                        <a href="#" class="d-flex nav-item nav-link pl-0 country-flag1" data-toggle="dropdown" aria-expanded="false">
                            <span class="avatar country-Flag mr-0 align-self-center bg-transparent">
                                @php
                                    $locale = app()->getLocale();
                                    $flag = 'us_flag.jpg';
                                    $langName = __('English');
                                    if ($locale === 'ar') {
                                        $flag = 'saudi_flag.jpg';
                                        $langName = __('العربية');
                                    } elseif ($locale === 'zh') {
                                        $flag = 'china_flag.jpg';
                                        $langName = __('简体中文');
                                    }
                                @endphp
                                <img src="{{ URL::asset('assets/img/flags/' . $flag) }}" alt="Language Flag">
                            </span>
                            <div class="my-auto">
                                <strong class="mr-2 ml-2 my-auto">{{ $langName }}</strong>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-left dropdown-menu-arrow" x-placement="bottom-end">
                            <a href="{{ route('language.switch', 'en') }}" class="dropdown-item d-flex">
                                <span class="avatar ml-3 align-self-center bg-transparent">
                                    <img src="{{ URL::asset('assets/img/flags/us_flag.jpg') }}" alt="English" width="20">
                                </span>
                                <div class="d-flex">
                                    <span class="mt-2">English</span>
                                </div>
                            </a>
                            <a href="{{ route('language.switch', 'ar') }}" class="dropdown-item d-flex">
                                <span class="avatar ml-3 align-self-center bg-transparent">
                                    <img src="{{ URL::asset('assets/img/flags/saudi_flag.jpg') }}" alt="Arabic" width="20">
                                </span>
                                <div class="d-flex">
                                    <span class="mt-2">العربية</span>
                                </div>
                            </a>
                            <a href="{{ route('language.switch', 'zh') }}" class="dropdown-item d-flex">
                                <span class="avatar ml-3 align-self-center bg-transparent">
                                    <img src="{{ URL::asset('assets/img/flags/china_flag.jpg') }}" alt="Chinese" width="20">
                                </span>
                                <div class="d-flex">
                                    <span class="mt-2">简体中文</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </li>
            </ul>

            <!-- Search + Fullscreen + Profile -->
            <div class="nav nav-item navbar-nav-right ml-auto">
				

                <!-- Fullscreen Button -->
                <div class="nav-item full-screen fullscreen-button">
                    <a class="new nav-link full-screen-link" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-maximize">
                            <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
                        </svg>
                    </a>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown main-profile-menu nav nav-item nav-link">
                    <a class="profile-user d-flex" href="{{ route('profile.index') }}">
                        <img alt="{{ Auth::user()->name }}" class="rounded-circle"
                             src="{{ Auth::user()->profile_photo_path ? asset('settings/' . Auth::user()->profile_photo_path) : asset('assets/img/brand/user.png') }}"
                             style="width: 40px; height: 40px; object-fit: cover;">
                    </a>
                    <div class="dropdown-menu">
                        <div class="main-header-profile bg-primary p-3">
                            <div class="d-flex wd-100p">
                                <div class="main-img-user">
                                    <img alt="{{ Auth::user()->name }}" class="rounded-circle"
                                         src="{{ Auth::user()->profile_photo_path ? asset('settings/' . Auth::user()->profile_photo_path) : asset('assets/img/brand/user.png') }}"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                </div>
                                <div class="mr-3 my-auto">
                                    <h6>{{ Auth::user()->name }}</h6>
                                    <span>{{ Auth::user()->getRoleNames()->first() }}</span>
                                </div>
                            </div>
                        </div>
                        <a class="dropdown-item" href="{{ route('profile.index') }}">
                            <i class="bx bx-user-circle"></i> {{ __('Profile') }}
                        </a>
                        <!-- Language Switcher inside profile dropdown -->
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('language.switch', 'en') }}" class="dropdown-item d-flex align-items-center">
                            <img src="{{ URL::asset('assets/img/flags/us_flag.jpg') }}" alt="English" width="20" class="mr-2"> English
                        </a>
                        <a href="{{ route('language.switch', 'ar') }}" class="dropdown-item d-flex align-items-center">
                            <img src="{{ URL::asset('assets/img/flags/saudi_flag.jpg') }}" alt="Arabic" width="20" class="mr-2"> العربية
                        </a>
                        <a href="{{ route('language.switch', 'zh') }}" class="dropdown-item d-flex align-items-center">
                            <img src="{{ URL::asset('assets/img/flags/china_flag.jpg') }}" alt="Chinese" width="20" class="mr-2"> 简体中文
                        </a>
                        <div class="dropdown-divider"></div>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                        <button type="submit" class="dropdown-item" form="logout-form">
                            <i class="bx bx-log-out"></i> {{ __('Sign Out') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /main-header -->