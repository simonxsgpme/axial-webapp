@extends('layouts.auth')

@section('content')
<div class="p-4 p-sm-5 maxw-450px m-auto auth-inner" data-simplebar>
    <div class="mb-4 text-center">
        <a href="/" aria-label="AXIAL logo">
            <img class="visible-light" src="https://www.sgpme.ci/wp-content/uploads/2026/02/logo-150.png" width="120" alt="AXIAL logo">
        </a>
    </div>
    <div class="text-center mb-5">
        <h5 class="mb-1">Vérification du code</h5>
        <p>Entrez le code à 6 chiffres envoyé à votre email</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('login.verify.submit') }}" class="verifyForm">
        @csrf

        <!-- OTP Code -->
        <div class="mb-3">
            <label for="code" class="form-label">Code de vérification</label>
            <input type="text"
                    class="form-control text-center @error('code') is-invalid @enderror"
                    id="code"
                    name="code"
                    placeholder="000000"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    required
                    autofocus
                    style="font-size: 1.5rem; letter-spacing: 0.5rem;">
            <div class="form-text">Le code expire dans 10 minutes</div>
        </div>

        <!-- Submit -->
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-success">
                Vérifier et se connecter
            </button>
        </div>

        <!-- Resend Code -->
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-muted">
                <i class="fi fi-rs-arrow-left me-1"></i> Retour à la connexion
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Auto-focus and format code input
    const codeInput = document.getElementById('code');
    
    codeInput.addEventListener('input', function(e) {
        // Only allow numbers
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Auto-submit when 6 digits are entered
        if (this.value.length === 6) {
            document.querySelector('.verifyForm').submit();
        }
    });

    // Prevent paste of non-numeric characters
    codeInput.addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedText = (e.clipboardData || window.clipboardData).getData('text');
        const numericOnly = pastedText.replace(/[^0-9]/g, '').substring(0, 6);
        this.value = numericOnly;
        
        if (numericOnly.length === 6) {
            document.querySelector('.verifyForm').submit();
        }
    });
</script>
@endpush
