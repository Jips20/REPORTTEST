@extends('admin.layouts.app')
@push('page-css')
@endpush
@push('page-header')
<div class="col-sm-12">
	<h3 class="page-title">Edit Product</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item active">Edit Product</li>
	</ul>
</div>
@endpush
<!-- Visit codeastro.com for more projects -->
@section('content')
@php
    // Stored price already includes the margin, so show the original price here
    $storedMargin = (float) $product->discount;
    $basePrice = $storedMargin > 0
        ? round($product->price / (1 + ($storedMargin / 100)), 2)
        : (float) $product->price;
@endphp
<div class="row">
	<div class="col-sm-12">
		<div class="card">
			<div class="card-body custom-edit-service">
				
			<!-- Edit Product -->
				<form method="post" enctype="multipart/form-data" id="update_service" action="{{route('products.update',$product)}}">
					@csrf
                    @method("PUT")
					<div class="service-fields mb-3">
						<div class="row">
							
							<div class="col-lg-12">
								<div class="form-group">
									<label>Product <span class="text-danger">*</span></label>
									<select class="select2 form-select form-control" name="product"> 
                                        @foreach ($purchases as $purchase)
                                            @if(!empty($product->purchase))
                                            <option {{($product->purchase->id == $purchase->id) ? 'selected': ''}} value="{{$purchase->id}}">{{$purchase->product}}</option>
                                            @endif
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
									<label>Selling Price<span class="text-danger">*</span></label>
									<input class="form-control" type="text" inputmode="decimal" name="price" id="selling_price" value="{{old('price', number_format($basePrice, 2, '.', ''))}}" maxlength="8" pattern="[1-9][0-9]{0,4}(\.[0-9]{1,2})?" title="Selling price must start with a digit from 1 to 9; leading zeros are not allowed.">
								</div>
							</div>
	
							<div class="col-lg-6">
								<div class="form-group">
									<label>Margin (%)<span class="text-danger">*</span></label>
									<input class="form-control" value="{{ rtrim(rtrim(number_format($storedMargin, 2, '.', ''), '0'), '.') ?: '0' }}" type="text" inputmode="decimal" name="discount" id="margin" maxlength="5">
								</div>
							</div>
							
						</div>
					</div>
	
					<div class="service-fields mb-3">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label>Descriptions <span class="text-danger">*</span></label>
									<textarea class="form-control service-desc" name="description" id="description" maxlength="300">{{$product->description}}</textarea>
								</div>
							</div>
							
						</div>
					</div>					
					
					<div class="submit-section">
						<button class="btn btn-success submit-btn" type="submit" name="form_submit" value="submit">Submit</button>
					</div>
				</form>
			<!-- /Edit Product -->
			</div>
		</div>
	</div>			
</div>
@endsection
@push('page-js')
	<script>
		document.addEventListener('input', function(event) {
			if (event.target.matches('.select2-container--open .select2-search__field')) {
				event.target.value = event.target.value.replace(/[^a-zA-Z0-9 ]/g, '').slice(0, 30);
			}
		});

		// Allows: up to `intDigits` whole digits, one period, up to 2 decimals
		function cleanDecimal(value, intDigits) {
			value = value.replace(/[^0-9.]/g, '');
			var parts = value.split('.');
			var whole = parts[0].slice(0, intDigits);
			if (parts.length > 1) {
				return whole + '.' + parts.slice(1).join('').slice(0, 2);
			}
			return whole;
		}

		// Selling Price: max 5 whole digits + optional .00
		document.getElementById('selling_price').addEventListener('input', function() {
			this.value = cleanDecimal(this.value, 5);
		});

		// Margin: max 2 whole digits + optional decimals
		document.getElementById('margin').addEventListener('input', function() {
			this.value = cleanDecimal(this.value, 2);
		});

		// Description: letters, numbers, space, max 300
		document.getElementById('description').addEventListener('input', function() {
			this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, '').slice(0, 300);
		});
	</script>
@endpush