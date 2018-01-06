<?php

namespace App\Http\Controllers\Misc;


use PDF;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Safety\WmsDoc;
use App\Models\Site\Site;
use App\Models\Site\Planner\SitePlanner;
use App\User;
use App\Models\Site\Planner\Task;
use App\Models\Site\Planner\Trade;
use App\Models\Company\Company;
use App\Models\Company\CompanyLeave;
use Carbon\Carbon;

class PdfController extends Controller {

    public function test()
    {
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->loadHTML('<h1>Test</h1>');
        return $pdf->stream();
    }

    public function workmethod($id)
    {
        $doc = WmsDoc::findOrFail($id);
        //return view('pdf.workmethod', compact('doc'));
        $pdf = PDF::loadView('pdf.workmethod', compact('doc'));
        $file = public_path('filebank/company/' . $doc->for_company_id . '/wms/' . $doc->name . ' v' . $doc->version . ' ref-' . $doc->id . ' ' . '.pdf');
        if (file_exists($file))
            unlink($file);
        $pdf->save($file);
        return $pdf->stream();
        //return $pdf->download('invoice.pdf');
        //PDF::loadHTML($html)->setPaper('a4')->setOrientation('landscape')->setOption('margin-bottom', 0)->save('myfile.pdf')
    }

    public function plannerSite($site_id, $date, $weeks)
    {
        $site = Site::findOrFail($site_id);
        //return view('pdf.plan-site', compact('site'));


        $pdf = PDF::loadView('pdf.plan-site', compact('site', 'date', 'weeks'))->setOrientation('landscape')->setOption('margin-bottom', 0);
        //$file = public_path('filebank/company/' . $doc->for_company_id . '/wms/' . $doc->name . ' v' . $doc->version . ' ref-' . $doc->id . ' ' . '.pdf');
        //if (file_exists($file))
        //    unlink($file);
        //$pdf->save($file);
        return $pdf->stream();
        //return $pdf->download('invoice.pdf');
        //PDF::loadHTML($html)->setPaper('a4')->setOrientation('landscape')->setOption('margin-bottom', 0)->save('myfile.pdf')
    }


}
