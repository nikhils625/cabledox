<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use DataTables;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Hash;
use Mail;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('users.list')) {
            abort(404);
        }

        if ($request->ajax()) {

            $authUser = \Auth::user();

            $users = User::with(['roles'])->where('id', '>', 1);

            if(!$authUser->roles->isEmpty() && isset($authUser->roles[0]->id))
            {
                $users->where('client_id', $authUser->client_id);
            }
            
            if($request->status == '0' || $request->status == '1'){
                $users->where('status', $request->status);
            }
            $users = $users->orderBy('id', 'DESC')->get();

            return Datatables::of($users)
            ->addIndexColumn()
            ->addColumn('first_name', function($users){
                return $users->first_name;
            })
            ->addColumn('last_name', function($users){
                return $users->last_name;
            })
            ->addColumn('email', function($users){
                return $users->email;
            })
            ->addColumn('role', function($users){
                if(!@$users->roles->isEmpty() && @$users->roles->count() == 1) {
                    return @$users->roles[0]->name;
                } else {
                    return "--";
                }
            })
            ->addColumn('status', function($users){
                return  $users->status;
            })
            ->addColumn('action', function($row) {
                $btn = '';
                if(auth()->user()->can('users.view')) {
                    $btn .= '<a href="'.route('users.show', $row->id).'" data-toggle="tooltip" class="icons" title="View"><i class="fa fa-eye"></i></a> &nbsp; ';
                }
                if(auth()->user()->can('users.edit')) {
                    $btn .= '<a href="'.route('users.edit', $row->id).'" data-toggle="tooltip" class="icons" title="Edit"><i class="fa fa-edit"></i></a> &nbsp; ';
                }
                if(auth()->user()->can('users.delete')) {
                    $btn .= '<a href="javascript:void(0);" class="delete icons" data-route="'. route('users.destroy', $row->id).'" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"> </i></a>';
                }
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('users.create')) {
            abort(404);
        }

        $roles = Role::where('id', '>', 1)->pluck('name', 'id')->toArray();
        return view('users.add', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputs = $request->except('_token');
        $validator = (new User)->validateUsers($inputs);
        
        if ($validator->fails()) {
            return redirect()->back()
                   ->withErrors($validator)
                   ->withInput($inputs);
        }

        try {
            \DB::beginTransaction();

            $user = new User;
            $user->client_id  = \Auth::user()->client_id;
            $user->first_name = $request->first_name;
            $user->last_name  = $request->last_name;
            $user->email      = $request->email; 
            $user->phone      = $request->phone;
            $user->role_id    = $request->role_id;
            $user->status     = 0;
            $user->created_by = \Auth::user()->id;
            $user->save();

            // $role = Role::find(2);
            // $user->assignRole(3);
            $user->syncRoles($request->role_id);

            /*send email to contact person*/
            $userEmail = $user->email;

            $token = Password::getRepository()->create($user);
            $reset_password_link = url('/password/reset/'.$token.'?email='.$userEmail.'');          
            \Mail::send('emails/sendMailToContactPerson', array(                     
                'contact_person_name'  => $user->first_name.' '.$user->last_name,
                'contact_person_email' => $userEmail,
                'company_name'         => $user->clients->company_name,
                'reset_password_link'  => $reset_password_link,

            ), function($message) use ($request){   
                $message->to($request->email);
                $message->from(env('MAIL_FROM_ADDRESS'),
                    env('MAIL_FROM_NAME'))
                ->subject("Reset Password");
            });

            \DB::commit();
            
            return redirect()->back()->with('success', 'User details saved successfully.');
        } catch(\Exception $e) {
            \DB::rollBack();
            $message = 'Internal Server Error - ' . $e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id = null)
    {
        if(!auth()->user()->can('users.view')) {
            abort(404);
        }

        $user  = User::with(['roles'])->find($id);
        if(!$user) {
            abort(404);
        }
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id = null)
    {
        if(!auth()->user()->can('users.edit')) {
            abort(404);
        }

        $user  = User::with(['roles'])->find($id);
        if(!$user) {
            abort(404);
        }
        $roles = Role::where('id', '>', 1)->pluck('name', 'id')->toArray();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null)
    {
        $inputs = $request->except('_token', '_method');

        $validator = (new User)->validateUsers($inputs, $id);
        if ($validator->fails()) {

            return redirect()->back()
                   ->withErrors($validator)
                   ->withInput($inputs);
        }

        try {
            \DB::beginTransaction();

            $user = User::find($id);

            $user->first_name = $request->first_name;
            $user->last_name  =  $request->last_name;
            $user->email      = $request->email;
            $user->role_id    = $request->role_id;
            $user->save();

            $user->syncRoles($request->role_id);

            \DB::commit();
            
            return redirect()->back()->with('success', 'User details saved successfully.');
        } catch(\Exception $e) {
            \DB::rollBack();
            $message = 'Internal Server Error - ' . $e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id = null)
    {
        if(!auth()->user()->can('users.delete')) {
            abort(404);
        }

        $user  = User::with(['roles'])->find($id);
        if(!$user) {
            $message = 'User not found.';
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }

        try {
            \DB::beginTransaction();

            $res = $user->delete();

            \DB::commit();

            $message = 'Error in deleting user, please try again later.';
            $result  = ['status' => 0, 'message' => $message];
            if($res) {
                $message = 'User deleted successfully';
                $result  = ['status' => 1, 'message' => $message];
            }
            
            return response()->json($result);
        } catch(\Exception $e) {
            \DB::rollBack();
            $message = 'Internal Server Error - ' . $e->getMessage();
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }
    }

    /**
     * Show the form for change password for the logged in user.
     *
     * @return \Illuminate\Http\Response
     */
    public function changePasswordForm()
    {
        $user  = \Auth::user();
        if(!$user) {
            abort(404);
        }
        return view('users.change_password_layout', compact('user'));
    }

    /**
     * changePassword instance.
     *
     * @return void
     */
    public function changePassword(Request $request)
    {
        $validatedData = $request->validate([
            'old_password' => 'required',
            'password'     => 'required|string|min:8',
            'confirm_password' => 'required|same:password',
        ]);

        try {
            \DB::beginTransaction();

            $userId   = \Auth::id();
            $users    = User::find($userId);

            if (!(Hash::check($request->old_password, $users->password ))) {
                // The passwords matches
                return redirect()->back()->with("error", "Your old password is not macth. Please try again.");
            }

            if(strcmp($request->old_password, $request->password) == 0){
                //Current password and new password are same
                return redirect()->back()->with("error", "New Password cannot be same as your current password. Please choose a different password.");
            }

            $password = Hash::make($request->password);
            $users->password = $password;
            $users->save();

            \DB::commit();

            return redirect()->back()->with("success", "Password changed successfully!");
        } catch(\Exception $e) {
            \DB::rollBack();
            $message = 'Internal Server Error - ' . $e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        }
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
    ** change active/inactive status on list client**/
    public function changeStatus(Request $request)
    {
        $user = User::find($request->id);
        if(!$user) {
            $message = 'User not found.';
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }

        try {
            \DB::beginTransaction();

            $user->status = $request->status;
            $res = $user->save();

            \DB::commit();

            $message = 'Error in changing status, please try again later.';
            $result  = ['status' => 0, 'message' => $message];
            if($res) {
                $message = 'User status changed successfully.';
                $result  = ['status' => 1, 'message' => $message];
            }            
            return response()->json($result);
        } catch(\Exception $e) {
            $message = 'Internal Server Error - ' . $e->getMessage();
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function myProfile()
    {
        $user  = \Auth::user();
        if(!$user) {
            abort(404);
        }
        $roles = Role::where('id', '>', 1)->pluck('name', 'id')->toArray();
        return view('profile.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request, $id = null)
    {
        $inputs = $request->except('_token', '_method');

        $validator = (new User)->validateUserProfile($inputs, $id);
        if ($validator->fails()) {

            return redirect()->back()
                   ->withErrors($validator)
                   ->withInput($inputs);
        }

        $dir = \Config::get('constants.uploadPaths.uploadProfileImage');

        try {
            \DB::beginTransaction();

            $user = User::find($id);

            $user->first_name = $request->first_name;
            $user->last_name  = $request->last_name;
            $user->email      = $request->email;
            $user->phone      = $request->phone;
            /*user profile*/
            if($request->hasFile('user_profile')){

                if(!empty($user->user_profile) && file_exists($dir . $user->user_profile)) {
                    unlink($dir . $user->user_profile);
                }

                $fileOzName = str_replace(' ', '', $request->file('user_profile')->            getClientOriginalName());
                $fileOzExtension = $request->file('user_profile')->getClientOriginalExtension();
                $fileName   = time().'_'.pathinfo(strtolower($fileOzName), PATHINFO_FILENAME).'.'.$fileOzExtension;

                $request->file('user_profile')->move($dir, $fileName);

                $user->user_profile = $fileName;
            }
            $user->save();

            \DB::commit();
            
            return redirect()->back()->with('success', 'User details saved successfully.');
        } catch(\Exception $e) {
            \DB::rollBack();
            $message = 'Internal Server Error - ' . $e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        }
    }

    /*
        Assign a specific permission to a specific user
    */
    function givePermission()
    {
        $role = Role::find(4); 
        $permission = ['termination-details.add'];
        $role->givePermissionTo($permission);
        echo "Permission given";
    }

    /*
        Revoke a specific permission from a specific user
    */
    function revokePermission()
    {
        $role = Role::find(1);
        $permission = 'clients.create';
        $role->revokePermissionTo($permission);
        echo "Permission revoked";
    }

    /*
        Create new permission and assign it to super-admin
    */
    function createPermission()
    {
        // dd('create-permission die');
        $permissionName = 'termination-details.add';

        $data = [
            'name' => $permissionName,
            'guard_name' => 'web',
        ];
        Permission::firstOrCreate($data);
        $role = Role::find(2);
        $permission = $permissionName;
        $role->givePermissionTo($permission);
        echo "Permission created";
    }
}