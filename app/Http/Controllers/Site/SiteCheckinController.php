<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use Validator;

use DB;
use Session;
use App\Models\Site\Site;
use App\Models\Site\Planner\SiteRoster;
use App\Models\Site\Planner\SiteAttendance;
use App\Models\Site\SiteHazard;
use App\Models\Misc\Action;
use App\Http\Requests;
use App\Http\Requests\Site\SiteRequest;
use App\Http\Requests\Site\SiteCheckinRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Alert;

class SiteCheckinController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasAnyPermissionType('site'))
            return view('errors/404');

        return view('site/list');
    }


    /**
     * Check-in to Site.
     */
    public function checkin()
    {
        $worksite = (Session::has('siteID')) ? Site::findOrFail(Session::get('siteID')) : '';

        return view('site/checkinSelect', compact('worksite'));
    }

    /**
     * Process Site Check-in.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkinQuestions()
    {
        if (request('site_id')) {
            $worksite = Site::findOrFail(request('site_id'));
            Session::put('siteID', request('site_id'));

            return view('site/checkin', compact('worksite'));
        }

        return redirect('/site/checkin');

    }

    /**
     * Process Site Check-in.
     *
     * @return \Illuminate\Http\Response
     */

    public function processCheckin(SiteCheckinRequest $request, $site_id)
    {
        $site = Site::findOrFail($site_id);

        if ($request->has('safe_site'))
            $site->attendance()->save(new SiteAttendance(['safe_site' => '1']));
        else {
            if ($request->has('checkinTrade')) {
                $worksite = Site::find($site->id);

                return view('site/checkinTradeFail', compact(['worksite']));
            }

            $site->attendance()->save(new SiteAttendance(['safe_site' => '0']));

            // Create Hazard + attach to site
            if ($request->has('action_required'))
                $hazard = $site->hazards()->save(new SiteHazard($request->only('action_required', 'reason', 'location', 'rating')));
            else
                $hazard = $site->hazards()->save(new SiteHazard($request->only('reason', 'location', 'rating')));

            //Create action taken + attach to hazard
            if ($hazard) {
                $action = Action::create(['action' => $request->get('action'), 'table' => 'site_hazards', 'table_id' => $hazard->id]);
                $hazard->touch(); // update timestamp

                // Handle attached Photo or Video
                if ($request->hasFile('media'))
                    $hazard->saveAttachedMedia($request->file('media'));

                // Email hazard
                $hazard->emailHazard($action);
            }
        }

        // if Today add them to Roster if Company is on Planer but user not on Roster
        $today = Carbon::now()->format('Y-m-d');
        if ($site->isCompanyOnPlanner(Auth::user()->company_id, $today) && !$site->isUserOnRoster(Auth::user()->id, $today)) {
            $newRoster = SiteRoster::create(array(
                'site_id'    => $site->id,
                'user_id'    => Auth::user()->id,
                'date'       => $today . ' 00:00:00',
                'created_by' => '1',
                'updated_by' => '1',
            ));
        }

        Toastr::success("Checked in");

        //$worksite = $site;
        //dd($site);
        return redirect('/dashboard');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SiteRequest $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(SiteRequest $request, $slug)
    {
    }


}
