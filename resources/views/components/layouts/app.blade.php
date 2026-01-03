<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Modul Keluhan Chat CS</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('lte/plugins/fontawesome-free/css/all.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet" href="{{asset('lte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{asset('lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{asset('lte/plugins/jqvmap/jqvmap.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('lte/dist/css/adminlte.min.css')}}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{asset('lte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{asset('lte/plugins/daterangepicker/daterangepicker.css')}}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{asset('lte/plugins/summernote/summernote-bs4.css')}}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('lte/plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
    
    <!-- Custom CSS for Sidebar Hover & Active -->
    <style>
        /* Sidebar nav link hover effect */
        .nav-sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        /* Active state for nav links */
        .nav-sidebar .nav-link.active {
            background-color: #007bff !important;
            color: #fff !important;
        }
        
        .nav-sidebar .nav-link.active .nav-icon {
            color: #fff !important;
        }
        
        /* Ensure hover works on non-active items */
        .sidebar-dark-primary .nav-sidebar .nav-link:not(.active):hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: #c2c7d0 !important;
        }
        
        /* Ensure proper spacing for content */
        .content-wrapper > .content:first-child {
            padding-top: 1rem;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <x-navbar/>

        <!-- Main Sidebar Container -->
        <x-sidebar/>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper" style="min-height: auto !important;">

            <!-- Content Header (Page header) -->
            @isset($header)
            <div class="content-header">
                <div class="container-fluid">
                    {{ $header }}
                </div>
            </div>
            @endisset

            {{ $slot }}
        </div>

        <!-- /.content-wrapper -->
        <!-- <footer class="main-footer">
            <strong>Copyright &copy; 2014-2019 <a href="http://adminlte.io">AdminLTE.io</a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 3.0.0
            </div>
        </footer> -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{asset('lte/plugins/jquery/jquery.min.js')}}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{asset('lte/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{{asset('lte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <!-- DataTables -->
    <script src="{{asset('lte/plugins/datatables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('lte/plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}"></script>
    <!-- ChartJS -->
    <script src="{{asset('lte/plugins/chart.js/Chart.min.js')}}"></script>
    <!-- Sparkline -->
    <script src="{{asset('lte/plugins/sparklines/sparkline.js')}}"></script>
    <!-- JQVMap -->
    <script src="{{asset('lte/plugins/jqvmap/jquery.vmap.min.js')}}"></script>
    <script src="{{asset('lte/plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
    <!-- jQuery Knob Chart -->
    <script src="{{asset('lte/plugins/jquery-knob/jquery.knob.min.js')}}"></script>
    <!-- daterangepicker -->
    <script src="{{asset('lte/plugins/moment/moment.min.js')}}"></script>
    <script src="{{asset('lte/plugins/daterangepicker/daterangepicker.js')}}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{asset('lte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
    <!-- Summernote -->
    <script src="{{asset('lte/plugins/summernote/summernote-bs4.min.js')}}"></script>
    <!-- overlayScrollbars -->
    <script src="{{asset('lte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
    <!-- AdminLTE App -->
    <script src="{{asset('lte/dist/js/adminlte.js')}}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{asset('lte/dist/js/pages/dashboard.js')}}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{asset('lte/dist/js/demo.js')}}"></script>
    
    <!-- Pusher & Laravel Echo for WebSocket -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.iife.js"></script>
    
    <script>
        // Initialize Echo for WebSocket real-time communication
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ config('broadcasting.connections.pusher.key') }}',
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            wsHost: window.location.hostname,
            wsPort: {{ config('broadcasting.connections.pusher.options.port', 6001) }},
            wssPort: {{ config('broadcasting.connections.pusher.options.port', 6001) }},
            forceTLS: false,
            encrypted: true,
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
        });
        
        console.log('Echo initialized for admin/CS');
    </script>

</body>

</html>
