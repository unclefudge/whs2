<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use Validator;

use DB;
use Session;
use App\Models\Site\Site;
use App\Models\Misc\Equipment\EquipmentLocation;
use App\Http\Requests;
use App\Http\Requests\Site\SiteRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Alert;

class SiteController extends Controller {

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sitelist()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasAnyPermissionType('site'))
            return view('errors/404');

        return view('site/list-basic');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site'))
            return view('errors/404');

        return view('site/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SiteRequest $request)
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site'))
            return view('errors/404');

        $site_request = $request->except('tabs', 'supervisors');
        //$site_request['client_id'];
        //dd($site_request);

        // Create Site
        $newSite = Site::create($site_request);

        if ($request->get('supervisors'))
            $newSite->supervisors()->sync($request->get('supervisors'));

        // Create new Equipment Location
        EquipmentLocation::create(['site_id' => $newSite->id, 'status' => 1]);

        $newSite->emailSite('new');

        Toastr::success("Created new site");

        return redirect('site');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $site = Site::where(compact('slug'))->firstorFail();

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.site', $site))
            return view('errors/404');

        $tabs = ['profile', 'info'];

        return view('site.show', compact('site', 'tabs'));
    }

    /**
     * Display the settings for the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function showSettings($slug, $tab = 'info')
    {
        $site = Site::where(compact('slug'))->firstOrFail();

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site', $site))
            return view('errors/404');

        $tabs = ['settings', $tab];

        return view('site.show', compact('site', 'tabs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(SiteRequest $request, $slug)
    {
        $site = Site::where(compact('slug'))->firstOrFail();
        $old_status = $site->status;

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site', $site))
            return view('errors/404');

        $site_request = $request->except('supervisors', 'tabs');

        // Site recently marked completed
        if ($site_request['status'] == 0 && $site->status != 0) {
            $site_request['completed'] = Carbon::now();
            $location = EquipmentLocation::where('site_id', $site->id)->first();
            if ($location) {
                $location->status = 0;
                $location->save();
            }
        }
        // Site recently reactivated
        if ($site_request['status'] != 0 && $site->status == 0) {
            $location = EquipmentLocation::where('site_id', $site->id)->first();
            if ($location) {
                $location->status = 1;
                $location->save();
            } else
                $location = EquipmentLocation::create(['site_id' => $site->id, 'status' => 1]);
        }
        if ($site_request['status'] != 0)
            $site_request['completed'] = '0000-00-00 00:00:00';

        //dd($site_request);
        $site->update($site_request);

        // Update supervisors for site
        if ($request->get('supervisors'))
            $site->supervisors()->sync($request->get('supervisors'));
        else
            $site->supervisors()->detach();

        // Email Site if status change
        if ($site->status != $old_status)
            $site->emailSite();

        Toastr::success("Saved changes");
        $tabs = explode(':', $request->get('tabs'));

        return redirect('/site/' . $site->slug . '/' . $tabs[0] . '/' . $tabs[1]);
    }

    public function updateAdmin(Request $request, $slug)
    {
        $site = Site::where(compact('slug'))->firstOrFail();

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.admin', $site))
            return view('errors/404');

        $site_request = $request->except('slug');

        $site_request['contract_sent'] = ($request->filled('contract_sent')) ? Carbon::createFromFormat('d/m/Y H:i', $request->get('contract_sent') . '00:00')->toDateTimeString() : null;
        $site_request['contract_signed'] = ($request->filled('contract_signed')) ? Carbon::createFromFormat('d/m/Y H:i', $request->get('contract_signed') . '00:00')->toDateTimeString() : null;
        $site_request['deposit_paid'] = ($request->filled('deposit_paid')) ? Carbon::createFromFormat('d/m/Y H:i', $request->get('deposit_paid') . '00:00')->toDateTimeString() : null;
        $site_request['completion_signed'] = ($request->filled('completion_signed')) ? Carbon::createFromFormat('d/m/Y H:i', $request->get('completion_signed') . '00:00')->toDateTimeString() : null;

        // Clear Site Certificates if not set
        if (!$request->has('engineering')) $site_request['engineering'] = '0';
        if (!$request->has('construction')) $site_request['construction'] = '0';
        if (!$request->has('hbcf')) $site_request['hbcf'] = '0';

        $site->update($site_request);

        Toastr::success("Saved changes");
        $tabs = explode(':', $request->get('tabs'));

        return redirect('/site/' . $site->slug . '/' . $tabs[0] . '/' . $tabs[1]);
    }

    /**
     * Update the photo on user model resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateLogo(SiteRequest $request, $slug)
    {
        $site = Site::where(compact('slug'))->firstOrFail();

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site', $site))
            return view('errors/404');

        $file = $request->file('photo');
        $path = "filebank/site/" . $site->id;
        $name = "sitephoto." . strtolower($file->getClientOriginalExtension());
        $path_name = $path . '/' . $name;
        $file->move($path, $name);

        Image::make(url($path_name))
            ->fit(740)
            ->save($path_name);

        $site->photo = $path_name;
        $site->save();
        Toastr::success("Saved changes");

        return redirect('/site/' . $site->slug . '/settings/photo');
    }

    /**
     * Get Sites current user is authorised to manage + Process datatables ajax request.
     */
    public function getSites()
    {
        $site_records = Auth::user()->company->sites(request('status'));

        $dt = Datatables::of($site_records)
            ->editColumn('id', function ($site) {
                // Only return link to site if owner of site
                return (Auth::user()->isCompany($site->company_id)) ? '<div class="text-center"><a href="/site/' . $site->slug . '"><i class="fa fa-search"></i></a></div>' : '';
            })
            ->editColumn('client_phone', function ($site) {
                $string = '';
                if ($site->client_phone) {
                    $string = $site->client_phone;
                    if ($site->client_phone_desc)
                        $string = $site->client_phone . ' ' . $site->client_phone_desc;
                }
                if ($site->client_phone2) {
                    $string .= '<br>' . $site->client_phone2;
                    if ($site->client_phone2_desc)
                        $string .= ' ' . $site->client_phone2_desc;
                }

                return $string;
            })
            ->addColumn('supervisor', function ($site) {
                return $site->supervisorsSBC();
            })
            ->rawColumns(['id', 'client_phone'])
            ->make(true);

        return $dt;
    }

    /**
     * Get Sites current user is authorised to manage + Process datatables ajax request.
     */
    public function getSiteList()
    {
        $status = request('status');
        if (request('site_group'))
            $site_records = Auth::user()->authSites('view.site', $status)->where('company_id', request('site_group'));
        else {
            // If SiteGroup is All (ie 0) and Status (Upcoming or Inactive) then restrict sites to only user own company
            // Child company can't see inactive or upcoming sites for parent
            $site_records = (request('site_group') == '0' && $status != 1) ?
                Auth::user()->authSites('view.site', $status)->where('company_id', Auth::user()->company_id) :
                Auth::user()->authSites('view.site', $status);
        }

        $dt = Datatables::of($site_records)
            ->editColumn('client_phone', function ($site) {
                $string = '';
                if ($site->client_phone) {
                    $string = $site->client_phone;
                    if ($site->client_phone_desc)
                        $string = $site->client_phone . ' ' . $site->client_phone_desc;
                }
                if ($site->client_phone2) {
                    $string .= '<br>' . $site->client_phone2;
                    if ($site->client_phone2_desc)
                        $string .= ' ' . $site->client_phone2_desc;
                }

                return $string;
            })
            ->addColumn('supervisor', function ($site) {
                return $site->supervisorsSBC();
            })
            ->rawColumns(['id', 'client_phone'])
            ->make(true);

        return $dt;
    }


    /**
     * Get basic Site details.
     */
    public function getSiteDetails($id)
    {
        return Site::findOrFail($id);
    }

    /**
     * Get basic Site details.
     */
    /*
    public function getSiteOwner($id)
    {
        $site = Site::find($id);
        return $site->client->clientOfCompany;
    }*/


    /**
     * Process Site Check-in.
     *
     * @return \Illuminate\Http\Response
     */
    /*
    public function processCheckin(SiteCheckinRequest $request, $slug)
    {
        $site = Site::where(compact('slug'))->firstOrFail();

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
    }*/

}
