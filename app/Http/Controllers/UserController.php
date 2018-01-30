<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Mail;
use Carbon\Carbon;
use App\User;
use App\Models\Company\Company;
use App\Models\Misc\Role2;
use App\Models\Misc\Permission2;
use App\Models\Misc\PermissionRoleCompany;
use App\Http\Requests;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;


/**
 * Class CompaniesController
 * @package App\Http\Controllers
 */
class UserController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasAnyPermissionType('user'))
            return view('errors/404');

        return view('user/list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.user'))
            return view('errors/404');

        $role_type = '';

        return view('user/create', compact('role_type'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.user'))
            return view('errors/404');

        $user_request = removeNullValues($request->all());
        $user_request['company_id'] = Auth::user()->company_id;
        $user_request['password'] = bcrypt($user_request['password']);  // encrypt password from form
        $user_request['password_reset'] = 1;

        // Empty State field if rest of address fields are empty
        if (!$request->filled('address') && !$request->filled('suburb') && !$request->filled('postcode'))
            $user_request['state'] = null;

        // Null email field if empty  - for unique validation
        if (!$user_request['email'])
            $user_request['email'] = null;

        // Create User
        $user = User::create($user_request);
        Toastr::success("Created new user");

        // Attach parent company default child role
        if ($user->company->parent_company) {
            $default_user_role = Role2::where('company_id', $user->company->reportsTo()->id)->where('child', 'default')->first();
            if ($default_user_role)
                $user->attachRole2($default_user_role->id);
        }

        // Attached Company Own Roles
        if ($request->filled('roles'))
            foreach ($request->get('roles') as $role_id)
                $user->attachRole2($role_id);


        // Send out Welcome Email to user
        Mail::to($user)->send(new \App\Mail\User\UserWelcome($user, request('password')));

        // Notify company + parent company new user created
        if ($user->company->subscription && $user->company->notificationsUsersType('user.created'))
            Mail::to($user->company->notificationsUsersType('user.created'))->send(new \App\Mail\User\UserCreated($user, Auth::user()));
        if ($user->company->parent_company && $user->company->reportsTo()->notificationsUsersType('user.created'))
            Mail::to($user->company->reportsTo()->notificationsUsersType('user.created'))->send(new \App\Mail\User\UserCreated($user, Auth::user()));

        // Signup Process - Initial update
        if ($user->company->signup_step == 3) {
            return redirect("company/" . $user->company->id . "/signup/3");
        }

        return redirect('user');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findorFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.user', $user))
            return view('errors/404');

        $tabs = ['profile', 'info'];

        return view('user/show', compact('user', 'tabs'));
    }

    /**
     * Display the settings for the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showSecurity($id)
    {
        $user = User::findorFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.user', $user))
            return view('errors/404');

        return view('user/security', compact('user'));
    }

    /**
     * Edit the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $tab = 'info')
    {
        $user = User::findorFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.user', $user))
            return view('errors/404');

        if (Auth::user()->password_reset)
            Toastr::warning("Your password was reset by an admin and you are required to choose an new one");

        return view('user/edit', compact('user'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $username)
    {
        $user = User::where(compact('username'))->firstOrFail();

        // Validate
        $this->validate(request(), [
            'username'           => 'required|min:3|max:50|unique:users,username,' . $user->id,
            'firstname'          => 'required',
            'lastname'           => 'required',
            'email'              => 'required_if:status,1|email|max:255|unique:users,email,' . $user->id . ',id',
            'roles'              => 'required_if:subscription,1',
            //'employment_type'    => 'required',
            //'subcontractor_type' => 'required_if:employment_type,2',
        ]);

        if (request()->filled('password') || request()->filled('password_force')) {
            $this->validate(request(), [
                'password' => 'required:|confirmed|min:3',
            ]);

        }

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.user', $user))
            return view('errors/404');

        $user_request = removeNullValues($request->all());
        $password_reset = false;

        // Empty State field if rest of address fields are empty
        if (!$request->filled('address') && !$request->filled('suburb') && !$request->filled('postcode'))
            $user_request['state'] = null;

        // Null email field if empty  - for unique validation
        if (!$user_request['email'])
            $user_request['email'] = null;

        // If user being made inactive then update email
        if ($request->filled('status') && $request->get('status') == 0) {
            // If user being made inactive and has email then append 'achived-userid' to front to allow
            // for the email to be potentially reused by another user
            if ($user->status && $user->email) {
                Toastr::warning("Updated email");
                $user_request['email'] = 'archived-' . $user->id . '-' . $user->email;
                if ($user_request['notes'])
                    $user_request['notes'] .= "\nupdated email to " . $user_request['email'] . ' due to archiving';
                else
                    $user_request['notes'] = "updated email to " . $user_request['email'] . ' due to archiving';
            }
        }

        // Encrypt password
        if ($request->filled('password'))
            $user_request['password'] = bcrypt($user_request['password']);
        // Password has ben set by someone other then user so force user to reset after login
        if ($request->filled('newpassword')) {
            $user_request['password'] = bcrypt($user_request['newpassword']);
            $user_request['password_reset'] = 1;
        }

        if ($request->filled('password')) {
            // Password has been reset by user after being set by another
            if (Auth::user()->password_reset && Auth::user()->id == $user->id) {
                $user_request['password_reset'] = 0;
                $password_reset = true;
            }
        }

        //dd($user_request);

        // Update User
        $user->update($user_request);

        if ($password_reset) {
            Toastr::success("Updated password");
            sleep(1);

            return redirect('/dashboard');
        }

        Toastr::success("Saved changes");

        // Signup Process - Initial update
        if (Auth::user()->company->status == 2)
            return redirect("company/".Auth::user()->company_id."/edit");   // Adding company info

        return redirect('/user/' . $user->id);
    }

    /**
     * Update the security for user resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateSecurity(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->security || !Auth::user()->allowed2('edit.user', $user))
            return view('errors/404');


        // Update Security but ensure at least one user from company has security access
        if (Auth::user()->isCompany($user->company_id)) {
            $security_count = User::where('company_id', $user->company_id)->where('security', '1')->where('status', '1')->get()->count();
            if ($user->security && !$request->has('security') && $security_count < 2) {
                Toastr::warning("Unable to remove Security Access because at least one user within company must have it.");
            } else {
                $user->security = $request->has('security') ? 1 : 0;
                $user->save();
            }
        }

        // Update Permissions
        $permissions = Permission2::all();
        $user->detachAllPermissions2(Auth::user()->company_id);
        foreach ($permissions as $permission) {
            // Add Permission if user given higher level then one of their roles.
            if ($request->get("p$permission->id") != 0 && $request->get("p$permission->id") > $user->rolesPermissionLevel($permission->id, Auth::user()->company_id)) {
                //echo "added $permission->id [".$request->get("p$permission->id")."/".$user->rolesPermissionLevel($permission->id, Auth::user()->company_id)."] to $user->fullname for company[".Auth::user()->company_id."]<br>";
                $user->attachPermission2($permission->id, $request->get("p$permission->id"), Auth::user()->company_id);
            }
        }

        // Update Roles
        $roles = $request->get('roles');
        $user->detachAllRoles2(Auth::user()->company_id);
        if ($roles) {
            foreach ($roles as $role)
                $user->attachRole2($role);
        }

        //dd($request->all());

        Toastr::success("Saved changes");

        return redirect('/user/' . $user->id . '/security');
    }

    /**
     * Get the security permissions for user resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
		 /*
    public function getSecurityPermissions(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $permissions = DB::table('permission_user')
            ->where('user_id', $user->id)
            ->lists('permission_id');
        foreach ($permissions as $permission)
            $array[] = "$permission";

        return $array;
    }*/

    /**
     * Update the photo on user model resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    /*
    public function updatePhoto(UserRequest $request, $username)
    {
        $user = User::where(compact('username'))->firstOrFail();

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.user', $user))
            return view('errors/404');

        $file = $request->file('photo');
        $path = "filebank/users/" . $user->id;
        $name = "photo." . strtolower($file->getClientOriginalExtension());
        $path_name = $path . '/' . $name;
        $file->move($path, $name);

        Image::make(url($path_name))
            ->fit(740)
            ->save($path_name);

        $user->photo = $path_name;
        $user->save();
        Toastr::success("Saved changes");

        return redirect('/user/' . $user->username . '/settings/photo');
    }*/


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function contractorList()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->isSupervisor())
            return view('errors/404');

        return view('user/list-contractors');
    }

    /**
     * Get Users current user is authorised to manage + Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers(Request $request)
    {
        $user_list = [Auth::user()->id];
        if (Auth::user()->hasAnyPermissionType('user'))
            $user_list = Auth::user()->authUsers('view.user')->pluck('id')->toArray();

        $user_records = User::select([
            'users.id', 'users.username', 'users.firstname', 'users.lastname', 'users.company_id', 'users.security', 'users.company_id',
            DB::raw('CONCAT(users.firstname, " ", users.lastname) AS full_name'),
            'companys.name', 'users.address', 'users.last_login', 'users.status'])
            ->join('companys', 'users.company_id', '=', 'companys.id')
            ->whereIn('users.id', $user_list)
            ->where('users.status', $request->get('status'));

        $dt = Datatables::of($user_records)
            //->filterColumn('full_name', 'whereRaw', "CONCAT(users.firstname,' ',users.lastname) like ?", ["%$1%"])
            ->editColumn('id', '<div class="text-center"><a href="/user/{{$id}}"><i class="fa fa-search"></i></a></div>')
            ->editColumn('full_name', function ($user) {
                $string = $user->firstname . ' ' . $user->lastname;

                if ($user->id == $user->company->primary_user)
                    $string .= " <span class='badge badge-info badge-roundless'>P</span>";
                if ($user->id == $user->company->secondary_user)
                    $string .= " <span class='badge badge-info badge-roundless'>S</span>";
                if ($user->security)
                    $string .= " <span class='badge badge-warning badge-roundless'>Sec</span>";

                return $string;
            })
            ->editColumn('name', function ($user) {
                $cname = $user->company->name;
                $cid = $user->company->id;
                return '<a href="company/'.$cid.'">'.$cname.'</a>';
            })
            ->editColumn('last_login', function ($user) {
                return ($user->last_login != '-0001-11-30 00:00:00') ? with(new Carbon($user->last_login))->format('d/m/Y') : 'never';
            })
            ->removeColumn('slug')
            ->rawColumns(['id', 'full_name', 'name'])
            ->make(true);

        return $dt;
    }

    /**
     * Get Users current user is authorised to manage + Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContractors(Request $request)
    {
        $user_list = Auth::user()->company->users($request->get('status'))->pluck('id')->toArray();

        $user_records = User::select([
            'users.id', 'users.username', 'users.firstname', 'users.lastname', 'users.phone', 'users.email', 'users.company_id', 'users.security',
            DB::raw('CONCAT(users.firstname, " ", users.lastname) AS full_name'),
            'companys.name',
            'users.address', 'users.last_login', 'users.status'])
            ->join('companys', 'users.company_id', '=', 'companys.id')
            ->where('users.company_id', '<>', Auth::user()->company_id)
            ->whereIn('users.id', $user_list);

        $dt = Datatables::of($user_records)
            //->filterColumn('full_name', 'whereRaw', "CONCAT(users.firstname,' ',users.lastname) like ?", ["%$1%"])
            ->editColumn('full_name', function ($user) {
                $company = Company::find($user->company_id);
                $string = $user->firstname . ' ' . $user->lastname;

                if ($user->id == $company->primary_user)
                    $string .= " <span class='badge badge-info badge-roundless'>P</span>";
                if ($user->id == $company->secondary_user)
                    $string .= " <span class='badge badge-info badge-roundless'>S</span>";
                if ($user->security)
                    $string .= " <span class='badge badge-warning badge-roundless'>Sec</span>";

                return $string;
            })
            ->editColumn('name', '<a href="company/{{$id}}">{{$name}}</a>')
            ->editColumn('last_login', function ($user) {
                return ($user->last_login != '-0001-11-30 00:00:00') ? with(new Carbon($user->last_login))->format('d/m/Y') : 'never';
            })
            ->removeColumn('slug')
            ->rawColumns(['id', 'full_name', 'name'])
            ->make(true);

        //var_dump($dt);

        return $dt;
    }
}
