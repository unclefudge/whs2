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
use App\Models\Company\CompanyDocCategory;
use App\Models\Company\CompanyLeave;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;

/**
 * Class CompanyPeriodTradeContractController
 * @package App\Http\Controllers
 */
class CompanyPeriodTradeContractController extends Controller {

    public function index()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasAnyPermissionType('site.export'))
            return view('errors/404');

        return view('site/export/list');
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
        //if (!Auth::user()->allowed2('add.company.doc'))
        //    return view('errors/404');

        return view('company/doc/ptc-create', compact('company'));
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        // Required even if empty
    }

    /**
     * Display the specified resource.
     */
    public function exportDocs()
    {
        $date = new Carbon('next Monday');
        $date = $date->format('d/m/Y');

        return view('company/export/docs', compact('date'));
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
