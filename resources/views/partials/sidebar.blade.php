<aside class="app-sidebar" id="appMenubar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <img src="{{ asset('assets/images/logo.svg') }}" alt="AXIAL" class="logo-lg" height="28">
            <img src="{{ asset('assets/images/logo.svg') }}" alt="AXIAL" class="logo-sm" height="28">
        </a>
    </div>

    <div class="sidebar-content" data-simplebar>
        <nav class="app-navbar">
            <ul class="menubar">

                <!-- Dashboard -->
                <li class="menu-label">Menu Principal</li>
                <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="waves-effect waves-light">
                        <i class="fi fi-rr-home menu-icon"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>

                <!-- Objectifs & Évaluations -->
                <li class="menu-label">Objectifs & Évaluations</li>
                <li class="menu-arrow">
                    <a href="javascript:void(0);" class="waves-effect waves-light">
                        <i class="fi fi-rr-bullseye-arrow menu-icon"></i>
                        <span>Objectifs</span>
                        <i class="fi fi-rr-angle-right arrow-icon"></i>
                    </a>
                    <ul class="menu-inner">
                        <li><a href="javascript:void(0);">Mes Objectifs</a></li>
                        <li><a href="javascript:void(0);">Objectifs Équipe</a></li>
                    </ul>
                </li>
                <li class="menu-arrow">
                    <a href="javascript:void(0);" class="waves-effect waves-light">
                        <i class="fi fi-rr-chart-histogram menu-icon"></i>
                        <span>Évaluations</span>
                        <i class="fi fi-rr-angle-right arrow-icon"></i>
                    </a>
                    <ul class="menu-inner">
                        <li><a href="javascript:void(0);">Mes Évaluations</a></li>
                        <li><a href="javascript:void(0);">Évaluer Équipe</a></li>
                    </ul>
                </li>

                <!-- Administration -->
                <li class="menu-label">Administration</li>
                <li class="menu-arrow">
                    <a href="javascript:void(0);" class="waves-effect waves-light">
                        <i class="fi fi-rr-users menu-icon"></i>
                        <span>Utilisateurs</span>
                        <i class="fi fi-rr-angle-right arrow-icon"></i>
                    </a>
                    <ul class="menu-inner">
                        <li><a href="javascript:void(0);">Liste</a></li>
                        <li><a href="javascript:void(0);">Rôles</a></li>
                    </ul>
                </li>

            </ul>
        </nav>
    </div>
</aside>
