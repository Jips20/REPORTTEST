@extends('admin.layouts.app')

<x-assets.datatables />


@push('page-css')
    
@endpush

@push('page-header')
<div class="col-sm-7 col-auto">
	<h3 class="page-title">Sales Reports</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item active">Generate Sales Reports</li>
	</ul>
</div>
<div class="col-sm-5 col">
	<a href="#generate_report" data-toggle="modal" class="btn btn-success float-right mt-2">Generate Report</a>
</div>
@endpush

@section('content')
@isset($rows)
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="sales-table" class="datatable table table-hover table-center mb-0">

                    @if ($report_type === 'normal')
                        <thead>
                            <tr>
                                <th>Medicine Name</th>
                                <th>Quantity</th>
                                <th>Total Sales</th>
                                <th>Total Cost</th>
                                <th>Profit</th>
                                <th>Date</th>
                                <th>Sold By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $sale)
                                @if (!empty($sale->product->purchase))
                                <tr>
                                    <td>{{$sale->product->purchase->product}}</td>
                                    <td>{{$sale->quantity}}</td>
                                    <td>{{AppSettings::get('app_currency', '$')}} {{ number_format($sale->total_price, 2) }}</td>
                                    <td>{{AppSettings::get('app_currency', '$')}} {{ number_format($sale->total_cost, 2) }}</td>
                                    <td>{{AppSettings::get('app_currency', '$')}} {{ number_format($sale->profit, 2) }}</td>
                                    <td>{{date_format(date_create($sale->created_at),"d M, Y")}}</td>
                                    <td>{{ optional($sale->seller)->name ?? 'N/A' }}</td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>

                    @elseif ($report_type === 'best_medicine')
                        <thead>
                            <tr>
                                <th>Rank</th><th>Medicine Name</th><th>Total Quantity Sold</th>
                                <th>Total Sales</th><th>Total Cost</th><th>Total Profit</th><th>Transactions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $i => $row)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ optional(optional($row->product)->purchase)->product ?? 'N/A' }}</td>
                                <td>{{ $row->total_qty }}</td>
                                <td>{{AppSettings::get('app_currency', '$')}} {{ number_format($row->total_sales, 2) }}</td>
                                <td>{{AppSettings::get('app_currency', '$')}} {{ number_format($row->total_cost, 2) }}</td>
                                <td>{{AppSettings::get('app_currency', '$')}} {{ number_format($row->total_profit, 2) }}</td>
                                <td>{{ $row->transactions }}</td>
                            </tr>
                            @endforeach
                        </tbody>

                    @else
                        <thead>
                            <tr>
                                <th>Rank</th><th>Sales Person</th><th>Total Quantity Sold</th>
                                <th>Total Sales</th><th>Total Cost</th><th>Total Profit</th><th>Transactions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $i => $row)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ optional($row->seller)->name ?? 'N/A (no seller recorded)' }}</td>
                                <td>{{ $row->total_qty }}</td>
                                <td>{{AppSettings::get('app_currency', '$')}} {{ number_format($row->total_sales, 2) }}</td>
                                <td>{{AppSettings::get('app_currency', '$')}} {{ number_format($row->total_cost, 2) }}</td>
                                <td>{{AppSettings::get('app_currency', '$')}} {{ number_format($row->total_profit, 2) }}</td>
                                <td>{{ $row->transactions }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    @endif

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endisset

<!-- Generate Modal -->
<div class="modal fade" id="generate_report" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('sales.report')}}">
                    @csrf
                    <div class="row form-row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Report Type</label>
                                <select name="report_type" class="form-control">
                                    <option value="normal" {{ ($report_type ?? '') === 'normal' ? 'selected' : '' }}>Normal Sales List</option>
                                    <option value="best_medicine" {{ ($report_type ?? '') === 'best_medicine' ? 'selected' : '' }}>Best Selling Medicine</option>
                                    <option value="best_salesperson" {{ ($report_type ?? '') === 'best_salesperson' ? 'selected' : '' }}>Best Sales Person (Sold By)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Order</label>
                                <select name="sort" class="form-control">
                                    <option value="desc" {{ ($sort ?? 'desc') === 'desc' ? 'selected' : '' }}>Descending (Highest / Newest first)</option>
                                    <option value="asc" {{ ($sort ?? '') === 'asc' ? 'selected' : '' }}>Ascending (Lowest / Oldest first)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>From</label>
                                <input type="date" name="from_date" value="{{ $from_date ?? '' }}" class="form-control from_date" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>To</label>
                                <input type="date" name="to_date" value="{{ $to_date ?? '' }}" class="form-control to_date" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-block submit_report">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Generate Modal -->
@endsection

@include('admin.includes.report-export')

@push('page-js')
<script>
    $(document).ready(function(){
        const reportPeriod = @json(isset($from_date) && isset($to_date)
            ? 'Period: '.\Carbon\Carbon::parse($from_date)->format('d M, Y').' - '.\Carbon\Carbon::parse($to_date)->format('d M, Y')
            : null);
        const reportTitle = @json($reportTitle ?? 'Sales Report');
        const reportType  = @json($report_type ?? 'normal');

        // totals + column widths depend on the report type
        const pdfOptions = reportType === 'normal'
            ? { totalColumns: [
                {index: 1, type: 'number'},
                {index: 2, type: 'currency'},
                {index: 3, type: 'currency'},
                {index: 4, type: 'currency'} ] }
            : { totalColumns: [
                {index: 2, type: 'number'},
                {index: 3, type: 'currency'},
                {index: 4, type: 'currency'},
                {index: 5, type: 'currency'},
                {index: 6, type: 'number'} ],
                widths: ['auto', '*', 'auto', 'auto', 'auto', 'auto', 'auto'] };

        $('#sales-table').DataTable({
            order: [],   // keep the Ascending/Descending order chosen in the form
            dom: 'Bfrtip',
            buttons: [{
                extend: 'collection',
                text: 'Export Data',
                buttons: [
                    { extend: 'pdf', title: '',
                      exportOptions: { columns: "thead th:not(.action-btn)" },
                      customize: function (doc) { customizePdfReport(doc, reportTitle, reportPeriod, pdfOptions); } },
                    { extend: 'excel', title: reportTitle,
                      exportOptions: { columns: "thead th:not(.action-btn)" } },
                    { extend: 'csv', title: reportTitle,
                      exportOptions: { columns: "thead th:not(.action-btn)" } },
                    { extend: 'print', title: '',
                      exportOptions: { columns: "thead th:not(.action-btn)" },
                      customize: function (win) { customizePrintReport(win, reportTitle, reportPeriod); } }
                ]
            }]
        });
    });
</script>
@endpush