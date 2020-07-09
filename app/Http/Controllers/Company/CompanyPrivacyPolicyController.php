<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use Validator;

use DB;
use PDF;
use Mail;
use Session;
use App\User;
use App\Models\Company\Company;
use App\Models\Company\CompanyDoc;
use App\Models\Company\CompanyDocPrivacyPolicy;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;

/**
 * Class CompanyPrivacyPolicyController
 * @package App\Http\Controllers
 */
class CompanyPrivacyPolicyController extends Controller {

    public function index()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasAnyPermissionType('site.export'))
            return view('errors/404');

        return view('site/export/list');
    }

    /**
     * Display the specified resource.
     */
    public function show($cid, $id)
    {
        //dd('here');
        $company = Company::findOrFail($cid);
        $policy = CompanyDocPrivacyPolicy::find($id);

        if ($policy)
            return view('company/doc/privacy-show', compact('company', 'policy'));

        return view('errors/404');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($cid)
    {
        $company = Company::findOrFail($cid);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.company.doc'))
            return view('errors/404');

        return view('company/doc/privacy-create', compact('company'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store($cid)
    {
        $company = Company::findOrFail($cid);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.company.doc'))
            return view('errors/404');

        $policy_request = removeNullValues(request()->all());
        $policy_request = request()->all();

        // Set + create create directory if required
        $path = "filebank/company/$company->id/docs";
        if (!file_exists($path))
            mkdir($path, 0777, true);

        // Ensure filename is unique by adding counter to similiar filenames
        $filename = sanitizeFilename($company->name) . '-PRIVACY-' . Carbon::now()->format('d-m-Y') . '.pdf';
        $count = 1;
        while (file_exists(public_path("$path/$filename")))
            $filename = sanitizeFilename($company->name) . '-PRIVACY-' . Carbon::now()->format('d-m-Y') . '-' . $count ++ . '.pdf';


        $policy_request['date'] = Carbon::now();
        $policy_request['attachment'] = $filename;
        $policy_request['contractor_signed_id'] = Auth::user()->id;
        $policy_request['contractor_signed_at'] = Carbon::now();
        $policy_request['for_company_id'] = $company->id;
        $policy_request['company_id'] = $company->reportsTo()->id;
        $policy_request['status'] = 1;
        //dd($policy_request);

        // Create Privacy Policy
        $policy = CompanyDocPrivacyPolicy::create($policy_request);


        //
        // Generate PDF
        //
        //return view('pdf/company-privacy', compact('policy', 'company'));
        $pdf = PDF::loadView('pdf/company-privacy', compact('policy', 'company'));
        $pdf->setPaper('a4');
        $pdf->save(public_path("$path/$filename"));
        //return $pdf->stream();

        // Create Company Doc
        $doc = CompanyDoc::create([
            'category_id'    => 12,
            'name'           => 'Privacy Policy',
            'attachment'     => $filename,
            'ref_no'         => $policy->id,
            'status'         => 1,
            'for_company_id' => $policy->for_company_id,
            'company_id'     => $policy->company_id,
        ]);

        $policy->closeToDo();

        Toastr::success("Signed policy");

        return redirect("/company/$company->id/doc/privacy-policy/$policy->id");
    }

    /**
     * Update the specified resource.
     */
    public function update($cid, $id)
    {

    }

}
