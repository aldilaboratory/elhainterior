<nav class="navbar navbar-expand-sm navbar-light bg-white border-bottom">
    <div class="container-fluid" style="max-width: 80rem;">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <x-application-logo class="text-dark" style="height: 2.25rem; width: auto;" />
        </a>

        <!-- Navigation Links (Desktop) -->
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                       href="{{ route('dashboard') }}">
                        {{ __('Dashboard') }}
                    </a>
                </li>
            </ul>

            <!-- Settings Dropdown (Desktop) -->
            <div class="navbar-nav">
                <div class="nav-item dropdown">
                    <button class="btn btn-link nav-link dropdown-toggle text-secondary border-0 bg-transparent" 
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                {{ __('Profile') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>

    <!-- Mobile Navigation -->
    <div class="collapse navbar-collapse d-sm-none" id="navbarNav">
        <!-- Mobile Navigation Links -->
        <div class="navbar-nav p-3 border-top">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
               href="{{ route('dashboard') }}">
                {{ __('Dashboard') }}
            </a>
        </div>

        <!-- Mobile User Info -->
        <div class="border-top pt-3 pb-1">
            <div class="px-3 mb-3">
                <div class="fw-medium text-dark">{{ Auth::user()->name }}</div>
                <div class="text-muted small">{{ Auth::user()->email }}</div>
            </div>

            <div class="navbar-nav px-3">
                <a class="nav-link" href="{{ route('profile.edit') }}">
                    {{ __('Profile') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link btn btn-link text-start p-0 border-0">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>