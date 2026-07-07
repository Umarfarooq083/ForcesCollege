<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Iconic Bootstrap 4.5.0 Admin Template">
        <meta name="author" content="WrapTheme, design by: ThemeMakker.com">
        <link rel="icon" href="/assets/images/Logo.jpeg" type="image/x-icon">
        <title inertia>{{ config('app.name', 'LMS') }}</title>

        <!-- Fonts -->
        <!-- <link rel="preconnect" href="https://fonts.bunny.net"> -->
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{url('assets/vendor/bootstrap/css/bootstrap.min.css')}}">
        <link rel="stylesheet" href="{{url('assets/vendor/font-awesome/css/font-awesome.min.css')}}">
        <link rel="stylesheet" href="{{url('assets/vendor/toastr/toastr.min.css')}}">
        <link rel="stylesheet" href="{{url('assets/vendor/charts-c3/plugin.css')}}"/>
        <link rel="stylesheet" href="{{url('assets/vendor/bootstrap-multiselect/bootstrap-multiselect.css')}}">

        
        <!-- MAIN Project CSS file -->
        <link rel="stylesheet" href="{{url('assets/css/main.css')}}">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        <!-- @vite(['resources/js/app.js']) -->
        @inertiaHead
    </head>
    <body data-theme="light"  class="font-nunito">
        @inertia
    </body>

    <script src="{{url('assets/bundles/libscripts.bundle.js')}}"></script>    
    <script src="{{url('assets/bundles/vendorscripts.bundle.js')}}"></script>

    <!-- page vendor js file -->
    <script src="{{url('assets/vendor/toastr/toastr.js')}}"></script>
    <script src="{{url('assets/bundles/c3.bundle.js')}}"></script>
    <script src="{{url('assets/vendor/bootstrap-multiselect/bootstrap-multiselect.js')}}"></script> <!-- Multi Select Plugin Js -->
    <!-- page js file -->
    <!-- <script src="{{url('assets/bundles/mainscripts.bundle.js')}}"></script> -->
    <!-- <script src="{{url('assets/js/index.js')}}"></script> -->


    <script>
        function formatCNIC(value) {
            value = String(value ?? '');
            const numeric = value.replace(/\D/g, '').slice(0, 13);
            const part1 = numeric.slice(0, 5);
            const part2 = numeric.slice(5, 12);
            const part3 = numeric.slice(12, 13);
            let formatted = part1;
            if (part2) formatted += '-' + part2;
            if (part3) formatted += '-' + part3;
            return formatted;
        }
    </script>    

</html> 
