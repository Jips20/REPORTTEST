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
                <!-- Create Sale -->
                <form method="POST" action="{{route('sales.store')}}">
					@csrf
					<div class="row form-row">
						<div class="col-12">
							<div class="form-group">
								<label>Product <span class="text-danger">*</span></label>
								<select class="select2 form-select form-control" name="product">
									<option disabled selected>Select Product</option>
									@foreach ($products as $product)
										@if (!empty($product->purchase))
											@if ($product->purchase->quantity <= 0)
												<option value="{{$product->id}}" disabled>
													{{$product->purchase->product}} — Unavailable (Out of Stock)
												</option>
											@else
												<option value="{{$product->id}}">
													{{$product->purchase->product}}
												</option>
											@endif
										@endif
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-12">
							<div class="form-group">
								<label>Quantity</label>
								<input type="text" inputmode="numeric" value="1" maxlength="3" pattern="[0-9]{1,3}"
									title="Numbers only (max 3 digits)" class="form-control" name="quantity"
									oninput="this.value=this.value.replace(/\D/g,'').slice(0,3)">
							</div>
						</div>
					</div>
					<button type="submit" class="btn btn-success btn-block">Save Changes</button>
				</form>
                <!--/ Create Sale -->
			</div>
		</div><!-- Visit codeastro.com for more projects -->
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
	</script>
@endpush