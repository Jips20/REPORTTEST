@extends('admin.layouts.app')
@push('page-css')
    
@endpush    
@push('page-header')
<div class="col-sm-12">
	<h3 class="page-title">Add Product</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item active">Add Product</li>
	</ul>
</div>
@endpush
<!-- Visit codeastro.com for more projects -->
@section('content')
<div class="row">
	<div class="col-sm-12">
		<div class="card">
			<div class="card-body custom-edit-service">
                <!-- Add Product -->
                <form method="post" enctype="multipart/form-data" id="update_service" action="{{route('products.store')}}">
                    @csrf
                    <div class="service-fields mb-3">
                        <div class="row">
                            
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Product <span class="text-danger">*</span></label>
                                    <select class="select2 form-select form-control" name="product"> 
                                        @foreach ($purchases as $purchase)
                                            <option value="{{$purchase->id}}">{{$purchase->product}}</option>
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
                                    <input class="form-control" type="text" inputmode="decimal" name="price" id="selling_price" value="{{old('price')}}" maxlength="8" pattern="[1-9][0-9]{0,4}(\.[0-9]{1,2})?" title="Selling price must start with a digit from 1 to 9; leading zeros are not allowed.">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Margin (%)<span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" inputmode="decimal" name="discount" id="margin" value="0" maxlength="5">
                                </div>
                            </div>
                            
                        </div>
                    </div>
                                    
                    
                    <div class="service-fields mb-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Descriptions <span class="text-danger">*</span></label>
                                    <textarea class="form-control service-desc" name="description" id="description" maxlength="300">{{old('description')}}</textarea>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                    
                    <div class="submit-section">
                        <button class="btn btn-success submit-btn" type="submit" name="form_submit" value="submit">Submit</button>
                    </div>
                </form>
                <!-- /Add Product -->
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

		function cleanDecimal(value, intDigits) {
			value = value.replace(/[^0-9.]/g, '');
			var parts = value.split('.');
			var whole = parts[0].slice(0, intDigits);
			if (parts.length > 1) {
				return whole + '.' + parts.slice(1).join('').slice(0, 2);
			}
			return whole;
		}

		document.getElementById('selling_price').addEventListener('input', function() {
			this.value = cleanDecimal(this.value, 5);
		});

		document.getElementById('margin').addEventListener('input', function() {
			this.value = cleanDecimal(this.value, 2);
		});

		document.getElementById('description').addEventListener('input', function() {
			this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, '').slice(0, 300);
		});
	</script>
@endpush