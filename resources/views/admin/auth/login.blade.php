@extends('admin.layouts.plain')
@section('content')
<h1>Pharmacy System</h1>
<p class="account-subtitle">Login Panel</p>
@if (session('login_error'))
@if (session('register_success'))
<x-alerts.success :message="session('register_success')" />
@endif
<x-alerts.danger :error="session('login_error')" />
@endif
<!-- Form -->
<form action="{{route('login')}}" method="post" id="loginForm">
	@csrf
	<div class="form-group">
		<input class="form-control" id="loginEmail" name="email" type="text" placeholder="Email"
			pattern="[A-Za-z0-9.@]+" title="Only letters, numbers, period (.) and @ are allowed" autocomplete="off"
			maxlength="30">
	</div>
	<div class="form-group position-relative">
		<input class="form-control" id="loginPassword" name="password" type="password" placeholder="Password"
			pattern="[A-Za-z0-9.]+" title="Only letters, numbers, and period (.) are allowed" autocomplete="off"
			maxlength="15" style="padding-right: 42px;">
		<span id="togglePassword" class="toggle-password-icon">
			<svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
				<circle cx="12" cy="12" r="3"></circle>
			</svg>
			<svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
				<path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
				<line x1="1" y1="1" x2="23" y2="23"></line>
			</svg>
		</span>
	</div>
	<div class="form-group">
		<button class="btn btn-success btn-block" type="submit">Login</button>
	</div>
</form>
<!-- /Form -->
<!-- Visit codeastro.com for more projects -->
<div class="text-center forgotpass"><a href="{{route('password.request')}}">Forgot Password?</a></div>
<div class="text-center dont-have">Don’t have an account? <a href="{{route('register')}}">Register</a></div>

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
		var emailInput = document.getElementById('loginEmail');
		var passwordInput = document.getElementById('loginPassword');
		var toggleBtn = document.getElementById('togglePassword');
		var eyeOpen = document.getElementById('eyeOpen');
		var eyeClosed = document.getElementById('eyeClosed');

		// Only letters, numbers, period (.) and @ allowed in Email
		emailInput.addEventListener('input', function () {
			this.value = this.value.replace(/[^A-Za-z0-9.@]/g, '');
		});

		// Only letters and numbers allowed in Password
		passwordInput.addEventListener('input', function () {
			this.value = this.value.replace(/[^A-Za-z0-9.]/g, '');
		});

		// Show / Hide password
		toggleBtn.addEventListener('click', function () {
			var isPassword = passwordInput.getAttribute('type') === 'password';
			passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
			eyeOpen.style.display = isPassword ? 'none' : 'block';
			eyeClosed.style.display = isPassword ? 'block' : 'none';
		});
	});
</script>
@endsection