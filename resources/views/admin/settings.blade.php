@extends('admin.layouts.app')
@php
    $title ='settings';
@endphp

@push('page-header')
<div class="col-sm-12">
	<h3 class="page-title">General Settings</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item"><a href="javascript:(0)">Settings</a></li>
		<li class="breadcrumb-item active">General Settings</li>
	</ul>
</div>
@endpush

@section('content')

<div class="row">				
	<div class="col-12">
		@include('app_settings::_settings')	
	</div>
</div>
@endsection

@push('page-js')
<script>
	$(function() {
		function sanitizeSetting(selector, invalidCharacters, maxLength) {
			$(selector).attr('maxlength', maxLength).on('input', function() {
				this.value = this.value.replace(invalidCharacters, '').slice(0, maxLength);
			});
		}

		sanitizeSetting('#app_name', /[^A-Za-z0-9 ]/g, 50);
		sanitizeSetting('#app_currency', /[^₱$]/g, 1);
		sanitizeSetting('#pharmacy_branch', /[^A-Za-z0-9 ]/g, 60);
		sanitizeSetting('#pharmacy_address', /[^A-Za-z0-9 #.]/g, 100);
		sanitizeSetting('#pharmacy_contact', /[^0-9]/g, 11);
	});
</script>
@endpush

