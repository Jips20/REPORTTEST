@extends('admin.layouts.app')
@push('page-css')
@endpush
@push('page-header')
<div class="col-sm-12">
	<h3 class="page-title">Edit Purchase</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item active">Edit Purchase</li>
	</ul>
</div>
@endpush
@section('content')
<div class="row">
	<div class="col-sm-12">
		<div class="card">
			<div class="card-body custom-edit-service">
			
			<!-- Edit Supplier -->
			<form method="post" enctype="multipart/form-data" autocomplete="off" action="{{route('purchases.update',$purchase)}}">
				@csrf
				@method("PUT")
				<div class="service-fields mb-3">
					<div class="row">
						<div class="col-lg-4">
							<div class="form-group">
								<label>Medicine Name<span class="text-danger">*</span></label>
								<input class="form-control" type="text" value="{{$purchase->product}}" name="product" id="product_name" maxlength="80">
							</div>
						</div>
						<div class="col-lg-4">
							<div class="form-group">
								<label>Category <span class="text-danger">*</span></label>
								<select class="select2 form-select form-control" name="category"> 
									@foreach ($categories as $category)
										<option {{($purchase->category->id == $category->id) ? 'selected': ''}} value="{{$category->id}}">{{$category->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="form-group">
								<label>Supplier <span class="text-danger">*</span></label>
								<select class="select2 form-select form-control" name="supplier"> 
									@foreach ($suppliers as $supplier)
										<option @if($purchase->supplier->id == $supplier->id) selected @endif value="{{$supplier->id}}">{{$supplier->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>
				
				<div class="service-fields mb-3">
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Cost Price<span class="text-danger">*</span></label>
								<input class="form-control" value="{{old('cost_price', $purchase->cost_price)}}" type="text" name="cost_price" id="cost_price" maxlength="9" inputmode="decimal">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label>Quantity<span class="text-danger">*</span></label>
								<input class="form-control" value="{{old('quantity', $purchase->quantity)}}" type="text" name="quantity" id="quantity" maxlength="4">
							</div>
						</div>
					</div>
				</div>
				<div class="service-fields mb-3">
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Expire Date<span class="text-danger">*</span></label>
								<input class="form-control" value="{{$purchase->expiry_date}}" type="date" name="expiry_date" min="{{ now()->addDay()->toDateString() }}">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label>Medicine Image</label>
								<input type="file" name="image" value="{{$purchase->image}}" class="form-control">
							</div>
						</div>
					</div>
				</div>
				
				
				<div class="submit-section">
					<button class="btn btn-success submit-btn" type="submit" >Submit</button>
				</div>
			</form>
			<!-- /Edit Supplier -->
			</div>
		</div>
	</div>			
</div>
@endsection	
<!-- Visit codeastro.com for more projects -->
@push('page-js')
	<!-- Select2 JS -->
	<script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
	<script>
		document.addEventListener('input', function(event) {
			if (event.target.matches('.select2-container--open .select2-search__field')) {
				event.target.value = event.target.value.replace(/[^a-zA-Z0-9 ]/g, '').slice(0, 20);
			}
		});

		// Medicine Name: letters, numbers, period, max 80
		document.getElementById('product_name').addEventListener('input', function() {
			this.value = this.value.replace(/[^a-zA-Z0-9.\s]/g, '').slice(0, 80);
		});

		// Cost Price: up to six digits and two decimal places
		document.getElementById('cost_price').addEventListener('input', function() {
			var value = this.value.replace(/[^0-9.]/g, '');
			var decimalPosition = value.indexOf('.');
			if (decimalPosition !== -1) {
				value = value.slice(0, decimalPosition + 1) + value.slice(decimalPosition + 1).replace(/\./g, '');
			}
			var parts = value.split('.');
			parts[0] = parts[0].slice(0, 6);
			if (parts.length > 1) {
				parts[1] = parts[1].slice(0, 2);
			}
			this.value = parts.slice(0, 2).join('.');
		});

		// Quantity: numbers only, max 4
		document.getElementById('quantity').addEventListener('input', function() {
			this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4);
		});
	</script>
@endpush