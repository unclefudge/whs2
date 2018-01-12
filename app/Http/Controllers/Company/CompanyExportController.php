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
 * Class CompanyExportController
 * @package App\Http\Controllers
 */
class CompanyExportController extends Controller {

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
     * Create Company Docs PDF
     */
    public function docsPDF(Request $request)
    {
        $today = Carbon::now()->format('Y-m-d');

        //dd($request->all());
        $expiry_from = Carbon::createFromFormat('d/m/Y H:i:s', $request->get('from') . ' 00:00:00');
        $expiry_to = Carbon::createFromFormat('d/m/Y H:i:s', $request->get('to') . ' 00:00:00');

        echo "Expiry from $expiry_from - $expiry_to &nbsp; status: " . $request->get('status') . "<br><br>";

        $multiple_company = ($request->get('for_company_id')) ? false : true;
        $companies = ($multiple_company) ? Auth::user()->company->companies('1')->pluck('id')->toArray() : [$request->get('for_company_id')];
        $categories = ($request->get('category_id') == 'ALL') ? CompanyDocCategory::all()->sortBy('name')->pluck('id')->toArray() : [$request->get('category_id')];
        //dd($categories);
        $docs = DB::table('company_docs as d')->select(['d.id', 'd.name', 'd.expiry', 'd.attachment', 'd.approved_by', 'd.approved_at', 'd.status', 'c.name as cname', 'c.id AS cid'])
            ->whereDate('d.expiry', '>=', $expiry_from->format('Y-m-d'))->whereDate('d.expiry', '<=', $expiry_to->format('Y-m-d'))
            ->whereIn('d.for_company_id', $companies)
            ->whereIn('d.category_id', $categories)
            ->join('companys as c', 'd.for_company_id', '=', 'c.id')
            ->orderBy('c.name')->orderBy('d.name')->get();

        //dd($docs);

        $data = [];
        foreach ($docs as $doc) {
            echo "$doc->expiry - $doc->name ($doc->status)<br>";
            if (($request->get('status') == 1 && ($doc->status == 1 || ($doc->status == 0 && $doc->approved_by))) // Approved
                || ($request->get('status') == 2 && $doc->status == 2) // Pending
                || ($request->get('status') == 3 && $doc->status == 3) // Rejected
                || (!$request->get('status')) // All
            ) {
                $data[] = [
                    'company_name' => $doc->cname,
                    'doc_name'     => $doc->name,
                    'expiry'       => Carbon::createFromFormat('Y-m-d H:i:s', $doc->expiry)->format('d/m/Y'),
                    'attachment'   => $doc->attachment,
                    'approved_by'  => ($doc->approved_by) ? User::find($doc->approved_by)->full_name : '',
                    'approved_at'  => ($doc->approved_by) ? Carbon::createFromFormat('Y-m-d H:i:s', $doc->approved_at)->format('d/m/Y') : '',
                    'status'       => $doc->status,
                ];
            }
        }

        $header = strtoupper("Company Documents<br><small>Expiry " . $expiry_from->format('d/m/Y') . ' - ' . $expiry_to->format('d/m/Y')) . '</small>';
        if (!$multiple_company)
            $header = strtoupper(Company::find($request->get('for_company_id'))->name . " Documents<br><small>Expiry " . $expiry_from->format('d/m/Y') . ' - ' . $expiry_to->format('d/m/Y') . '</small>');

        //return view('pdf/company-docs', compact('data', 'header', 'multiple_company'));
        $pdf = PDF::loadView('pdf/company-docs', compact('data', 'header', 'multiple_company'));
        $pdf->setPaper('a4');
        //->setOption('page-width', 200)->setOption('page-height', 287)
        //->setOption('margin-bottom', 10)
        //->setOrientation('portrait');

        //if ($request->has('view_pdf'))
        return $pdf->stream();

        /*
        if ($request->has('email_pdf')) {
            if ($request->get('email_list')) {
                $email_list = explode(';', $request->get('email_list'));
                $email_list = array_map('trim', $email_list); // trim white spaces

                $data = [
                    'user_fullname'     => Auth::user()->fullname,
                    'user_company_name' => Auth::user()->company->name,
                    'startdata'         => $startdata
                ];
                Mail::send('emails/jobstart', $data, function ($m) use ($email_list, $data) {
                    $user_email = Auth::user()->email;
                    ($user_email) ? $send_from = $user_email : $send_from = 'do-not-reply@safeworksite.net';

                    $m->from($send_from, Auth::user()->fullname);
                    $m->to($email_list);
                    $m->subject('Upcoming Job Start Dates');
                });
                if (count(Mail::failures()) > 0) {
                    foreach (Mail::failures as $email_address)
                        Toastr::error("Failed to send to $email_address");
                } else
                    Toastr::success("Sent email");

                return view('planner/export/start');
            }
        }*/
    }

    /**
     * Create Company Docs PDF
     */
    public function tradecontractPDF(Request $request, $id, $version)
    {
        $company = Company::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.company', $company))
            return view('errors/404');

        $data[] = ['company_name' => 'cname', 'status' => 'status',];

        //return view('pdf/company-tradecontract', compact('data', 'company'));
        $pdf = PDF::loadView('pdf/company-tradecontract', compact('data', 'company'));
        $pdf->setPaper('a4');
        //->setOption('page-width', 200)->setOption('page-height', 287)
        //->setOption('margin-bottom', 10)
        //->setOption('footer-font-size', '7')
        //->setOption('footer-left', utf8_decode('Document created ' . date('\ d/m/Y\ ')))
        //->setOption('footer-center', utf8_decode('Page [page] / [topage]'))
        //->setOption('footer-right', utf8_decode("Initials:     "))
        //->setOrientation('portrait');

        //if ($request->has('view_pdf'))
        return $pdf->stream();
    }

    /**
     * Create Company Docs PDF
     */
    public function subcontractorstatementPDF(Request $request, $id, $version)
    {
        $company = Company::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.company', $company))
            return view('errors/404');

        $year = Carbon::now()->format('Y');
        if ($version == 'next')
            $year ++;
        $month = Carbon::now()->format('n');
        $last_year = $year - 1;
        $next_year = $year + 1;

        $date_from = ($month > 6) ? Carbon::parse("July 1 $year") : Carbon::parse("June 1 $last_year");
        $date_to = ($month > 6) ? Carbon::parse("June 30 $next_year") : Carbon::parse("June 1 $year");

        $data = ['date_from'             => $date_from, 'date_to' => $date_to,
                 'suburb_state_postcode' => $company->suburb_state_postcode,
                 'parent_name'           => $company->reportsTo()->name,
                 'parent_abn'            => $company->reportsTo()->abn,
        ];

        //dd($data);
        //return view('pdf/company-subcontractorstatement', compact('data', 'company'));
        $pdf = PDF::loadView('pdf/company-subcontractorstatement', compact('data', 'company'));
        //->setOption('page-width', 200)->setOption('page-height', 287)
        //->setOption('margin-bottom', 10)
        //->setOrientation('portrait');

        return $pdf->stream();
    }

}
