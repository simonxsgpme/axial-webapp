<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#316AFF">
  <meta name="robots" content="index, follow">
  <meta name="author" content="SGPME IT">
  <meta name="keywords" content="">
  <meta name="description" content="">

  <title>@yield('title', 'AXIAL - SGPME')</title>

  <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('partials.styles')

    @yield('styles')

</head>

<body>
  <div class="page-layout">

    <header class="app-header">
      <div class="app-header-inner">
        <button class="app-toggler" type="button" aria-label="app toggler">
          <span></span>
          <span></span>
          <span></span>
        </button>
        <div class="app-header-end">
          <div class="vr my-3"></div>
          <div class="dropdown text-end ms-sm-3 ms-2 ms-lg-4">
            <a href="#" class="d-flex align-items-center py-2" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
              <div class="text-end me-2 d-none d-lg-inline-block">
                <div class="fw-bold text-dark">{{ Auth::user()->full_name }}</div>
                <small class="text-body d-block lh-sm">
                  <i class="fi fi-rr-angle-down text-3xs me-1"></i> {{ Auth::user()->role->name }}
                </small>
              </div>
              <div class="avatar avatar-sm rounded-circle avatar-status-success">
                <img src="/storage/{{ Auth::user()->avatar }}" alt="user avatar">
              </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end w-225px mt-1">
              <li class="d-flex align-items-center p-2">
                <div class="avatar avatar-sm rounded-circle">
                  <img src="/storage/{{ Auth::user()->avatar }}" alt="user avatar">
                </div>
                <div class="ms-2">
                  <div class="fw-bold text-dark">{{ Auth::user()->full_name }}</div>
                  <small class="text-body d-block lh-sm">{{ Auth::user()->email }}</small>
                </div>
              </li>
              <li>
                <div class="dropdown-divider my-1"></div>
              </li>
              <!--
              <li>
                <a class="dropdown-item d-flex align-items-center gap-2" href="">
                  <i class="fi fi-rr-note scale-1x"></i> My Task
                </a>
              </li>
              <li>
                <a class="dropdown-item d-flex align-items-center gap-2" href="pages/faq.html">
                  <i class="fi fi-rs-interrogation scale-1x"></i> Help Center
                </a>
              </li>
              <li>
                <a class="dropdown-item d-flex align-items-center gap-2" href="settings.html">
                  <i class="fi fi-rr-settings scale-1x"></i> Account Settings
                </a>
              </li>
              <li>
                <a class="dropdown-item d-flex align-items-center gap-2" href="pages/pricing.html">
                  <i class="fi fi-rr-usd-circle scale-1x"></i> Upgrade Plan
                </a>
              </li>
              <li>
                <div class="dropdown-divider my-1"></div>
              </li>
            -->
              <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                        <i class="fi fi-rr-cross scale-1x"></i> Me deconnecter
                    </button>
                </form>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </header>

    <aside class="app-menubar" id="appMenubar">
      <div class="app-navbar-brand">
        <a class="navbar-brand-logo" href="index.html" style="display: block; margin: 0 auto;">
          <img src="https://www.sgpme.ci/wp-content/uploads/2026/02/logo-150.png" width="80" alt="SGPME Logo">
        </a>
      </div>
      <nav class="app-navbar" data-simplebar>
        <ul class="menubar">
          <li class="menu-item">
            <a class="menu-link" href="{{ route('dashboard') }}">
              <i class="fi fi-rr-home"></i>
              <span class="menu-label">Accueil</span>
            </a>
          </li>
          <li class="menu-item {{ request()->routeIs('campaigns.*') ? 'active' : '' }}">
              <a class="menu-link {{ request()->routeIs('campaigns.*') ? 'active' : '' }}" href="{{ route('campaigns.index') }}">
                  <i class="fi fi-rr-folder"></i>
                  <span class="menu-label">Campagnes</span>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('objectives.*') ? 'active' : '' }}">
              <a class="menu-link {{ request()->routeIs('objectives.*') ? 'active' : '' }}" href="{{ route('objectives.index') }}">
                  <i class="fi fi-rr-bullseye"></i>
                  <span class="menu-label">Mes Objectifs</span>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('supervisor.objectives.*') ? 'active' : '' }}">
              <a class="menu-link {{ request()->routeIs('supervisor.objectives.*') ? 'active' : '' }}" href="{{ route('supervisor.objectives.index') }}">
                  <i class="fi fi-rr-check-double"></i>
                  <span class="menu-label">Validation Objectifs</span>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('evaluations.*') ? 'active' : '' }}">
              <a class="menu-link {{ request()->routeIs('evaluations.*') ? 'active' : '' }}" href="{{ route('evaluations.index') }}">
                  <i class="fi fi-rr-chart-histogram"></i>
                  <span class="menu-label">Mon Évaluation</span>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('supervisor.evaluations.*') ? 'active' : '' }}">
              <a class="menu-link {{ request()->routeIs('supervisor.evaluations.*') ? 'active' : '' }}" href="{{ route('supervisor.evaluations.index') }}">
                  <i class="fi fi-rr-star"></i>
                  <span class="menu-label">Évaluer Collaborateurs</span>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
              <a class="menu-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                  <i class="fi fi-rr-users"></i>
                  <span class="menu-label">Utilisateurs</span>
              </a>
          </li>
          <li class="menu-heading">
            <span class="menu-label">CONFIGURATIONS</span>
          </li>
            <li class="menu-item {{ request()->routeIs('settings.roles.*') ? 'active' : '' }}">
                <a class="menu-link {{ request()->routeIs('settings.roles.*') ? 'active' : '' }}" href="{{ route('settings.roles.index') }}">
                    <i class="fi fi-rr-shield"></i>
                    <span class="menu-label">Rôles</span>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('settings.permissions.*') ? 'active' : '' }}">
                <a class="menu-link {{ request()->routeIs('settings.permissions.*') ? 'active' : '' }}" href="{{ route('settings.permissions.index') }}">
                    <i class="fi fi-rr-key"></i>
                    <span class="menu-label">Permissions</span>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('settings.entities.*') ? 'active' : '' }}">
                <a class="menu-link {{ request()->routeIs('settings.entities.*') ? 'active' : '' }}" href="{{ route('settings.entities.index') }}">
                    <i class="fi fi-rr-building"></i>
                    <span class="menu-label">Entités</span>
                </a>
            </li>
        </ul>
      </nav>
    </aside>

    <main class="app-wrapper">

      <div class="container">

        @yield('content')

      </div>

    </main>

    <footer class="footer-wrapper bg-body">
      <div class="container">
        <div class="row g-2">
          <div class="col-lg-6 col-md-7 text-center text-md-start">
            <p class="mb-0">© <span class="currentYear">{{ date('Y') }}</span> AXIAL. powered by <a href="javascript:void(0);">SGPME IT</a>.</p>
          </div>
        </div>
      </div>
    </footer>
    <!-- end::GXON Footer -->

  </div>


    @include('partials.scripts')

    @yield('scripts')

    @stack('scripts')
</body>

</html>
