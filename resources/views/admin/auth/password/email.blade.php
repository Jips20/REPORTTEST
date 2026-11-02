@extends('admin.layouts.plain')
@section('content')
<h1>Forgot Password?</h1>
<p class="account-subtitle">Enter your email to get a password reset link</p>

@if (session('status'))
<x-alerts.success :message="session('status')" />
@endif

<!-- Form -->
<form action="{{route('password.request')}}" method="post">
	@csrf
	<div class="form-group">
		<input class="form-control" id="forgotEmail" name="email" type="text" placeholder="Email"
			pattern="[A-Za-z0-9.@]+" title="Only letters, numbers, period (.) and @ are allowed"
			autocomplete="off" value="{{ $errors->has('email') ? '' : old('email') }}">
		<small id="emailHint" class="d-block mt-1" style="color:#dc3545;"></small>
	</div>
	<div class="form-group mb-0">
		<button class="btn btn-primary btn-block" type="submit">Submit</button>
	</div>
</form>
<!-- /Form -->
<div class="text-center dont-have">Remember your password? <a href="{{route('login')}}">Login</a></div>

<script>
	document.addEventListener('DOMContentLoaded', function () {
		var emailInput = document.getElementById('forgotEmail');
		emailInput.addEventListener('input', function () {
			// Allow letters, numbers, period while typing (so "@gmail.com" can still be typed)
			this.value = this.value.replace(/[^A-Za-z0-9.@]/g, '').slice(0, 50);

			var emailHint = document.getElementById('emailHint');
			var value = this.value;

			if (value.length === 0) {
				emailHint.textContent = '';
			} else if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
				emailHint.style.color = '#28a745';
				emailHint.textContent = 'Email format looks valid.';
			} else {
				emailHint.style.color = '#dc3545';
				emailHint.textContent = 'Enter a valid email address.';
			}
		});
	});
</script>
@endsection