<!DOCTYPE html>
<html lang="en">

<head>

  <base >
  <meta charset="utf-8">
  <meta name="theme-color" content="#316AFF">
  <meta name="robots" content="index, follow">
  <meta name="author" content="SGPME IT">
  <meta name="format-detection" content="telephone=no">
  <meta name="keywords" content="">
  <meta name="description" content="">

  <title>@yield('title', 'Connexion - SGPME')</title>

  <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('partials.styles')

    @yield('styles')
</head>

<body>
  <div class="page-layout">

    <div class="auth-frame-wrapper">
      <div class="row g-0 h-100">
        <div class="col-lg-6">
          <div class="auth-frame" style="background-image: url({{ asset('assets/images/auth/auth-frame.webp') }});">
            <div class="clearfix">
              <div class="auth-content">
                <h1 class="display-6 text-white fw-bold">AXIAL by SGPME</h1>
                <p class="text-white">Une <strong>équipe engagée</strong> pour atteindre des <strong>objectifs bien visés</strong>.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 align-self-center">
            @yield('content')
        </div>
      </div>
    </div>

  </div>

    @include('partials.scripts')

    @yield('scripts')

    @stack('scripts')
</body>
</html>
