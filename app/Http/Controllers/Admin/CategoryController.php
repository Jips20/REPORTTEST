<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-category')->only('index');
        $this->middleware('permission:create-category')->only('store');
        $this->middleware('permission:edit-category')->only('update');
        $this->middleware('permission:destroy-category')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = 'categories';
        if($request->ajax()){
            $categories = Category::get();
            return DataTables::of($categories)
                    ->addIndexColumn()
                    ->addColumn('created_at',function($category){
                        return date_format(date_create($category->created_at),"d M,Y");
                    })
                    ->addColumn('action',function ($row){
                        $editbtn = '<a data-id="'.$row->id.'" data-name="'.$row->name.'" href="javascript:void(0)" class="editbtn"><button class="btn btn-info"><i class="fas fa-edit"></i></button></a>';
                        $deletebtn = '<a data-id="'.$row->id.'" data-route="'.route('categories.destroy',$row->id).'" href="javascript:void(0)" id="deletebtn"><button class="btn btn-danger"><i class="fas fa-trash"></i></button></a>';
                        if(!auth()->user()->hasPermissionTo('edit-category')){
                            $editbtn = '';
                        }
                        if(!auth()->user()->hasPermissionTo('destroy-category')){
                            $deletebtn = '';
                        }
                        $btn = $editbtn.' '.$deletebtn;
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('admin.products.categories',compact(
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
            'name' => [
                'required',
                'max:50',
                'regex:/^[a-zA-Z0-9\s]+$/',
                Rule::unique('categories', 'name'),
            ],
        ],[
            'name.unique' => 'This category name already exists.',
        ]);

        Category::create([
            'name' => $request->name,
        ]);

        $notification = notify("Category has been added");
        return back()->with($notification);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request,[
            'id' => 'required|exists:categories,id',
            'name' => [
                'required',
                'max:50',
                'regex:/^[a-zA-Z0-9\s]+$/',
                // ignore the category being edited, so saving it
                // without changing the name still works
                Rule::unique('categories', 'name')->ignore($request->id),
            ],
        ],[
            'name.unique' => 'This category name already exists.',
        ]);

        $category = Category::findOrFail($request->id);
        $category->fill([
            'name' => $request->name,
        ]);

        if (!$category->isDirty()) {
            return back()->with(notify('No changes were made.', 'info'));
        }

        $category->save();
        $notification = notify("Category has been updated");
        return back()->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        return Category::findOrFail($request->id)->delete();
    }
}