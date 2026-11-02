@extends('admin.layouts.plain')
@section('content')
<h1>Pharmacy System</h1>
<p class="account-subtitle">Register as Sales Person</p>
<!-- Visit codeastro.com for more projects -->

<!-- Form -->
<form action="{{route('register')}}" method="POST" id="registerForm">
	@csrf
	<div class="form-group">
		<input class="form-control" id="regName" name="name" type="text" value="{{ $errors->has('name') ? '' : old('name') }}"
			placeholder="Full Name" pattern="[A-Za-z. ]+" maxlength="50"
			title="Only letters, spaces and periods are allowed" autocomplete="off" required>
	</div>
	<div class="form-group">
		<input class="form-control" id="regEmail" name="email" type="text" value="{{ $errors->has('email') ? '' : old('email') }}"
			placeholder="Email (e.g. name@gmail.com)" pattern="[A-Za-z0-9.]+@gmail\.com" maxlength="30"
			title="Only Gmail addresses are accepted (e.g. name@gmail.com)" autocomplete="off" required>
		<small id="emailHint" class="d-block mt-1" style="color:#dc3545;"></small>
	</div>
	<div class="form-group position-relative">
		<input class="form-control" id="regPassword" name="password" type="password" placeholder="Password"
			pattern="[A-Za-z0-9]+" maxlength="15" autocomplete="off" style="padding-right: 42px;" required>
		<span class="toggle-password-icon" data-target="regPassword">
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
		Must be at least 8 characters, with both letters and numbers.
	</small>
	<div class="form-group position-relative">
		<input class="form-control" id="regPasswordConfirm" name="password_confirmation" type="password"
			placeholder="Confirm Password" pattern="[A-Za-z0-9]+" maxlength="15" autocomplete="off"
			style="padding-right: 42px;" required>
		<span class="toggle-password-icon" data-target="regPasswordConfirm">
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
		<button class="btn btn-danger btn-block" type="submit">Register (SalesPerson)</button>
	</div>
</form>
<!-- /Form -->
								
<div class="text-center dont-have">Already have an account? <a href="{{route('login')}}">Login</a></div>

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

		var nameInput = document.getElementById('regName');
		var emailInput = document.getElementById('regEmail');
		var passwordInput = document.getElementById('regPassword');
		var confirmInput = document.getElementById('regPasswordConfirm');
		var hint = document.getElementById('passwordHint');
		var registerForm = document.getElementById('registerForm');

		registerForm.addEventListener('invalid', function (event) {
			var input = event.target;
			if (!input.validity.patternMismatch) {
				return;
			}

			input.setCustomValidity(input.validationMessage);
			window.setTimeout(function () {
				input.value = '';
				if (input === emailInput) {
					document.getElementById('emailHint').textContent = '';
				}
			}, 0);
		}, true);

		registerForm.addEventListener('input', function (event) {
			event.target.setCustomValidity('');
		});

		nameInput.addEventListener('input', function () {
			this.value = this.value.replace(/[^A-Za-z. ]/g, '').slice(0, 50);
		});

		emailInput.addEventListener('input', function () {
			this.value = this.value.replace(/[^A-Za-z0-9.@]/g, '').slice(0, 30);
		});

		function filterPassword(e) {
			e.target.value = e.target.value.replace(/[^A-Za-z0-9]/g, '').slice(0, 15);
		}
		passwordInput.addEventListener('input', filterPassword);
		confirmInput.addEventListener('input', filterPassword);

		passwordInput.addEventListener('input', function () {
			var value = this.value;
			var hasLetter = /[A-Za-z]/.test(value);
			var hasNumber = /\d/.test(value);
			var isLongEnough = value.length >= 8;

			if (hasLetter && hasNumber && isLongEnough) {
				hint.style.color = '#28a745';
				hint.textContent = 'Looks good!';
			} else {
				hint.style.color = '#dc3545';
				hint.textContent = 'Must be at least 8 characters, with both letters and numbers.';
			}
		});
	});
</script>
@endsection