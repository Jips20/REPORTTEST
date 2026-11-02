<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SalesPersonStatusMail;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-users')->only('index');
        $this->middleware('permission:create-user')->only(['create', 'store']);
        $this->middleware('permission:edit-user')->only(['edit', 'update', 'approve', 'reject']);
        $this->middleware('permission:destroy-user')->only('destroy');
        // profile, updateProfile and updatePassword are left open on purpose:
        // every logged-in user must be able to edit their own profile
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = 'users';
        if ($request->ajax()) {
            $users = User::get();
            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('created_at', function ($category) {
                    return date_format(date_create($category->created_at), "d M,Y");
                })
                ->addColumn('avatar', function ($user) {
                    $src = asset('assets/img/avatar_1nn.png');
                    if (!empty($user->avatar)) {
                        $src = asset('storage/users/'.$user->avatar);
                    }
                    return '<img src="'.$src.'" class="avatar-img rounded-circle" width="50" />';
                })
                ->addColumn('role', function ($row) {
                    foreach ($row->getRoleNames() as $role) {
                        return '<span>'.$role.'</span>';
                    }
                })
                ->addColumn('status', function ($row) {
                    if ($row->status === 'approved') {
                        return '<span class="badge bg-success">Approved</span>';
                    } elseif ($row->status === 'rejected') {
                        return '<span class="badge bg-danger">Rejected</span>';
                    }
                    return '<span class="badge bg-warning">Pending</span>';
                })
                ->addColumn('action', function ($row) {
                    $editbtn = '<a href="'.route("users.edit", $row->id).'" class="editbtn"><button class="btn btn-info"><i class="fas fa-edit"></i></button></a>';
                    $deletebtn = '<a data-id="'.$row->id.'" data-route="'.route('users.destroy', $row->id).'" href="javascript:void(0)" id="deletebtn"><button class="btn btn-danger"><i class="fas fa-trash"></i></button></a>';
                    if (!auth()->user()->hasPermissionTo('edit-user')) {
                        $editbtn = '';
                    }
                    if (!auth()->user()->hasPermissionTo('destroy-user')) {
                        $deletebtn = '';
                    }

                    $approveRejectBtns = '';
                    if ($row->hasRole('sales-person') && $row->status === 'pending') {
                        $approveRejectBtns = '<a data-route="'.route('users.approve', $row->id).'" href="javascript:void(0)" class="approvebtn"><button class="btn btn-success"><i class="fas fa-check"></i></button></a> '
                            .'<a data-route="'.route('users.reject', $row->id).'" href="javascript:void(0)" class="rejectbtn"><button class="btn btn-warning"><i class="fas fa-times"></i></button></a>';
                    }

                    $btn = $editbtn.' '.$deletebtn.' '.$approveRejectBtns;
                    return $btn;
                })
                ->rawColumns(['avatar','role','status','action'])
                ->make(true);
        }
        return view('admin.users.index', compact(
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
        $title = 'create user';
        $roles = Role::get();
        return view('admin.users.create', compact('title','roles'));
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
            'name'=>'required|max:50|regex:/\A[A-Za-z0-9 ]+\z/',
            'email'=>'required|email|max:50|regex:/\A[A-Za-z0-9.@]+\z/',
            'role'=>'required',
            'password'=>'required|confirmed|min:8|max:15|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,15}$/',
            'password_confirmation'=>'required|min:8|max:15|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,15}$/',
            'avatar'=>'nullable|file|image|mimes:jpg,jpeg,gif,png',
        ], [
            'password.regex'=>'Password must be 8 to 15 characters and include both letters and numbers only.',
            'password_confirmation.regex'=>'Password confirmation must be 8 to 15 characters and include both letters and numbers only.',
        ]);
        $imageName = null;
        if ($request->hasFile('avatar')) {
            $imageName = time().'.'.$request->avatar->extension();
            $request->avatar->move(public_path('storage/users'), $imageName);
        }
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'avatar' => $imageName,
            'password' => Hash::make($request->password),
            'status' => 'approved',
        ]);
        $user->assignRole($request->role);
        $notifiation = notify('user created successfully');
        return redirect()->route('users.index')->with($notifiation);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \app\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $title = "edit user";
        $roles = Role::get();
        return view('admin.users.edit',compact(
            'title','roles','user'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \app\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->validate($request,[
            'name'=>'required|max:50|regex:/\A[A-Za-z0-9 ]+\z/',
            'email'=>[
                'required',
                'email',
                'max:50',
                'regex:/\A[A-Za-z0-9.@]+\z/',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role'=>'required',
            'password'=>'nullable|confirmed|min:8|max:15|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,15}$/',
            'password_confirmation'=>'required_with:password|min:8|max:15|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,15}$/',
            'avatar'=>'nullable|file|image|mimes:jpg,jpeg,gif,png',
        ], [
            'password.regex'=>'Password must be 8 to 15 characters and include both letters and numbers only.',
            'password_confirmation.regex'=>'Password confirmation must be 8 to 15 characters and include both letters and numbers only.',
        ]);

        $roleNames = $user->getRoleNames();
        $roleChanged = $roleNames->count() !== 1 || !$roleNames->contains($request->role);
        $imageName = $user->avatar;
        if ($request->hasFile('avatar')) {
            $imageName = time().'.'.$request->avatar->extension();
        }

        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
            'avatar' => $imageName,
        ]);

        if ($request->filled('password') && !Hash::check($request->password, $user->password)) {
            $user->password = Hash::make($request->password);
        }

        if (!$user->isDirty() && !$roleChanged) {
            return redirect()->route('users.index')
                ->with(notify('No changes were made.', 'info'));
        }

        if ($request->hasFile('avatar')) {
            $request->avatar->move(public_path('storage/users'), $imageName);
        }

        $user->save();
        if ($roleChanged) {
            $user->syncRoles([$request->role]);
        }

        $notification = notify('user updated successfully');
        return redirect()->route('users.index')->with($notification);
    }

    public function profile(){
        $title = 'user profile';
        $roles = Role::get();
        return view('admin.users.profile',compact(
            'title','roles'
        ));
    }

    public function updateProfile(Request $request,User $user){
        $rules = [
            'name' => 'required|min:5|max:50|regex:/\A[A-Za-z0-9 ]+\z/',
            'email' => 'required|email|max:30|regex:/\A[A-Za-z0-9.@]+\z/',
            'username' => 'nullable|min:3|max:200',
            'avatar' => 'nullable|file|image|mimes:jpg,jpeg,png,gif',
        ];
        $canEditRole = $request->user()->hasPermissionTo('edit-role');
        if ($canEditRole && $request->has('role')) {
            $rules['role'] = ['required', Rule::exists('roles', 'name')];
        }
        $this->validate($request, $rules);

        $roleNames = $user->getRoleNames();
        $roleChanged = $canEditRole && $request->has('role')
            && ($roleNames->count() !== 1 || !$roleNames->contains($request->role));
        $imageName = $user->avatar;
        if ($request->hasFile('avatar')) {
            $imageName = time().'.'.$request->avatar->extension();
        }

        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
            'avatar' => $imageName,
        ]);

        if (!$user->isDirty() && !$roleChanged) {
            return redirect()->route('profile')
                ->with(notify('No changes were made.', 'info'));
        }

        if ($request->hasFile('avatar')) {
            $request->avatar->move(public_path('storage/users'), $imageName);
        }

        if ($user->isDirty()) {
            $user->save();
        }
        if ($roleChanged) {
            $user->syncRoles([$request->role]);
        }

        $notification = notify('profile updated successfully');
        return redirect()->route('profile')->with($notification);
    }

    /**
     * Update current user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request, User $user)
    {
        $this->validate($request, [
            'current_password'=>'required|max:20|regex:/\A[\x21-\x7E]+\z/',
            'password'=>'required|min:8|max:15|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,15}$/|confirmed',
            'password_confirmation'=>'required|min:8|max:15|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,15}$/',
        ]);
        $verify_password = password_verify($request->current_password, $user->password);
        if ($verify_password) {
            $user->update(['password'=>Hash::make($request->password)]);
            $notification = notify('User password updated successfully!!!');
            $logout = auth()->logout();
            return back()->with($notification, $logout);
        } elseif(!$verify_password) {
            $notification = notify("Incorrect Old Password!!!",'danger');
            return back()->with($notification)->with('password_update_failed', true);
        }
    }

    /**
     * Approve a pending sales person and email them.
     *
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function approve(User $user)
    {
        $user->update(['status' => 'approved']);
        Mail::to($user->email)->send(new SalesPersonStatusMail($user, 'approved'));
        $notification = notify('Sales person approved successfully');
        return back()->with($notification);
    }

    /**
     * Reject a pending sales person and email them.
     *
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function reject(User $user)
    {
        $user->update(['status' => 'rejected']);
        Mail::to($user->email)->send(new SalesPersonStatusMail($user, 'rejected'));
        $notification = notify('Sales person rejected');
        return back()->with($notification);
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    */
    public function destroy(Request $request)
    {
        return User::findOrFail($request->id)->delete();
    }
}