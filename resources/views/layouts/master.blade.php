<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="Keywords" content="admin,admin dashboard,admin dashboard template,admin panel template,admin template,admin theme,bootstrap 4 admin template,bootstrap 4 dashboard,bootstrap admin,bootstrap admin dashboard,bootstrap admin panel,bootstrap admin template,bootstrap admin theme,bootstrap dashboard,bootstrap form template,bootstrap panel,bootstrap ui kit,dashboard bootstrap 4,dashboard design,dashboard html,dashboard template,dashboard ui kit,envato templates,flat ui,html,html and css templates,html dashboard template,html5,jquery html,premium,premium quality,sidebar bootstrap 4,template admin bootstrap 4"/>
    @include('layouts.head')
</head>

<body class="main-body app sidebar-mini">
    <!-- Loader -->
    <div id="global-loader">
        <img src="{{URL::asset('assets/img/loader.svg')}}" class="loader-img" alt="Loader">
    </div>
    <!-- /Loader -->

    <!-- Check if user has 'superadmin' role to show the sidebar -->

        @include('layouts.main-sidebar')
        <!-- If superadmin, keep the content layout normal -->
        <div class="main-content app-content">

      
        <div class="main-content app-content" style="margin-left: 0; width: 85%;  display: contents;">


    @include('layouts.main-header')

    <!-- container -->
    <div class="container-fluid">
        @yield('page-header')
        <div class="master-content-card card p-3 mt-2">
        @yield('content')
    </div>

        <!-- Only include sidebar and models if the user is superadmin -->

            @include('layouts.sidebar')


        @include('layouts.models')
    </div>
    <!-- /container -->

    @include('layouts.settings')
    @include('layouts.footer')
    @include('layouts.footer-scripts')

</body>
</html>
