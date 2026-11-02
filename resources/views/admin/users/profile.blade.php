@extends('admin.layouts.app')

@push('page-header')
<div class="col">
	<h3 class="page-title">Profile</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item active">Profile</li>
	</ul>
</div>
@endpush

@section('content')
@php
	$passwordUpdateFailed = $errors->has('current_password')
		|| $errors->has('password')
		|| $errors->has('password_confirmation')
		|| session('password_update_failed');
@endphp
<div id="password-validation-message" class="alert bg-danger mb-2 d-none" role="alert" aria-live="assertive"></div>
<div class="row">
	<div class="col-md-12">
		<div class="profile-header">
			<div class="row align-items-center">
				<div class="col-auto profile-image">
					<a href="#">
						<img class="rounded-circle" alt="User Image" src="{{!empty(auth()->user()->avatar) ? asset('storage/users/'.auth()->user()->avatar): asset('assets/img/avatar_1nn.png')}}">
					</a>
				</div>
				<div class="col ml-md-n2 profile-user-info">
					<h4 class="user-name mb-0">{{auth()->user()->name}}</h4>
					<h6 class="text-muted">{{auth()->user()->email}}</h6>
				</div>

			</div>
		</div>
		<div class="profile-menu">
			<ul class="nav nav-tabs nav-tabs-solid">
				<li class="nav-item">
					<a class="nav-link {{ $passwordUpdateFailed ? '' : 'active' }}" data-toggle="tab" href="#per_details_tab">About</a>
				</li>
				<li class="nav-item">
					<a class="nav-link {{ $passwordUpdateFailed ? 'active' : '' }}" data-toggle="tab" href="#password_tab">Password</a>
				</li>
			</ul>
		</div>
		<div class="tab-content profile-tab-cont">

			<!-- Personal Details Tab -->
			<div class="tab-pane fade {{ $passwordUpdateFailed ? '' : 'show active' }}" id="per_details_tab">

				<!-- Personal Details -->
				<div class="row">
					<div class="col-lg-12">
						<div class="card">
							<div class="card-body">
								<h5 class="card-title d-flex justify-content-between">
									<span>Personal Details</span>
									<a class="edit-link" data-toggle="modal" href="#edit_personal_details"><i class="fa fa-edit mr-1"></i>Edit</a>
								</h5>
								<div class="row">
									<p class="col-sm-2 text-muted text-sm-right mb-0 mb-sm-3">Name</p>
									<p class="col-sm-10">{{auth()->user()->name}}</p>
								</div>

								<div class="row">
									<p class="col-sm-2 text-muted text-sm-right mb-0 mb-sm-3">Email ID</p>
									<p class="col-sm-10">{{auth()->user()->email}}</p>
								</div>

								<div class="row">
									<p class="col-sm-2 text-muted text-sm-right mv-0 mb-sm-3">User Role</p>
									<p class="col-sm-10">
										@foreach (auth()->user()->getRoleNames() as $role)
										{{$role}}
										@endforeach
									</p>
								</div>

							</div>
						</div>

						<!-- Edit Details Modal -->
						<div class="modal fade" id="edit_personal_details" aria-hidden="true" role="dialog">
							<div class="modal-dialog modal-dialog-centered" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title">Personal Details</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
										<form method="POST" enctype="multipart/form-data" action="{{route('profile.update',auth()->user())}}">
											@csrf
											<div class="row form-row">
												<div class="col-12">
													<div class="form-group">
														<label>Full Name</label>
														<input class="form-control" name="name" type="text" value="{{old('name', auth()->user()->name)}}" placeholder="Full Name" maxlength="50">
													</div>
												</div>
												<div class="col-12">
													<div class="form-group">
														<label>email</label>
														<input class="form-control" name="email" type="text" value="{{old('email', auth()->user()->email)}}" placeholder="Email" maxlength="30">
													</div>
												</div>
												@can('edit-role')
												<div class="col-12">
													<div class="form-group">
														<label>Role</label>
														<select class="form-control select edit_role" name="role">
															@foreach ($roles as $role)
																<option value="{{$role->name}}" {{old('role', auth()->user()->getRoleNames()->first()) === $role->name ? 'selected' : ''}}>{{$role->name}}</option>
															@endforeach
														</select>
													</div>
												</div>
												@endcan
												<div class="col-12">
													<div class="form-group">
														<label>User Avatar</label>
														<input type="file" value="{{auth()->user()->avatar}}" class="form-control" name="avatar">
													</div>
												</div>

											</div>
											<button type="submit" class="btn btn-success btn-block">Save Changes</button>
										</form>
									</div>
								</div>
							</div>
						</div>
						<!-- /Edit Details Modal -->

					</div>


				</div>
				<!-- /Personal Details -->

			</div>
			<!-- /Personal Details Tab -->

			<!-- Change Password Tab -->
			<div id="password_tab" class="tab-pane fade {{ $passwordUpdateFailed ? 'show active' : '' }}">

				<div class="card">
					<div class="card-body">
						<h5 class="card-title">Change Password</h5>
						<div class="row">
							<div class="col-md-10 col-lg-12">
								<form id="profile-password-form" method="POST" action="{{route('update-password',auth()->user())}}">
									@csrf
									@method("PUT")
									<div class="form-group position-relative">
										<label>Current Password</label>
										<input type="password" id="profile-current-password" name="current_password" class="form-control profile-password-input" placeholder="enter your current password" maxlength="20">
										<button type="button" class="profile-password-toggle" data-target="profile-current-password" aria-label="Show current password" title="Show current password">
											<svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
											<svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
										</button>
									</div>
									<div class="form-group position-relative">
										<label>New Password</label>
										<input type="password" id="profile-new-password" name="password" class="form-control profile-password-input" placeholder="enter your new password" minlength="8" maxlength="15" pattern="(?=.*[A-Za-z])(?=.*[0-9])[A-Za-z0-9]{8,15}" title="Password must be 8 to 15 characters and include both letters and numbers only.">
										<button type="button" class="profile-password-toggle" data-target="profile-new-password" aria-label="Show new password" title="Show new password">
											<svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
											<svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
										</button>
									</div>
									<small class="form-text text-muted mb-3">Use 8 to 15 characters, including both letters and numbers only.</small>
									<div class="form-group position-relative">
										<label>Confirm Password</label>
										<input type="password" id="profile-confirm-password" name="password_confirmation" class="form-control profile-password-input" placeholder="repeat your new password" minlength="8" maxlength="15" pattern="(?=.*[A-Za-z])(?=.*[0-9])[A-Za-z0-9]{8,15}" title="Password must be 8 to 15 characters and include both letters and numbers only.">
										<button type="button" class="profile-password-toggle" data-target="profile-confirm-password" aria-label="Show password confirmation" title="Show password confirmation">
											<svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
											<svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
										</button>
									</div>
									<button class="btn btn-success" type="submit">Save Changes</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- /Change Password Tab -->

		</div>
	</div>
</div>
@endsection

@push('page-css')
	<style>
		.profile-password-toggle {
			position: absolute;
			top: 50%;
			right: 14px;
			display: flex;
			align-items: center;
			padding: 0;
			color: #9aa0a6;
			background: none;
			border: 0;
			cursor: pointer;
			transform: translateY(-50%);
		}

		.profile-password-toggle:hover {
			color: #28a745;
		}

		.profile-password-input {
			padding-right: 42px;
		}
	</style>
@endpush

@push('page-js')
	<script>
		var passwordForm = document.getElementById('profile-password-form');
		var passwordValidationMessage = document.getElementById('password-validation-message');

		function showPasswordValidationMessage(message) {
			passwordValidationMessage.textContent = message;
			passwordValidationMessage.classList.remove('d-none');
		}

		function clearPasswordFields() {
			document.getElementById('profile-current-password').value = '';
			document.getElementById('profile-new-password').value = '';
			document.getElementById('profile-confirm-password').value = '';
		}

		passwordForm.addEventListener('submit', function(event) {
			var newPassword = document.getElementById('profile-new-password').value;
			var confirmation = document.getElementById('profile-confirm-password').value;
			if (newPassword !== confirmation) {
				event.preventDefault();
				showPasswordValidationMessage('New password and confirmation do not match.');
				clearPasswordFields();
			}
		});

		document.getElementById('profile-password-form').addEventListener('invalid', function(event) {
			if (event.target.matches('#profile-new-password, #profile-confirm-password')) {
				showPasswordValidationMessage('Password must be 8 to 15 characters and include both letters and numbers only.');
				window.setTimeout(function() {
					clearPasswordFields();
				}, 0);
			}
		}, true);

		$(document).on('click', '.profile-password-toggle', function() {
			var input = document.getElementById(this.dataset.target);
			var showingPassword = input.type === 'password';

			input.type = showingPassword ? 'text' : 'password';
			this.querySelector('.eye-open').style.display = showingPassword ? 'none' : 'block';
			this.querySelector('.eye-closed').style.display = showingPassword ? 'block' : 'none';
			this.setAttribute('aria-label', showingPassword ? 'Hide password' : 'Show password');
			this.setAttribute('title', showingPassword ? 'Hide password' : 'Show password');
		});

		$(document).on('input', '#edit_personal_details input[name="name"]', function() {
			this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '').slice(0, 50);
		});

		$(document).on('input', '#edit_personal_details input[name="email"]', function() {
			this.value = this.value.replace(/[^A-Za-z0-9.@]/g, '').slice(0, 30);
		});

		$(document).on('input', '#profile-current-password', function() {
			this.value = this.value.replace(/[^\x21-\x7E]/g, '').slice(0, 20);
		});

		$(document).on('input', '#profile-new-password, #profile-confirm-password', function() {
			this.value = this.value.replace(/[^A-Za-z0-9]/g, '').slice(0, 15);
		});

		@if ($passwordUpdateFailed)
			$('#password_tab input[type="password"]').val('');
		@endif
	</script>
@endpush