<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Events\PurchaseOutStock;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-sales')->only('index');
        $this->middleware('permission:create-sale')->only(['create', 'store']);
        $this->middleware('permission:edit-sale')->only(['edit', 'update']);
        $this->middleware('permission:destroy-sale')->only('destroy');
        $this->middleware('permission:view-reports')->only(['reports', 'generateReport']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = 'sales';
        if($request->ajax()){
            $sales = Sale::with('seller')->latest();
            return DataTables::of($sales)
                    ->addIndexColumn()
                    ->addColumn('product',function($sale){
                        $image = '';
                        if(!empty($sale->product)){
                            $image = null;
                            if(!empty($sale->product->purchase->image)){
                                $image = '<span class="avatar avatar-sm mr-2">
                                <img class="avatar-img" src="'.asset("storage/purchases/".$sale->product->purchase->image).'" alt="image">
                                </span>';
                            }
                            return $sale->product->purchase->product. ' ' . $image;
                        }                 
                    })
                    ->addColumn('total_price',function($sale){                   
                        return settings('app_currency','$').' '. $sale->total_price;
                    })
                    ->addColumn('date',function($row){
                        return date_format(date_create($row->created_at),'d M, Y');
                    })
                    ->addColumn('sold_by', function($sale){
                        return optional($sale->seller)->name ?? 'N/A';
                    })
                    ->addColumn('action', function ($row) {
                        $editbtn = '<a href="'.route("sales.edit", $row->id).'" class="editbtn"><button class="btn btn-info"><i class="fas fa-edit"></i></button></a>';
                        $deletebtn = '<a data-id="'.$row->id.'" data-route="'.route('sales.destroy', $row->id).'" href="javascript:void(0)" id="deletebtn"><button class="btn btn-danger"><i class="fas fa-trash"></i></button></a>';
                        if (!auth()->user()->hasPermissionTo('edit-sale')) {
                            $editbtn = '';
                        }
                        if (!auth()->user()->hasPermissionTo('destroy-sale')) {
                            $deletebtn = '';
                        }
                        $btn = $editbtn.' '.$deletebtn;
                        return $btn;
                    })
                    ->rawColumns(['product','action'])
                    ->make(true);

        }
        $products = Product::get();
        return view('admin.sales.index',compact(
            'title','products',
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'create sales';
        $products = Product::get();
        return view('admin.sales.create',compact(
            'title','products'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'product'  => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:999'
        ]);

        try {
            $purchase = DB::transaction(function () use ($request) {
                $sold_product = Product::findOrFail($request->product);
                $purchase = Purchase::lockForUpdate()->findOrFail($sold_product->purchase_id);

                // block expired medicine
                if (Carbon::parse($purchase->expiry_date)->lte(Carbon::today())) {
                    throw new \RuntimeException($purchase->product.' is expired and cannot be sold.');
                }

                // block selling more than the stock
                if ((int) $purchase->quantity < (int) $request->quantity) {
                    throw new \RuntimeException("Not enough stock. Only {$purchase->quantity} available.");
                }

                // deduct stock
                $purchase->update([
                    'quantity' => (int) $purchase->quantity - (int) $request->quantity,
                ]);

                // save the sale, including the cost at the time of sale
                Sale::create([
                    'product_id'  => $sold_product->id,
                    'user_id'     => auth()->id(),
                    'quantity'    => $request->quantity,
                    'unit_cost'   => $purchase->cost_price,
                    'total_price' => $request->quantity * $sold_product->price,
                ]);

                return $purchase;
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with(notify($e->getMessage(), 'danger'));
        }

        $notification = notify('Product has been sold');

        if ((int) $purchase->quantity === 1) {
            event(new PurchaseOutStock($purchase));
            $notification = notify('Product is running out of stock!!!', 'warning');
        }

        return redirect()->route('sales.index')->with($notification);
    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \app\Models\Sale $sale
     * @return \Illuminate\Http\Response
     */
    public function edit(Sale $sale)
    {
        $title = 'edit sale';
        $products = Product::get();
        return view('admin.sales.edit',compact(
            'title','sale','products'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \app\Models\Sale $sale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sale $sale)
    {
        $this->validate($request,[
            'product'  => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:999'
        ]);

        if ((int) $request->product === (int) $sale->product_id
            && (int) $request->quantity === (int) $sale->quantity) {
            return redirect()->route('sales.index')
                ->with(notify('No changes were made.', 'info'));
        }

        $newProduct = Product::find($request->product);
        if (!$newProduct || !$newProduct->purchase_id) {
            return back()->with(notify('Selected product is unavailable.', 'danger'));
        }

        try {
            $purchase = DB::transaction(function () use ($request, $sale, $newProduct) {
                $sameProduct = (int) $sale->product_id === (int) $newProduct->id;

                // Keep the original unit price if the product didn't change,
                // so old sales aren't repriced when the product price changes later.
                $unitPrice = ($sameProduct && (int) $sale->quantity > 0)
                    ? $sale->total_price / $sale->quantity
                    : $newProduct->price;

                // 1) Return the OLD quantity back to stock
                $oldProduct = Product::withTrashed()->find($sale->product_id);
                if ($oldProduct && $oldProduct->purchase_id) {
                    $oldPurchase = Purchase::lockForUpdate()->find($oldProduct->purchase_id);
                    if ($oldPurchase) {
                        $oldPurchase->update([
                            'quantity' => (int) $oldPurchase->quantity + (int) $sale->quantity,
                        ]);
                    }
                }

                // 2) Deduct the NEW quantity (re-read so the restored stock is counted)
                $purchase = Purchase::lockForUpdate()->find($newProduct->purchase_id);
                if (!$purchase || (int) $purchase->quantity < (int) $request->quantity) {
                    $available = $purchase ? (int) $purchase->quantity : 0;
                    // Throwing rolls back step 1 as well
                    throw new \RuntimeException("Not enough stock. Only {$available} available.");
                }
                $purchase->update([
                    'quantity' => (int) $purchase->quantity - (int) $request->quantity,
                ]);

                // 3) Update the sale (user_id is intentionally NOT changed)
                $sale->update([
                    'product_id'  => $newProduct->id,
                    'quantity'    => $request->quantity,
                    'unit_cost'   => $sameProduct ? $sale->unit_cost : $purchase->cost_price,   // NEW
                    'total_price' => round($unitPrice * (int) $request->quantity, 2),
                ]);

                return $purchase;
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with(notify($e->getMessage(), 'danger'));
        }

        $notification = notify('Sale has been updated');

        $remaining = (int) $purchase->quantity;
        if ($remaining === 1) {
            event(new PurchaseOutStock($purchase));
            $notification = notify('Product is running out of stock!!!', 'warning');
        }

        return redirect()->route('sales.index')->with($notification);
    }

    /**
     * Generate sales reports index
     *
     * @return \Illuminate\Http\Response
     */
    public function reports(Request $request){
        $title = 'sales reports';
        return view('admin.sales.reports',compact(
            'title'
        ));
    }

    /**
     * Generate sales report form post
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateReport(Request $request){
        $this->validate($request,[
            'from_date'   => 'required|date',
            'to_date'     => 'required|date|after_or_equal:from_date',
            'report_type' => 'required|in:normal,best_medicine,best_salesperson',
            'sort'        => 'required|in:asc,desc',
        ]);

        $from = $request->from_date;
        $to   = $request->to_date;
        $sort = $request->sort;
        $type = $request->report_type;

        $base = Sale::whereBetween(DB::raw('DATE(sales.created_at)'), [$from, $to]);

        if ($type === 'best_medicine') {
            $title = 'Best Selling Medicine Report';
            $rows = (clone $base)
                ->select('product_id',
                    DB::raw('SUM(quantity) as total_qty'),
                    DB::raw('SUM(total_price) as total_sales'),
                    DB::raw('SUM(quantity * unit_cost) as total_cost'),
                    DB::raw('SUM(total_price - (quantity * unit_cost)) as total_profit'),
                    DB::raw('COUNT(*) as transactions'))
                ->groupBy('product_id')
                ->orderBy('total_qty', $sort)
                ->with(['product' => function($q){ $q->withTrashed(); }, 'product.purchase'])
                ->get();

        } elseif ($type === 'best_salesperson') {
            $title = 'Best Sales Person Report';
            $rows = (clone $base)
                ->select('user_id',
                    DB::raw('SUM(quantity) as total_qty'),
                    DB::raw('SUM(total_price) as total_sales'),
                    DB::raw('SUM(quantity * unit_cost) as total_cost'),
                    DB::raw('SUM(total_price - (quantity * unit_cost)) as total_profit'),
                    DB::raw('COUNT(*) as transactions'))
                ->groupBy('user_id')
                ->orderBy('total_sales', $sort)
                ->with('seller')
                ->get();

        } else {
            $title = 'Sales Report';
            $rows = (clone $base)
                ->with(['seller', 'product.purchase'])
                ->orderBy('created_at', $sort)
                ->get();
        }

        return view('admin.sales.reports', [
            'title'       => 'sales reports',
            'rows'        => $rows,
            'report_type' => $type,
            'sort'        => $sort,
            'reportTitle' => $title,
            'from_date'   => $from,
            'to_date'     => $to,
        ]);
    }
}
