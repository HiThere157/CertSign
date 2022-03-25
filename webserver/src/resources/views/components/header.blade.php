<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">CertSign</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link active" href="/">Overview</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Root CA</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Certificates</a>
                </li>

            </ul>
            @auth
                <div class="text-end">
                    <a type="button" class="btn btn-outline-light me-2">Settings</a>
                    <a href="logout" type="button" class="btn btn-warning">Logout</a>
                </div>
            @endauth
            @guest
                <div class="text-end">
                    <a href="register" type="button" class="btn btn-outline-light me-2">Sign-Up</a>
                    <a href="login" type="button" class="btn btn-warning">Login</a>
                </div>
            @endguest
        </div>
    </div>
</nav>