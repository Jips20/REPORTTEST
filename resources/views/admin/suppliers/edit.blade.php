@extends('admin.layouts.app')

@push('page-css')

@endpush

@push('page-header')
<div class="col-sm-12">
	<h3 class="page-title">Edit Supplier</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item active">Edit Supplier</li>
	</ul>
</div>
@endpush

@section('content')
<div class="row">
	<div class="col-sm-12">
		<div class="card">
			<div class="card-body custom-edit-service">
			
			<!-- Edit Supplier -->
			<form method="post" enctype="multipart/form-data" action="{{route('suppliers.update',$supplier)}}">
				@csrf
				@method("PUT")
				<div class="service-fields mb-3">
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Name<span class="text-danger">*</span></label>
								<input class="form-control" type="text" value="{{old('name', $supplier->name)}}" name="name" maxlength="30" pattern="[A-Za-z0-9 .]+" required>
							</div>
						</div>
						<div class="col-lg-6">
							<label>Email<span class="text-danger">*</span></label>
							<input class="form-control" type="text" value="{{old('email', $supplier->email)}}" name="email" maxlength="50" pattern="[A-Za-z0-9.@]*">
						</div>
					</div>
				</div>

				<div class="service-fields mb-3">
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Phone<span class="text-danger">*</span></label>
								<input class="form-control" type="text" value="{{old('phone', $supplier->phone)}}" name="phone" maxlength="11" pattern="[0-9]{11}">
							</div>
						</div>
						<div class="col-lg-6">
							<label>Company<span class="text-danger">*</span></label>
							<input class="form-control" type="text" value="{{old('company', $supplier->company)}}" name="company" maxlength="60" pattern="[A-Za-z0-9 ]+" required>
						</div>
					</div>
				</div>

				<div class="service-fields mb-3">
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Address <span class="text-danger">*</span></label>
								<input type="text" name="address" value="{{old('address', $supplier->address)}}" class="form-control" maxlength="80" pattern="[A-Za-z0-9 .-]+" required>
							</div>
						</div>
						<div class="col-lg-6">
							<label>Product</label>
							<input type="text" name="product" value="{{old('product', $supplier->product)}}" class="form-control" maxlength="50" pattern="[A-Za-z0-9 ]+" required>
						</div>
					</div>
				</div>	
				<div class="service-fields mb-3">
					<div class="row">
						<div class="col-12">
							<label>Comment</label>
							<textarea name="comment" class="form-control" cols="30" rows="10" maxlength="300" pattern="[A-Za-z0-9 .]*">{{old('comment', $supplier->comment)}}</textarea>
						</div>
					</div>
				</div>		
				
				
				<div class="submit-section">
					<button class="btn btn-success submit-btn" type="submit" name="form_submit" value="submit">Submit</button>
				</div>
			</form>

			<!-- /Edit Supplier -->

			</div>
		</div>
	</div>			
</div>
@endsection	



@push('page-js')
	<script>
		$(function() {
			$('input[name="name"]').on('input', function() {
				this.value = this.value.replace(/[^A-Za-z0-9 .]/g, '').slice(0, 30);
			});

			$('input[name="email"]').on('input', function() {
				this.value = this.value.replace(/[^A-Za-z0-9.@]/g, '').slice(0, 50);
			});

			$('input[name="phone"]').on('input', function() {
				this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
			});

			$('input[name="company"]').on('input', function() {
				this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '').slice(0, 60);
			});

			$('input[name="address"]').on('input', function() {
				this.value = this.value.replace(/[^A-Za-z0-9 .-]/g, '').slice(0, 80);
			});

			$('input[name="product"]').on('input', function() {
				this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '').slice(0, 50);
			});

			$('textarea[name="comment"]').on('input', function() {
				this.value = this.value.replace(/[^A-Za-z0-9 .]/g, '').slice(0, 300);
			});
		});
	</script>
@endpush



