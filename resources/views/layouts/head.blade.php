<!-- Title -->
<title>@yield('title') </title>
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Favicon -->
<link rel="icon" href="{{URL::asset('assets/img/brand/favicon.ico')}}" type="image/x-icon"/>

<!-- Font Awesome CSS -->
<link href="{{URL::asset('assets/plugins/fontawesome-free/css/all.min.css')}}" rel="stylesheet">

<!-- Icons css (removed as Font Awesome is directly linked) -->
<!-- <link href="{{URL::asset('assets/css/icons.css')}}" rel="stylesheet"> -->

<!--  Custom Scroll bar-->
<link href="{{URL::asset('assets/plugins/mscrollbar/jquery.mCustomScrollbar.css')}}" rel="stylesheet"/>
<!--  Sidebar css -->
<link href="{{URL::asset('assets/plugins/sidebar/sidebar.css')}}" rel="stylesheet">
<!-- Sidemenu css -->
<link rel="stylesheet" href="{{URL::asset('assets/css-rtl/sidemenu.css')}}">

@yield('css')
<!--- Style css -->
<link href="{{URL::asset('assets/css-rtl/style.css')}}" rel="stylesheet">
<!--- Dark-mode css -->
<link href="{{URL::asset('assets/css-rtl/style-dark.css')}}" rel="stylesheet">
<!---Skinmodes css-->
<link href="{{URL::asset('assets/css-rtl/skin-modes.css')}}" rel="stylesheet">
<!---Custom css-->
<link href="{{URL::asset('assets/css/custom.css')}}" rel="stylesheet">

<!-- Animations css -->
<link href="{{URL::asset('assets/css-rtl/animate.css')}}" rel="stylesheet">




