@php $user = Auth::user(); @endphp
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Carteira Financeira - Dashboard</title>
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/carteira-custom.css') }}" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="{{ asset('assets/select2/dist/css/select2.min.css') }}" rel="stylesheet">
    @stack('head')
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <x-sidebar />
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <x-navbar />
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <x-responses />
                    @yield('content')
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Modal Configurações -->
            <x-user />

            <!-- jQuery sempre primeiro -->
            <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>

            <!-- DataTables e Select2 dependem de jQuery -->
            <script src="{{ asset('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
            <script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
            <script src="{{ asset('assets/select2/dist/js/select2.min.js') }}"></script>
            <!-- jQuery Mask Plugin -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

            <!-- Bootstrap depende de jQuery -->
            <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

            <!-- Outros plugins -->
            <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
            <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>

            @stack('scripts')
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

</body>

</html>
