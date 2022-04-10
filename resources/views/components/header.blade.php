<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">CertSign</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ route('home') }}">Overview</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('certificates') ? 'active' : '' }}" href="{{ route('certificates') }}">Certificates</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('certificates/deleted') ? 'active' : '' }}" href="{{ route('certificates.deleted') }}">Restore</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('logs') ? 'active' : '' }}" href="{{ route('logs') }}">Logs</a>
                </li>

            </ul>
            @auth
                <div class="text-end">
                    <span class="btn disabled btn-outline-light me-2">
                        Hi, {{ Auth::user()->username }}!
                    </span>

                    @can('isAdmin')
                        <a href="{{ route('settings') }}" type="button" class="btn me-2 {{ request()->is('settings') ? 'btn-light' : 'btn-outline-light' }}">Settings</a>
                    @endcan
                    <a href="{{ route('logout') }}" type="button" class="btn btn-warning">Logout</a>
                </div>
            @endauth
            @guest
                <div class="text-end">
                    <a href="{{ route('register') }}" type="button" class="btn btn-outline-light me-2">Sign-Up</a>
                    <a href="{{ route('login') }}" type="button" class="btn btn-warning">Login</a>
                </div>
            @endguest
        </div>
    </div>
</nav>
