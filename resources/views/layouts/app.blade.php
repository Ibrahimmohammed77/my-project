<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>@yield('title', 'Dashboard') - Tabler - Premium and Open Source dashboard template with responsive and high quality UI.</title>
    <!-- CSS files -->
    <link href="{{ asset('assets/tabler/css/tabler.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/tabler/css/tabler-flags.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/tabler/css/tabler-payments.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/tabler/css/tabler-vendors.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/tabler/css/demo.min.css') }}" rel="stylesheet"/>
    <style>
      @import url('https://rsms.me/inter/inter.css');
      :root {
      	--tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
      }
      body {
      	font-feature-settings: "cv03", "cv04", "cv11";
      }
    </style>
    @stack('styles')
  </head>
  <body>
    <script src="{{ asset('assets/tabler/js/demo-theme.min.js') }}"></script>
    <div class="page">
      <!-- Navbar -->
      @include('layouts.partials.header')
      @include('layouts.partials.navbar')
      
      <div class="page-wrapper">
        <!-- Page header -->
        @hasSection('header')
        <div class="page-header d-print-none">
          <div class="container-xl">
             @yield('header')
          </div>
        </div>
        @endif

        <!-- Page body -->
        <div class="page-body">
          <div class="container-xl">
            @yield('content')
          </div>
        </div>
        
        @include('layouts.partials.footer')
      </div>
    </div>
    <!-- Libs JS -->
    <!-- Tabler Core -->
    <script src="{{ asset('assets/tabler/js/tabler.min.js') }}" defer></script>
    <script src="{{ asset('assets/tabler/js/demo.min.js') }}" defer></script>
    @stack('scripts')
  </body>
</html>
