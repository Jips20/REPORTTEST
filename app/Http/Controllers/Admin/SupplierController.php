<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-supplier')->only('index');
        $this->middleware('permission:create-supplier')->only(['create', 'store']);
        $this->middleware('permission:edit-supplier')->only(['edit', 'update']);
        $this->middleware('permission:destroy-supplier')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = 'suppliers';
        if($request->ajax()){
            $suppliers = Supplier::get();
            return DataTables::of($suppliers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editbtn = '<a href="'.route("suppliers.edit", $row->id).'" class="editbtn"><button class="btn btn-info"><i class="fas fa-edit"></i></button></a>';
                    $deletebtn = '<a data-id="'.$row->id.'" data-route="'.route('suppliers.destroy', $row->id).'" href="javascript:void(0)" id="deletebtn"><button class="btn btn-danger"><i class="fas fa-trash"></i></button></a>';
                    if (!auth()->user()->hasPermissionTo('edit-supplier')) {
                        $editbtn = '';
                    }
                    if (!auth()->user()->hasPermissionTo('destroy-supplier')) {
                        $deletebtn = '';
                    }
                    $btn = $editbtn.' '.$deletebtn;
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    
        return view('admin.suppliers.index',compact(
            'title'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'create supplier';
        return view('admin.suppliers.create',compact(
            'title'
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
            'name'=>[
                'required',
                'max:30',
                'regex:/\A[A-Za-z0-9 .]+\z/',
                Rule::unique('suppliers', 'name'),
            ],
            'product'=>'required|max:50|regex:/\A[A-Za-z0-9 ]+\z/',
            'email'=>'nullable|email|max:50|regex:/\A[A-Za-z0-9.@]+\z/',
            'phone'=>'nullable|digits:11',
            'company'=>'required|max:60|regex:/\A[A-Za-z0-9 ]+\z/',
            'address'=>'required|max:80|regex:/\A[A-Za-z0-9 .-]+\z/',
            'comment' =>'nullable|max:300|regex:/\A[A-Za-z0-9 .]*\z/',
        ],[
            'name.unique' => 'This supplier name already exists.',
        ]);
        Supplier::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'company'=>$request->company,
            'address'=>$request->address,
            'product'=>$request->product,
            'comment'=>$request->comment,
        ]);
        $notification = notify("Supplier has been added");
        return redirect()->route('suppliers.index')->with($notification);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \app\Models\Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        $title = 'edit supplier';
        return view('admin.suppliers.edit',compact(
            'title','supplier'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \app\Models\Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        $this->validate($request,[
            'name'=>[
                'required',
                'max:30',
                'regex:/\A[A-Za-z0-9 .]+\z/',
                // ignore the supplier being edited, so saving it
                // without changing the name still works
                Rule::unique('suppliers', 'name')->ignore($supplier->id),
            ],
            'product'=>'required|max:50|regex:/\A[A-Za-z0-9 ]+\z/',
            'email'=>'nullable|email|max:50|regex:/\A[A-Za-z0-9.@]+\z/',
            'phone'=>'nullable|digits:11',
            'company'=>'required|max:60|regex:/\A[A-Za-z0-9 ]+\z/',
            'address'=>'required|max:80|regex:/\A[A-Za-z0-9 .-]+\z/',
            'comment' =>'nullable|max:300|regex:/\A[A-Za-z0-9 .]*\z/',
        ],[
            'name.unique' => 'This supplier name already exists.',
        ]);
        $supplier->fill([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'company'=>$request->company,
            'address'=>$request->address,
            'product'=>$request->product,
            'comment'=>$request->comment,
        ]);

        if (!$supplier->isDirty()) {
            return redirect()->route('suppliers.index')
                ->with(notify('No changes were made.', 'info'));
        }

        $supplier->save();
        $notification = notify("Supplier has been updated");
        return redirect()->route('suppliers.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        return Supplier::findOrFail($request->id)->delete();
    }
}