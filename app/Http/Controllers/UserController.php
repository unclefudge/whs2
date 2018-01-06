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
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    /*protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|min:3|max:50|unique:users',
            'email'    => 'email|max:255|unique:users',
            'password' => 'required|min:3',
        ]);
    }*/

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

        $user_request = $request->except('roles');
        $user_request['password'] = bcrypt($user_request['password']);  // encrypt password from form

        // Empty State field if rest of address fields are empty
        if (!$user_request['address'] && !$user_request['suburb'] && !$user_request['postcode'])
            $user_request['state'] = '';

        // Null email field if empty  - for unique validation
        if (!$user_request['email'])
            $user_request['email'] = null;

        // Create User
        $user = User::create($user_request);
        Toastr::success("Created new user");

        // Attach Roles
        foreach ($request->get('roles') as $role)
            $user->attachRole($role);


        // Email new User
        if (\App::environment('prod')) {
            $email_list = "tara@capecod.com.au; gary@capcod.com.au";
            $email_list = explode(';', $email_list);
            $email_list = array_map('trim', $email_list); // trim white spaces

            $email_user = Auth::user()->email;
            $data = [
                'date'         => $user->created_at->format('d/m/Y g:i a'),
                'username'     => $user->username,
                'fullname'     => $user->fullname,
                'company_name' => $user->company->name,
                'created_by'   => Auth::user()->fullname,
                'site_owner'   => Auth::user()->company->name,
            ];
            Mail::send('emails/new-user', $data, function ($m) use ($email_list) {
                $m->from('do-not-reply@safeworksite.net');
                $m->to($email_list);
                $m->subject('New User Notification');
            });
        }

        return redirect('user');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {
        $user = User::where(compact('username'))->firstorFail();

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
    public function showSettings($username, $tab = 'info')
    {
        $user = User::where(compact('username'))->firstOrFail();

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.user', $user))
            return view('errors/404');

        $tabs = ['settings', $tab];

        if ($tab == 'password' && Auth::user()->password_reset)
            Toastr::warning("Your password was reset by an admin and you are required to choose an new one");


        return view('user/show', compact('user', 'tabs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $username)
    {
        $user = User::where(compact('username'))->firstOrFail();

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.user', $user))
            return view('errors/404');

        $user_request = $request->except('tabs');

        $tabs = $request->get('tabs');
        $password_reset = false;

        switch ($tabs) {
            case 'settings:info': {
                // Empty State field if rest of address fields are empty
                if (!$request['address'] && !$request['suburb'] && !$request['postcode'])
                    $user_request['state'] = '';

                // Null email field if empty  - for unique validation
                if (!$request['email'])
                    $user_request['email'] = null;

                // Update status
                if ($request->get('status')) {
                    $user_request['status'] = 1;
                } else {
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
                    $user_request['status'] = 0;
                }
            }
            case 'settings:password': {
                // encrypt password
                if ($request->get('password'))
                    $user_request['password'] = bcrypt($user_request['password']);

                // Password has ben set by someone other then user so force user to reset after login
                if (Auth::user()->id != $user->id)
                    $user_request['password_reset'] = 1;

                // Password has been reset by user after being set by another
                if (Auth::user()->password_reset && Auth::user()->id == $user->id) {
                    $user_request['password_reset'] = 0;
                    $password_reset = true;
                }
            }
        }

        $user->update($user_request);

        if ($password_reset) {
            Toastr::success("Updated password");
            sleep(1);

            return redirect('/dashboard');
        }

        Toastr::success("Saved changes");
        $tabs = explode(':', $request->get('tabs'));

        return redirect('/user/' . $user->username . '/' . $tabs[0] . '/' . $tabs[1]);
    }

    /**
     * Update the photo on user model resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
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
    }

    /**
     * Update the security for user resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateSecurity(Request $request, $username)
    {
        $user = User::where(compact('username'))->firstOrFail();

        // Check authorisation and throw 404 if not
        if (!Auth::user()->security || !Auth::user()->allowed2('edit.user', $user))
            return view('errors/404');


        // Update Security but ensure at least one user from company has security access
        $security_count = User::where('company_id', $user->company_id)->where('security', '1')->where('status', '1')->get()->count();
        if ($user->security && !$request->has('security') && $security_count < 2) {
            Toastr::warning("Unable to remove Security Access because at least one user within company must have it.");
        } else {
            $user->security = $request->has('security') ? 1 : 0;
            $user->save();
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

        return redirect('/user/' . $user->username . '/settings/security');
    }

    /**
     * Get the security permissions for user resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSecurityPermissions(Request $request, $username)
    {
        $user = User::where(compact('username'))->firstOrFail();
        $permissions = DB::table('permission_user')
            ->where('user_id', $user->id)
            ->lists('permission_id');
        foreach ($permissions as $permission)
            $array[] = "$permission";

        return $array;
    }


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
            'companys.name',
            'users.address', 'users.last_login', 'users.status'])
            ->join('companys', 'users.company_id', '=', 'companys.id')
            ->whereIn('users.id', $user_list)
            ->where('users.status', $request->get('status'));

        $dt = Datatables::of($user_records)
            //->filterColumn('full_name', 'whereRaw', "CONCAT(users.firstname,' ',users.lastname) like ?", ["%$1%"])
            ->editColumn('id', '<div class="text-center"><a href="/user/{{$username}}"><i class="fa fa-search"></i></a></div>')
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
            ->editColumn('name', '<a href="company/{{$company_id}}">{{$name}}</a>')
            ->editColumn('last_login', function ($user) {
                return ($user->last_login != '-0001-11-30 00:00:00') ? with(new Carbon($user->last_login))->format('d/m/Y') : 'never';
            })
            ->removeColumn('slug')
            ->addColumn('action', function ($user) {
                if (Auth::user()->allowed2('edit.user', $user))
                    return '<a href="/user/' . $user->username . '/settings" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-pencil"></i> Edit</a>';
            })
            ->rawColumns(['id', 'full_name', 'name', 'action'])
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
            ->filterColumn('full_name', 'whereRaw', "CONCAT(users.firstname,' ',users.lastname) like ?", ["%$1%"])
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
            ->addColumn('action', function ($user) {
                if (Auth::user()->allowed2('edit.user', $user))
                    return '<a href="/user/' . $user->username . '/settings" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-pencil"></i> Edit</a>';
            })
            ->rawColumns(['id', 'full_name', 'name', 'action'])
            ->make(true);

        //var_dump($dt);

        return $dt;
    }
}
