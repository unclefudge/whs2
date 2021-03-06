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
        return view('company/signup/welcome');
    }

    /**
     * Create a new Referred Registration - Welcome
     *
     */
    protected function refCreate($key)
    {
        list($company_id, $rest) = explode('-', $key, 2);
        $company = Company::find($company_id);
        if ($company && $company->signup_key == $key) {
            return view('company/signup/welcome-referred', compact('company'));
        }

        return view('errors/404');
    }

    /**
     * Create a new registration - Primary User
     *
     */
    protected function primaryCreate($key)
    {
        list($company_id, $rest) = explode('-', $key, 2);
        $company = Company::find($company_id);
        if ($company && $company->signup_key == $key) {
            return view('company/signup/primary-create', compact('company'));
        }

        return view('errors/404');
    }


    /**
     * Store new registration - Primary user
     */
    protected function primaryStore()
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

        $user_request = removeNullValues(request()->all());
        $user_request['company_id'] = $company->id;
        $user_request['security'] = 1;
        $user_request['password'] = bcrypt($user_request['password']);  // encrypt password from form
        $user_request['password_reset'] = 0;
        $user_request['created_by'] = 1;
        $user_request['updated_by'] = 1;

        // Empty State field if rest of address fields are empty
        if (!request()->filled('address') && !request()->filled('suburb') && !request()->filled('postcode'))
            $user_request['state'] = null;

        //dd($user_request);

        // Create user
        $user = User::create($user_request);
        $user->save();

        // Updated Created fields as user self created prior to being authenticated
        $user->created_by = $user->id;

        //
        // Attach Role + Permission
        //

        // Attach parent company default primary_user role
        $primary_user_role = Role2::where('company_id', $company->reportsTo()->id)->where('child', 'primary')->first();
        if ($primary_user_role)
            $user->attachRole2($primary_user_role->id);

        // Attach permissions required for primary user
        $user->attachPermission2(1, 99, $company->id);  // View all users
        $user->attachPermission2(3, 99, $company->id);  // Edit all users
        $user->attachPermission2(5, 1, $company->id);   // Add users
        $user->attachPermission2(7, 1, $company->id);   // Dell users
        $user->attachPermission2(241, 1, $company->id); // Signoff users
        $user->attachPermission2(379, 99, $company->id);   // View users contact
        $user->attachPermission2(380, 99, $company->id);   // Edit users contact
        $user->attachPermission2(384, 99, $company->id);   // View users security
        $user->attachPermission2(385, 99, $company->id);   // Edit users security

        $user->attachPermission2(9, 99, $company->id);   // View company details
        $user->attachPermission2(11, 99, $company->id);  // Edit company details
        $user->attachPermission2(13, 1, $company->id);   // Add company details
        $user->attachPermission2(15, 1, $company->id);   // Del company details
        $user->attachPermission2(308, 99, $company->id); // View business details
        $user->attachPermission2(309, 99, $company->id); // Edit business details
        $user->attachPermission2(312, 1, $company->id);  // Signoff business details
        $user->attachPermission2(313, 99, $company->id); // View contruction details
        $user->attachPermission2(314, 99, $company->id); // Edit contruction details
        $user->attachPermission2(317, 1, $company->id);  // Signoff contruction details
        $user->attachPermission2(303, 99, $company->id); // View WHS details
        $user->attachPermission2(304, 99, $company->id); // Edit WHS details
        $user->attachPermission2(307, 1, $company->id);  // Signoff WHS details

        // Update Company Primary User + Signup step
        $company->primary_user = $user->id;
        $company->signup_step = 2;
        $company->nickname = null;
        $company->save();


        // Sign in User
        auth()->login($user);

        Toastr::success("Signed Up");

        return redirect("/signup/company/$company->id");
    }

    /**
     * Store new registration - create company/user
     *
     */
    protected function store()
    {
        //
    }


}
