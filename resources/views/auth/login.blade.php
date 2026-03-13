@extends('layouts.auth')

@section('content')
<div class="p-4 p-sm-5 maxw-450px m-auto auth-inner" data-simplebar>
    <div class="mb-4 text-center">
        <a href="/" aria-label="AXIAL logo">
            <img class="visible-light" src="https://www.sgpme.ci/wp-content/uploads/2026/02/logo-150.png" width="120" alt="AXIAL logo">
        </a>
    </div>
    <div class="text-center mb-5">
        <h5 class="mb-1">Bienvenue sur AXIAL</h5>
        <p>Ravi de vous revoir !</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('login.send-otp') }}" class="loginForm">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Adresse email</label>
            <input type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="exemple@sgpme.ci"
                    required
                    autofocus>
            <div class="form-text">Un code de connexion sera envoyé à cette adresse</div>
        </div>

        <!-- Submit -->
        <div class="d-grid">
            <button type="submit" class="btn btn-success">
                Recevoir le code
            </button>
        </div>
    </form>
</div>
@endsection


