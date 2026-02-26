<header class="app-header">
    <div class="header-wrapper">

        <!-- Left: Toggler + Breadcrumb -->
        <div class="header-left d-flex align-items-center gap-3">
            <button type="button" class="app-toggler btn btn-icon waves-effect waves-light">
                <i class="fi fi-rr-menu-burger"></i>
            </button>
            <h6 class="mb-0 fw-semibold d-none d-sm-block">@yield('page-title', 'Tableau de bord')</h6>
        </div>

        <!-- Right: Actions -->
        <div class="header-right d-flex align-items-center gap-2">

            <!-- Theme Switcher -->
            <div class="dropdown">
                <button class="btn btn-icon btn-ghost-secondary rounded-circle" type="button" id="ld-theme" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fi fi-rr-sun theme-icon-active"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <button type="button" class="dropdown-item" data-bs-theme-value="light">
                            <i class="fi fi-rr-sun me-2"></i> Clair
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item" data-bs-theme-value="dark">
                            <i class="fi fi-rr-moon me-2"></i> Sombre
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item" data-bs-theme-value="auto">
                            <i class="fi fi-rr-computer me-2"></i> Auto
                        </button>
                    </li>
                </ul>
            </div>

            <!-- User Dropdown -->
            <div class="dropdown">
                <button class="btn btn-ghost-secondary rounded-pill d-flex align-items-center gap-2 px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar avatar-sm">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="rounded-circle">
                        @else
                            <span class="avatar-text rounded-circle bg-primary text-white">
                                {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    <span class="d-none d-md-inline-block fw-medium">{{ Auth::user()->full_name }}</span>
                    <i class="fi fi-rr-angle-small-down"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="px-3 py-2">
                        <h6 class="mb-0">{{ Auth::user()->full_name }}</h6>
                        <small class="text-muted">{{ Auth::user()->email }}</small>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);">
                            <i class="fi fi-rr-user me-2"></i> Mon Profil
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);">
                            <i class="fi fi-rr-settings me-2"></i> Paramètres
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fi fi-rr-sign-out-alt me-2"></i> Déconnexion
                            </button>
                        </form>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</header>
