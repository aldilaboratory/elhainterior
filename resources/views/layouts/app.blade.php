<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Elha Interior') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
	    <link href="https://fonts.googleapis.com/css?family=Poppins:200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">

        <!-- Favicon -->
	    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
	
        <!-- Bootstrap -->
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
        <!-- Magnific Popup -->
        <link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.min.css') }}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.css') }}">
        <!-- JQuery UI -->
        <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
        <!-- Themify Icons -->
        <link rel="stylesheet" href="{{ asset('assets/css/themify-icons.css') }}">
        <!-- Nice Select CSS -->
        <link rel="stylesheet" href="{{ asset('assets/css/nice-select.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/niceselect.css') }}">
        <!-- Animate CSS -->
        <link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}">
        <!-- Flex Slider CSS -->
        <link rel="stylesheet" href="{{ asset('assets/css/flex-slider.min.css') }}">
        <!-- Owl Carousel -->
        <link rel="stylesheet" href="{{ asset('assets/css/owl-carousel.css') }}">
        <!-- Slicknav -->
        <link rel="stylesheet" href="{{ asset('assets/css/slicknav.min.css') }}">
        
        <!-- Eshop StyleSheet -->
        <link rel="stylesheet" href="{{ asset('assets/css/reset.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">

        {{-- SweetAlert --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Scripts -->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    </head>
    <body class="js">
        <!-- Header -->
        @include('layouts.navigation')

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

        <!-- Footer -->
        @include('layouts.footer')

    	<!-- Jquery -->
        <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/js/jquery-migrate-3.0.0.js') }}"></script>
        <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
        <!-- Popper JS -->
        <script src="{{ asset('assets/js/popper.min.js') }}"></script>
        <!-- Bootstrap JS -->
        <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
        <!-- Color JS -->
        <script src="{{ asset('assets/js/colors.js') }}"></script>
        <!-- Slicknav JS -->
        <script src="{{ asset('assets/js/slicknav.min.js') }}"></script>
        <!-- Owl Carousel JS -->
        <script src="{{ asset('assets/js/owl-carousel.js') }}"></script>
        <!-- Magnific Popup JS -->
        <script src="{{ asset('assets/js/magnific-popup.js') }}"></script>
        <!-- Fancybox JS -->
        <script src="{{ asset('assets/js/facnybox.min.js') }}"></script>
        <!-- Map -->
        <script src="{{ asset('assets/js/gmap.min.js') }}"></script>
        <script src="{{ asset('assets/js/map-script.js') }}"></script>
        <!-- Waypoints JS -->
        <script src="{{ asset('assets/js/waypoints.min.js') }}"></script>
        <!-- Countdown JS -->
        <script src="{{ asset('assets/js/finalcountdown.min.js') }}"></script>
        <!-- Nice Select JS -->
        <script src="{{ asset('assets/js/nicesellect.js') }}"></script>
        <!-- Ytplayer JS -->
        <script src="{{ asset('assets/js/ytplayer.min.js') }}"></script>
        <!-- Flex Slider JS -->
        <script src="{{ asset('assets/js/flex-slider.js') }}"></script>
        <!-- ScrollUp JS -->
        <script src="{{ asset('assets/js/scrollup.js') }}"></script>
        <!-- Onepage Nav JS -->
        <script src="{{ asset('assets/js/onepage-nav.min.js') }}"></script>
        <!-- Easing JS -->
        <script src="{{ asset('assets/js/easing.js') }}"></script>
        <!-- Active JS -->
        <script src="{{ asset('assets/js/active.js') }}"></script>
        {{-- SweetAlert --}}
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    text: '{{ session('success') }}',
                    timer: 5000,
                    timerProgressBar: false,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top',
                    background: '#E6FFE6',
                });
            @endif
            
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    text: '{{ session('error') }}',
                    timer: 5000,
                    timerProgressBar: false,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top',
                    background: '#ffefea',
                });
            @endif
        });
        </script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-btn').forEach(button => {
          button.addEventListener('click', function (e) {
            e.preventDefault(); // aman karena type="button" (tidak submit)
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');

            Swal.fire({
              title: 'Apakah Anda yakin?',
              text: `Data "${name}" akan dihapus!`,
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Ya, hapus!',
              cancelButtonText: 'Batal'
            }).then((result) => {
              if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
              }
            });
          });
        });
      });
    </script>
    </body>
</html>
