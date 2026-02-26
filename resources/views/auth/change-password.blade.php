@extends('layouts.auth')

@section('content')
<div class="p-4 p-sm-5 maxw-450px m-auto auth-inner" data-simplebar>

    <div class="text-center mb-4">
        <div class="visible-light">
            <img src="{{ asset('assets/images/brand/logo-light.png') }}" alt="AXIAL" style="height: 40px;">
        </div>
        <div class="visible-dark">
            <img src="{{ asset('assets/images/brand/logo-dark.png') }}" alt="AXIAL" style="height: 40px;">
        </div>
    </div>

    <div class="text-center mb-4">
        <div class="avatar avatar-lg bg-warning-subtle rounded-circle mx-auto mb-3">
            <i class="fi fi-rr-lock fs-3 text-warning"></i>
        </div>
        <h4 class="fw-bold mb-1">Définir votre mot de passe</h4>
        <p class="text-muted mb-0" style="font-size: 14px;">
            Bienvenue ! Pour accéder à l'application, veuillez définir un mot de passe personnel.
        </p>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fi fi-rr-exclamation me-2"></i>
        {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form method="POST" action="{{ route('password.change.update') }}" id="changePasswordForm">
        @csrf

        <div class="mb-3">
            <label for="password" class="form-label">Nouveau mot de passe <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                    id="password" name="password" placeholder="Minimum 8 caractères" required autofocus>
                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                    <i class="fi fi-rs-eye" id="eyeIcon"></i>
                </button>
            </div>
            <div class="mt-2" id="strengthBar" style="display:none;">
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar" id="strengthProgress" style="width: 0%; transition: width 0.3s;"></div>
                </div>
                <small id="strengthLabel" class="text-muted"></small>
            </div>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="password" class="form-control"
                    id="password_confirmation" name="password_confirmation" placeholder="Répétez le mot de passe" required>
                <button type="button" class="btn btn-outline-secondary" id="toggleConfirm">
                    <i class="fi fi-rs-eye" id="eyeIconConfirm"></i>
                </button>
            </div>
            <small class="d-none text-danger mt-1" id="matchError"><i class="fi fi-rr-exclamation me-1"></i>Les mots de passe ne correspondent pas.</small>
            <small class="d-none text-success mt-1" id="matchOk"><i class="fi fi-rr-check me-1"></i>Les mots de passe correspondent.</small>
        </div>

        <button type="submit" class="btn btn-primary w-100 waves-effect waves-light" id="submitBtn">
            <i class="fi fi-rr-check me-1"></i> Confirmer et accéder
        </button>
    </form>

    <div class="text-center mt-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-muted p-0" style="font-size: 13px;">
                <i class="fi fi-rr-sign-out me-1"></i> Se déconnecter
            </button>
        </form>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        let input = document.getElementById('password');
        let icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fi fi-rs-eye-crossed';
        } else {
            input.type = 'password';
            icon.className = 'fi fi-rs-eye';
        }
    });

    document.getElementById('toggleConfirm').addEventListener('click', function() {
        let input = document.getElementById('password_confirmation');
        let icon = document.getElementById('eyeIconConfirm');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fi fi-rs-eye-crossed';
        } else {
            input.type = 'password';
            icon.className = 'fi fi-rs-eye';
        }
    });

    // Password strength indicator
    document.getElementById('password').addEventListener('input', function() {
        let val = this.value;
        let bar = document.getElementById('strengthBar');
        let progress = document.getElementById('strengthProgress');
        let label = document.getElementById('strengthLabel');

        if (val.length === 0) { bar.style.display = 'none'; return; }
        bar.style.display = 'block';

        let score = 0;
        if (val.length >= 8) score++;
        if (val.length >= 12) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        let levels = [
            { pct: 20, color: 'bg-danger', text: 'Très faible' },
            { pct: 40, color: 'bg-danger', text: 'Faible' },
            { pct: 60, color: 'bg-warning', text: 'Moyen' },
            { pct: 80, color: 'bg-info', text: 'Fort' },
            { pct: 100, color: 'bg-success', text: 'Très fort' },
        ];
        let lvl = levels[Math.min(score, 4)];
        progress.style.width = lvl.pct + '%';
        progress.className = 'progress-bar ' + lvl.color;
        label.textContent = lvl.text;
        label.className = lvl.color.replace('bg-', 'text-');

        checkMatch();
    });

    // Password match check
    document.getElementById('password_confirmation').addEventListener('input', checkMatch);

    function checkMatch() {
        let p = document.getElementById('password').value;
        let c = document.getElementById('password_confirmation').value;
        let err = document.getElementById('matchError');
        let ok = document.getElementById('matchOk');
        if (c.length === 0) { err.classList.add('d-none'); ok.classList.add('d-none'); return; }
        if (p === c) { err.classList.add('d-none'); ok.classList.remove('d-none'); }
        else { ok.classList.add('d-none'); err.classList.remove('d-none'); }
    }

    // Prevent submit if passwords don't match
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        let p = document.getElementById('password').value;
        let c = document.getElementById('password_confirmation').value;
        if (p !== c) {
            e.preventDefault();
            document.getElementById('matchError').classList.remove('d-none');
            document.getElementById('password_confirmation').focus();
        }
    });
</script>
@endsection
