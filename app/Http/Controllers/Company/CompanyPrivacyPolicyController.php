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
            'category_id'    => 5,
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
        $company = Company::findOrFail($cid);
        $ptc = CompanyDocPeriodTrade::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.company.doc'))
            return view('errors/404');

        $ptc_request = request()->all();
        // Archive old contract if required
        if (request('archive')) {
            $ptc_request['principle_signed_name'] = request('principle_signed_name2');
            $old_ptc = CompanyDoc::findOrFail(request('archive'));
            $old_ptc->status = 0;
            $old_ptc->save();
        }
        $ptc_request['principle_signed_id'] = Auth::user()->id;
        $ptc_request['principle_signed_at'] = Carbon::now();
        $ptc_request['status'] = 1;

        // Set + create create directory if required
        $path = "filebank/company/$company->id/docs";
        if (!file_exists($path))
            mkdir($path, 0777, true);

        // Ensure filename is unique by adding counter to similiar filenames
        $filename = sanitizeFilename($company->name) . '-PTC-' . $ptc->date->format('d-m-Y') . '.pdf';
        $count = 1;
        while (file_exists(public_path("$path/$filename")))
            $filename = sanitizeFilename($company->name) . '-PTC-' . $ptc->date->format('d-m-Y') . '-' . $count ++ . '.pdf';

        $ptc_request['attachment'] = $filename;

        //dd($ptc_request);
        $ptc->update($ptc_request);
        $ptc->closeToDo();

        //
        // Generate PDF
        //
        //return view('pdf/company-tradecontract', compact('ptc', 'company'));
        $pdf = PDF::loadView('pdf/company-tradecontract', compact('ptc', 'company'));
        $pdf->setPaper('a4');
        $pdf->save(public_path("$path/$filename"));
        //return $pdf->stream();

        // Update Company Doc
        $doc = CompanyDoc::where('category_id', 5)->where('ref_no', $ptc->id)->where('company_id', $ptc->company_id)->where('for_company_id', $ptc->for_company_id)->first();
        if ($doc) {
            $doc->attachment = $filename;
            $doc->status = 1;
            $doc->approved_by = $ptc->principle_signed_id;
            $doc->approved_at = $ptc->principle_signed_at;
            $doc->save();
        }


        Toastr::success("Signed contract");

        return redirect("/company/$company->id/doc/period-trade-contract/$ptc->id");
    }


    /**
     * Create PDF
     */
    public function tradecontractPDF($cid)
    {
        $company = Company::findOrFail($cid);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.company', $company))
            return view('errors/404');

        //$data[] = ['company_name' => 'cname', 'status' => 'status',];
        //return view('pdf/company-tradecontract', compact('data', 'company'));
        //$pdf = PDF::loadView('pdf/company-tradecontract', compact('data', 'company'));
        $pdf = PDF::loadView('pdf/company-tradecontract', compact('company'));
        $pdf->setPaper('a4');
        //$pdf->setOption('page-width', 200)->setOption('page-height', 287);
        //$pdf->setOption('margin-bottom', 10);
        //->setOption('footer-font-size', '7')
        //->setOption('footer-left', utf8_decode('Document created ' . date('\ d/m/Y\ ')))
        //->setOption('footer-center', utf8_decode('Page [page] / [topage]'))
        //->setOption('footer-right', utf8_decode("Initials:     "))
        //->setOrientation('portrait');

        //if ($request->has('view_pdf'))
        return $pdf->stream();
    }


}
