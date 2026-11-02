@extends('admin.layouts.plain')
@section('content')
<div class="text-center">
	<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#f0ad4e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-3">
		<circle cx="12" cy="12" r="10"></circle>
		<polyline points="12 6 12 12 16 14"></polyline>
	</svg>
	<h1>Registration Successful!</h1>
	<p class="account-subtitle">
		Please wait for the Approval of the Admin. Kindly check your email once your account has been reviewed.
	</p>
	<p class="mt-3">
		<a href="{{ route('login') }}">&larr; Back to Login</a>
	</p>
</div>
@endsection