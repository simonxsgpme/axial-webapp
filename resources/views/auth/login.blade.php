@extends('layouts.auth')

@section('content')
<div class="p-4 p-sm-5 maxw-450px m-auto auth-inner" data-simplebar>
    <div class="mb-4 text-center">
        <a href="/" aria-label="GXON logo">
            <img class="visible-light" src="https://www.sgpme.ci/wp-content/uploads/2026/02/logo-150.png" width="120" alt="SGPME logo">
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

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Adresse email</label>
            <input type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="exemp@sgpme.ci"
                    required
                    autofocus>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <div class="input-group">
                <input type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        placeholder="********"
                        required>
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="fi fi-rs-eye"></i>
                </button>
            </div>
        </div>

        <!-- Remember Me -->
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">Se souvenir de moi</label>
            </div>
        </div>

        <!-- Submit -->
        <div class="d-grid">
            <button type="submit" class="btn btn-success">
                Me connecter
            </button>
        </div>
    </form>
</div>
@endsection


@push('scripts')
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fi-rs-eye');
            icon.classList.add('fi-rs-eye-off');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fi-rs-eye-off');
            icon.classList.add('fi-rs-eye');
        }
    });
</script>
@endpush
