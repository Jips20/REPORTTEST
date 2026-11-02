@extends('admin.layouts.app')


@push('page-css')
    
@endpush

@push('page-header')
<div class="col-sm-12">
	<h3 class="page-title">Edit Sale</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item active">Edit Sale</li>
	</ul>
</div>
@endpush

@section('content')
<div class="row">
	<div class="col-sm-12">
		<div class="card">
			<div class="card-body custom-edit-service">
                <!-- Edit Sale -->
                <form method="POST" action="{{route('sales.update',$sale)}}">
					@csrf
					@method("PUT")
					<div class="row form-row">
						<div class="col-12">
							<div class="form-group">
								<label>Product <span class="text-danger">*</span></label>
								<select class="select2 form-select form-control edit_product" disabled>
									@foreach ($products as $product)
										@if (!empty($product->purchase))
											@php
												$isCurrent = $product->purchase->id == $sale->product->purchase_id;
												$outOfStock = $product->purchase->quantity <= 0;
											@endphp
											<option value="{{$product->id}}"
												{{ $isCurrent ? 'selected' : '' }}
												{{ ($outOfStock && !$isCurrent) ? 'disabled' : '' }}>
												{{$product->purchase->product}}{{ $outOfStock ? ' — Unavailable (Out of Stock)' : '' }}
											</option>
										@endif
									@endforeach
								</select>
								<input type="hidden" name="product" value="{{$sale->product_id}}">
							</div>
						</div>
						<div class="col-12">
							<div class="form-group">
								<label>Quantity</label>
								<input type="text" inputmode="numeric" maxlength="3" pattern="[0-9]{1,3}"
									title="Numbers only (max 3 digits)" class="form-control edit_quantity"
									value="{{$sale->quantity ?? '1'}}" name="quantity"
									oninput="this.value=this.value.replace(/\D/g,'').slice(0,3)">
							</div>
						</div>
					</div>
					<button type="submit" class="btn btn-success btn-block">Save Changes</button>
				</form>
                <!--/ Edit Sale -->
			</div>
		</div>
	</div>			
</div>
@endsection	
<!-- Visit codeastro.com for more projects -->

@push('page-js')
	<script>
		document.addEventListener('input', function(event) {
			if (event.target.matches('.select2-container--open .select2-search__field')) {
				event.target.value = event.target.value.replace(/[^a-zA-Z0-9 ]/g, '').slice(0, 30);
			}
		});
	</script>
@endpush