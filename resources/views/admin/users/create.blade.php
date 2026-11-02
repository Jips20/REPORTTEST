@extends('admin.layouts.app')

@push('page-css')
    <style>
        .user-password-toggle {
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

        .user-password-toggle:hover {
            color: #28a745;
        }

        .user-password-input {
            padding-right: 42px;
        }
    </style>
@endpush

@push('page-header')
<div class="col-sm-12">
	<h3 class="page-title">Create User</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item active">Dashboard</li>
	</ul>
</div>
@endpush

@section('content')

<div class="row">
    <div class="col-md-12 col-lg-12">
    
        <div class="card card-table">
            <div class="card-header">
                <h4 class="card-title ">Add User</h4>
            </div>
            <div class="card-body">
                <div class="p-5">
                    <form method="POST" enctype="multipart/form-data" action="{{route('users.store')}}">
                        @csrf
                        <div class="row form-row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="John Doe" maxlength="50">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="example@gmail.com" maxlength="50">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Role</label>
                                    <div class="form-group">
                                        <select class="select2 form-select form-control" name="role">
                                            @foreach ($roles as $role)
                                                <option value="{{$role->name}}">{{$role->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Picture</label>
                                    <input type="file" name="avatar" class="form-control" >
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group position-relative">
                                            <label>Password</label>
                                            <input type="password" id="user-password" name="password" class="form-control user-password-input" minlength="8" maxlength="15" pattern="(?=.*[A-Za-z])(?=.*[0-9])[A-Za-z0-9]{8,15}" title="Password must be 8 to 15 characters and include both letters and numbers only." required>
                                            <button type="button" class="user-password-toggle" data-target="user-password" aria-label="Show password" title="Show password">
                                                <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                <svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                                    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group position-relative">
                                            <label>Confirm Password</label>
                                            <input type="password" id="user-password-confirmation" name="password_confirmation" class="form-control user-password-input" minlength="8" maxlength="15" pattern="(?=.*[A-Za-z])(?=.*[0-9])[A-Za-z0-9]{8,15}" title="Password must be 8 to 15 characters and include both letters and numbers only." required>
                                            <button type="button" class="user-password-toggle" data-target="user-password-confirmation" aria-label="Show password confirmation" title="Show password confirmation">
                                                <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                <svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                                    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
        
    </div>

    
</div>

@endsection

@push('page-js')
    <script>
        document.addEventListener('invalid', function(event) {
            if (event.target.matches('#user-password, #user-password-confirmation') && event.target.validity.patternMismatch) {
                var form = event.target.form;
                window.setTimeout(function() {
                    form.querySelector('#user-password').value = '';
                    form.querySelector('#user-password-confirmation').value = '';
                }, 0);
            }
        }, true);

        $(document).on('click', '.user-password-toggle', function() {
            var input = document.getElementById(this.dataset.target);
            var showingPassword = input.type === 'password';

            input.type = showingPassword ? 'text' : 'password';
            this.querySelector('.eye-open').style.display = showingPassword ? 'none' : 'block';
            this.querySelector('.eye-closed').style.display = showingPassword ? 'block' : 'none';
            this.setAttribute('aria-label', showingPassword ? 'Hide password' : 'Show password');
            this.setAttribute('title', showingPassword ? 'Hide password' : 'Show password');
        });

        $(document).on('input', 'input[name="name"]', function() {
            this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '').slice(0, 50);
        });

        $(document).on('input', 'input[name="email"]', function() {
            this.value = this.value.replace(/[^A-Za-z0-9.@]/g, '').slice(0, 50);
        });

        $(document).on('input', 'input[name="password"], input[name="password_confirmation"]', function() {
            this.value = this.value.replace(/[^A-Za-z0-9]/g, '').slice(0, 15);
        });
    </script>
@endpush