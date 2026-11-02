@extends('admin.layouts.plain')
@section('content')
<h1>Reset Password</h1>
<p class="account-subtitle">Enter your new password below.</p>

<!-- Form -->
<form action="{{ route('password.update') }}" method="POST" id="resetForm">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="form-group">
        <input
            class="form-control"
            name="email"
            type="email"
            value="{{ $email }}"
            placeholder="Email"
            readonly
            maxlength="100"
        >
    </div>
    <div class="form-group position-relative">
        <input
            class="form-control"
            id="newPassword"
            name="password"
            type="password"
            placeholder="Enter new password"
            pattern="(?=.*[A-Za-z])(?=.*[0-9])[A-Za-z0-9]{8,15}"
            autocomplete="off"
            style="padding-right: 42px;"
            required
            minlength="8"
            maxlength="15"
        >
        <span class="toggle-password-icon" data-target="newPassword">
            <svg class="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
            <svg class="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>
        </span>
    </div>
    <small id="passwordHint" class="d-block mb-2" style="color:#dc3545;">
        Must be 8 to 15 characters, using letters and numbers only.
    </small>
    <div class="form-group position-relative">
        <input
            class="form-control"
            id="confirmPassword"
            name="password_confirmation"
            type="password"
            placeholder="Confirm new password"
            pattern="(?=.*[A-Za-z])(?=.*[0-9])[A-Za-z0-9]{8,15}"
            autocomplete="off"
            style="padding-right: 42px;"
            required
            minlength="8"
            maxlength="15"
        >
        <span class="toggle-password-icon" data-target="confirmPassword">
            <svg class="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
            <svg class="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>
        </span>
    </div>
    <div class="form-group mb-0">
        <button class="btn btn-primary btn-block" type="submit">
            Reset Password
        </button>
    </div>
</form>
<!-- /Form -->
<div class="text-center dont-have">
    Remember your password?
    <a href="{{ route('login') }}">Login</a>
</div>

<style>
    .toggle-password-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #9aa0a6;
        display: flex;
        align-items: center;
        transition: color 0.2s ease;
    }
    .toggle-password-icon:hover {
        color: #28a745;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Show/Hide toggle for both password fields
        document.querySelectorAll('.toggle-password-icon').forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                var targetInput = document.getElementById(this.dataset.target);
                var eyeOpen = this.querySelector('.eyeOpen');
                var eyeClosed = this.querySelector('.eyeClosed');
                var isPassword = targetInput.getAttribute('type') === 'password';
                targetInput.setAttribute('type', isPassword ? 'text' : 'password');
                eyeOpen.style.display = isPassword ? 'none' : 'block';
                eyeClosed.style.display = isPassword ? 'block' : 'none';
            });
        });

        var newPassword = document.getElementById('newPassword');
        var confirmPassword = document.getElementById('confirmPassword');
        var hint = document.getElementById('passwordHint');

        // Only letters and numbers allowed, live filter
        function filterInput(e) {
            e.target.value = e.target.value.replace(/[^A-Za-z0-9]/g, '');
        }
        newPassword.addEventListener('input', filterInput);
        confirmPassword.addEventListener('input', filterInput);

        // Live strength check: min 8 chars, letters + numbers
        newPassword.addEventListener('input', function () {
            var value = this.value;
            var hasLetter = /[A-Za-z]/.test(value);
            var hasNumber = /\d/.test(value);
            var isValidLength = value.length >= 8 && value.length <= 15;

            if (hasLetter && hasNumber && isValidLength) {
                hint.style.color = '#28a745';
                hint.textContent = 'Looks good!';
            } else {
                hint.style.color = '#dc3545';
                hint.textContent = 'Must be 8 to 15 characters, using letters and numbers only.';
            }
        });
    });
</script>
@endsection