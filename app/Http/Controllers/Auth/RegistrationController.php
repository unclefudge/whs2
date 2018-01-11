<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Session;
use App\User;
use App\Models\Company\Company;
use App\Models\Misc\Role2;
use nilsenj\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegistrationController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Create a new registration - signup form
     *
     */
    protected function create()
    {
        return view('auth/signup');
    }

    /**
     * Store new registration - create company/user
     *
     */
    protected function store()
    {
        //
    }

    /**
     * Create a new Referred Registration - signup form
     *
     */
    protected function refCreate($key)
    {
        list($company_id, $rest) = explode('-', $key, 2);
        $company = Company::find($company_id);
        if ($company && $company->signup_key == $key) {
            return view('auth/signup-referred', compact('company'));
        }

        return view('errors/404');
    }

    /**
     * Store new Referred Registration - create user
     */
    protected function refStore(Request $request)
    {
        $this->validate(request(), [
            'username'           => 'required|min:3|max:50|unique:users,username,',
            'password'           => 'required|confirmed|min:3',
            'firstname'          => 'required',
            'lastname'           => 'required',
            'email'              => 'required|email|max:255|unique:users,email,NULL',
            'employment_type'    => 'required',
            'subcontractor_type' => 'required_if:employment_type,2',
        ]);

        list($company_id, $rest) = explode('-', request('signup_key'), 2);
        $company = Company::find($company_id);

        $user_request = removeNullValues($request->all());
        $user_request['company_id'] = $company->id;
        $user_request['security'] = 1;
        $user_request['password'] = bcrypt($user_request['password']);  // encrypt password from form
        $user_request['password_reset'] = 0;

        // Empty State field if rest of address fields are empty
        if (!$request->filled('address') && !$request->filled('suburb') && !$request->filled('postcode'))
            $user_request['state'] = null;

        // Create user
        $user = User::create($user_request);
        $user->save();

        // Updated Created fields as user self created prior to being authenticated
        $user->created_by = $user->id;

        //
        // Attach Role + Permission
        //

        // Attach parent company default primary_user role
        $primary_user_role = Role2::where('company_id', $company->reportsToCompany()->id)->where('child', 'primary')->first();
        if ($primary_user_role)
            $user->attachRole2($primary_user_role->id);

        // Attach permissions required for primary user
        $user->attachPermission2(1, 99, $company->id);  // View all users
        $user->attachPermission2(3, 99, $company->id);  // Edit all users
        $user->attachPermission2(5, 1, $company->id);   // Create users
        $user->attachPermission2(7, 1, $company->id);   // Archive users
        $user->attachPermission2(241, 1, $company->id); // Signoff users
        $user->attachPermission2(9, 99, $company->id);  // View company
        $user->attachPermission2(11, 99, $company->id); // Edit company

        // Update Company Primary User + Signup step
        $company->primary_user = $user->id;
        $company->signup_step = 2;
        $company->nickname = null;
        $company->save();


        // Sign in User
        auth()->login($user);

        Toastr::success("Signed Up");

        return redirect("company/$company->id/edit");
    }

}
