<?php
$class = ['class' => 'form-control bs-select'];
$classDisable = ['class' => 'form-control bs-select', 'disabled'];
$sub1 = ($user->company->reportsTo()->subscription > 0) ? 1 : 0;
$sub2 = ($user->company->reportsTo()->subscription > 1) ? 1 : 0;
$plan = ($user->company->reportsTo()->addon('planner')) ? 1 : 0;
$cc = ($user->company->reportsTo()->id == '3') ? 1 : 0;
$be = ($user->company->reportsTo()->id == '210') ? 1 : 0; // Blue Eco
$cid = $user->company->reportsTo()->id;
$dis = false;
$rec = $user;
?>

@if (App\Models\Misc\Role2::where('company_id', $cid)->first())
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <span class="caption-subject font-dark bold uppercase">Assigned Roles</span>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="row">
                <div class="col-md-12">
                    @if(Auth::user()->hasPermission2('edit.user.security') && Auth::user()->isCompany($cid))
                        <div class="form-group {!! fieldHasError('roles', $errors) !!}">
                            {!! Form::select('roles', $user->company->reportsTo()->rolesSelect('ext'), $user->roles2->pluck('id')->toArray(),
                            ['class' => 'form-control select2-multiple', 'name' => 'roles[]', 'multiple']) !!}
                            {!! fieldErrorMessage('roles', $errors) !!}
                        </div>
                    @else
                        {!! Form::label('roles', 'Assigned Role(s)', ['class' => 'control-label']) !!}
                        {!! Form::text('roles_txt', $user->parentRolesSBC(), ['class' => 'form-control', 'disabled']) !!}
                        <br>
                    @endif
                </div>
            </div>
            @if (Auth::user()->isCompany($user->company->parent_company))
                <div class="form-actions right">
                    <button type="submit" class="btn green">Save</button>
                </div>
            @endif
        </div>
    </div>
@endif

<div class="row" style="margin:0px; background-color: #48525e; color: #FFFFFF;">
    <div class="col-md-12">
        <h3>SECURITY PERMISSIONS</h3>
    </div>
</div>
{{-- Extra Permissions --}}
@if (Auth::user()->isCompany($cid) && Auth::user()->company->subscription && $user->extraUserPermissionsText($cid))
    <div class="row">
        <div class="col-md-12">
            <div class="note note-warning">
                {!! $user->extraUserPermissionsText($cid) !!}
                <a href="/user/{{ $user->id }}/resetpermissions" class="btn dark">Remove additional permissions</a>
            </div>
        </div>
    </div>
@endif

{{-- Management --}}
<div class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">Management</span>
            <span class="caption-helper"> Users & Companies</span>
        </div>
    </div>
    <div class="portlet-body form">
        <div class="row">
            <div class="col-md-12">
                {{-- Users / Companies --}}
                <table class="table table-striped">
                    <tr>
                        <td style="background: #FFF; border: 0px #e7ecf1; font-size: 18px; font-weight: 300; padding: 0;"></td>
                        <td width="15%" style="border: 1px solid; border-color:#e7ecf1">View</td>
                        <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Edit</td>
                        <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Create</td>
                        <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Delete
                            <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                               data-content="For record integrity most data can't be deleted but users may be given access to archive / resolve it instead. For data that is actually deleted you will be asked to 'confirm'"
                               data-original-title="Delete"> <i class="fa fa-question-circle font-grey-silver"></i> </a></td>
                        <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Sign Off
                            <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                               data-content="Certain actions or updates to the record are required to be 'Signed Off' by an authorised user."
                               data-original-title="Sign Off"> <i class="fa fa-question-circle font-grey-silver"></i> </a></td>
                    </tr>
                </table>

                {{-- Companies --}}
                <h5 class="font-green-haze" style="font-size: 16px">Companies
                    <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                       data-content="Grants ability to view or modify your company information or any 'child' company."
                       data-original-title="Company"> <i class="fa fa-question-circle font-grey-silver"></i> </a></h5>
                <table class="table table-bordered table-striped">
                    @if ($sub1)
                        <tr>
                            <td>Construction</td>
                            <td width="15%">{!! permSelect('view.company.con', ($sub1) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                            <td width="15%">{!! permSelect('edit.company.con', ($sub1) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                            <td width="45%" colspan="3"></td>
                        </tr>
                        <tr>
                            <td>WHS Compliance</td>
                            <td width="15%">{!! permSelect('view.company.whs', ($sub1) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                            <td width="15%">{!! permSelect('edit.company.whs', ($sub1) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                            <td width="45%" colspan="3"></td>
                        </tr>
                        <tr>
                            <td>Company Leave</td>
                            <td width="15%">{!! permSelect('view.company.leave', ($sub1) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                            <td width="15%">{!! permSelect('edit.company.leave', ($sub1) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                            <td width="45%" colspan="3"></td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        @if (Auth::user()->isCompany($user->company->parent_company))
            <div class="form-actions right">
                <button type="submit" class="btn green">Save</button>
            </div>
        @endif
    </div>
</div>

{{-- Documents --}}
<div class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">Documents</span>
        </div>
    </div>
    <div class="portlet-body form">
        <div class="row">
            <div class="col-md-12">
                @if ($sub1)
                    <table class="table table-striped">
                        <tr>
                            <td style="background: #FFF; border: 0px #e7ecf1; font-size: 18px; font-weight: 300; padding: 0;"></td>
                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">View</td>
                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Edit</td>
                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Create</td>
                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Delete
                                <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                   data-content="For record integrity most data can't be deleted but users may be given access to archive / resolve it instead. For data that is actually deleted you will be asked to 'confirm'"
                                   data-original-title="Delete"> <i class="fa fa-question-circle font-grey-silver"></i> </a></td>
                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Sign Off
                                <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                   data-content="Certain actions or updates to the record are required to be 'Signed Off' by an authorised user."
                                   data-original-title="Sign Off"> <i class="fa fa-question-circle font-grey-silver"></i> </a></td>
                        </tr>
                    </table>
                    <h5 class="font-green-haze" style="font-size: 16px">Public Documents</h5>
                    <table class="table table-bordered table-striped">
                        @foreach ($companyDocTypes::all() as $doc_type => $doc_name)
                            <tr>
                                <td>{{ $doc_name }}
                                    <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                       data-content="{!! $companyDocTypes::docNames($doc_type, 0) !!}" data-original-title="Documents"> <i
                                                class="fa fa-question-circle font-grey-silver"></i> </a></td>
                                <td width="15%">{!! permSelect("view.docs.$doc_type.pub", ($sub1) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                <td width="15%">{!! permSelect("edit.docs.$doc_type.pub", ($sub1) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                <td width="15%">{!! permSelect("add.docs.$doc_type.pub", 'up', $rec, $cid, $dis) !!}</td>
                                <td width="15%">{!! permSelect("del.docs.$doc_type.pub", 'arc', $rec, $cid, $dis) !!}</td>
                                <td width="15%">{!! permSelect("sig.docs.$doc_type.pub", 'sig', $rec, $cid, $dis) !!}</td>
                            </tr>
                        @endforeach
                    </table>
                    <h5 class="font-green-haze" style="font-size: 16px">Private Documents</h5>
                    <table class="table table-bordered table-striped">
                        @foreach ($companyDocTypes::all() as $doc_type => $doc_name)
                            <tr>
                                <td>{{ $doc_name }}
                                    <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                       data-content="{!! $companyDocTypes::docNames('acc', 1) !!}" data-original-title="Documents"> <i
                                                class="fa fa-question-circle font-grey-silver"></i> </a></td>
                                <td width="15%">{!! permSelect("view.docs.$doc_type.pri", ($sub1) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                <td width="15%">{!! permSelect("edit.docs.$doc_type.pri", ($sub1) ? 'own' : 'all', $rec, $cid, $dis) !!}</td>
                                <td width="15%">{!! permSelect("add.docs.$doc_type.pri", 'up', $rec, $cid, $dis) !!}</td>
                                <td width="15%">{!! permSelect("del.docs.$doc_type.pri", 'arc', $rec, $cid, $dis) !!}</td>
                                <td width="15%">{!! permSelect("sig.docs.$doc_type.pri", 'sig', $rec, $cid, $dis) !!}</td>
                            </tr>
                        @endforeach
                    </table>
                @endif
            </div>
        </div>
        @if (Auth::user()->isCompany($user->company->parent_company))
            <div class="form-actions right">
                <button type="submit" class="btn green">Save</button>
            </div>
        @endif
    </div>
</div>

@if ($sub1)
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <span class="caption-subject font-dark bold uppercase">Legacy Permissions</span>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="row">
                <div class="col-md-12">
                    {{-- Legacy --}}
                    <table class="table table-striped">
                        <tr>
                            <td style="background: #FFF; border: 0px #e7ecf1; font-size: 18px; font-weight: 300; padding: 0;"></td>
                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">View</td>
                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Edit</td>
                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Create</td>
                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Delete
                                <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                   data-content="For record integrity most data can't be deleted but users may be given access to archive / resolve it instead. For data that is actually deleted you will be asked to 'confirm'"
                                   data-original-title="Delete"> <i class="fa fa-question-circle font-grey-silver"></i> </a></td>
                            <td width="15%" style="border: 1px solid; border-color:#e7ecf1">Sign Off
                                <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                   data-content="Certain actions or updates to the record are required to be 'Signed Off' by an authorised user."
                                   data-original-title="Sign Off"> <i class="fa fa-question-circle font-grey-silver"></i> </a></td>
                        </tr>
                    </table>


                    <h5 class="font-green-haze" style="font-size: 16px">Work Site
                        <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                           data-content="Grants ability to view or modify work sites and relevant trades/tasks/superviors required on the sites."
                           data-original-title="Work Sites"> <i class="fa fa-question-circle font-grey-silver"></i> </a></h5>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <td>Site</td>
                            <td width="15%">{!! permSelect('view.site', ($plan) ? 'super.plan' : 'super', $rec, $cid) !!}</td>
                            <td width="60%" colspan="4"></td>
                        </tr>
                        <tr>
                            <td>Attendance</td>
                            <td width="15%">{!! permSelect('view.site.attendance', ($sub1) ? 'company.individual' : 'all', $rec, $cid, $dis) !!}</td>
                            <td width="60%" colspan="4"></td>
                        </tr>
                    </table>

                    @if ($plan)
                        <h5 class="font-green-haze" style="font-size: 16px">Planners
                            <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                               data-content="Grants ability to view or modify planners for work sites that belong to your company."
                               data-original-title="Planners"> <i class="fa fa-question-circle font-grey-silver"></i>
                            </a>
                        </h5>
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td>Weekly</td>
                                <td width="15%">{!! permSelect('view.weekly.planner', 'super.plan', $rec, $cid) !!}</td>
                                <td width="15%">{!! permSelect('edit.weekly.planner', 'super.plan', $rec, $cid) !!}</td>
                                <td width="45%" colspan="3"></td>
                            </tr>
                            <tr>
                                <td>Site</td>
                                <td width="15%">{!! permSelect('view.site.planner', 'super.plan', $rec, $cid) !!}</td>
                                <td width="15%">{!! permSelect('edit.site.planner', 'super.plan', $rec, $cid) !!}</td>
                                <td width="45%" colspan="3"></td>
                            </tr>
                            <tr>
                                <td>Trade</td>
                                <td width="15%">{!! permSelect('view.trade.planner', 'super.plan', $rec, $cid) !!}</td>
                                <td width="15%">{!! permSelect('edit.trade.planner', 'super.plan', $rec, $cid) !!}</td>
                                <td width="45%" colspan="3"></td>
                            </tr>
                        </table>

                        <h5 class="font-green-haze" style="font-size: 16px">Roster / Compliance
                            <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                               data-content="Grants ability to view or edit roster for users which belong to your company work sites."
                               data-original-title="Attendance / Compliance"> <i
                                        class="fa fa-question-circle font-grey-silver"></i>
                            </a>
                        </h5>
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td>Roster</td>
                                <td width="15%">{!! permSelect('view.roster', ($sub1) ? 'super.company' : 'super.individual', $rec, $cid) !!}</td>
                                <td width="15%">{!! permSelect('edit.roster', ($sub1) ? 'super.company' : 'super.individual', $rec, $cid) !!}</td>
                                <td width="45%" colspan="3"></td>
                            </tr>
                            @if($cc)
                                <tr>
                                    <td>Compliance</td>
                                    <td width="15%">{!! permSelect('view.compliance', ($sub1) ? 'super.company' : 'super', $rec, $cid) !!}</td>
                                    <td width="15%">{!! permSelect('edit.compliance', ($sub1) ? 'super.company' : 'super', $rec, $cid) !!}</td>
                                    <td width="45%" colspan="3"></td>
                                </tr>
                            @endif
                        </table>
                    @endif

                    <h5 class="font-green-haze" style="font-size: 16px">Site Documents
                        <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                           data-content="Grants ability to view / modify documents which belong to your company."
                           data-original-title="Site Documents"> <i class="fa fa-question-circle font-grey-silver"></i>
                        </a>
                    </h5>

                    <table class="table table-bordered table-striped">
                        <tr>
                            <td>Accident Reports</td>
                            @if ($plan)
                                <td width="15%">{!! permSelect('view.site.accident', ($sub1) ? 'every' : 'super.individual', $rec, $cid) !!}</td>
                                <td width="15%">{!! permSelect('edit.site.accident', ($sub1) ? 'every' : 'super.individual', $rec, $cid) !!}</td>
                            @else
                                <td width="15%">{!! permSelect('view.site.accident', ($sub1) ? 'every-plan' : 'super.individual', $rec, $cid) !!}</td>
                                <td width="15%">{!! permSelect('edit.site.accident', ($sub1) ? 'every-plan' : 'super.individual', $rec, $cid) !!}</td>
                            @endif
                            <td width="15%">{!! permSelect('add.site.accident', 'add', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('del.site.accident', 'res', $rec, $cid) !!}</td>
                            <td width="15%"></td>
                        </tr>
                        <tr>
                            <td>Hazard Reports</td>
                            @if ($plan)
                                <td width="15%">{!! permSelect('view.site.hazard', ($sub1) ? 'every' : 'super.individual', $rec, $cid) !!}</td>
                                <td width="15%">{!! permSelect('edit.site.hazard', ($sub1) ? 'every' : 'super.individual', $rec, $cid) !!}</td>
                            @else
                                <td width="15%">{!! permSelect('view.site.hazard', ($sub1) ? 'every-plan' : 'super.individual', $rec, $cid) !!}</td>
                                <td width="15%">{!! permSelect('edit.site.hazard', ($sub1) ? 'every-plan' : 'super.individual', $rec, $cid) !!}</td>
                            @endif
                            <td width="15%">{!! permSelect('add.site.hazard', 'add', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('del.site.hazard', 'res', $rec, $cid) !!}</td>
                            <td width="15%"></td>
                        </tr>
                        <tr>
                            <td>Safety (Risk / Hazardous Materials)</td>
                            <td width="15%">{!! permSelect('view.safety.doc', ($plan) ? 'super.plan' : 'super', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('edit.safety.doc', ($plan) ? 'super.plan' : 'super', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('add.safety.doc', 'add', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('del.safety.doc', 'del', $rec, $cid) !!}</td>
                            <td width="15%"></td>
                        </tr>
                        <tr>
                            <td>General / Plans</td>
                            <td width="15%">{!! permSelect('view.site.doc', ($plan) ? 'super.plan' : 'super', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('edit.site.doc', ($plan) ? 'super.plan' : 'super', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('add.site.doc', 'add', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('del.site.doc', 'del', $rec, $cid) !!}</td>
                            <td width="15%"></td>
                        </tr>
                        @if($cc)
                            <tr>
                                <td>Upcoming Plans<br><span class="font-grey-silver">Cape Cod Only</span></td>
                                <td width="15%">{!! permSelect('view.site.doc.upcoming', 'all', $rec, $cid) !!}</td>
                                <td width="60%" colspan="4"></td>
                            </tr>
                        @endif
                        <tr>
                            <td>Asbestos Notifications</td>
                            <td width="15%">{!! permSelect('view.site.asbestos', 'super', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('edit.site.asbestos', 'super', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('add.site.asbestos', 'add', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('del.site.asbestos', 'res', $rec, $cid) !!}</td>
                            <td width="15%"></td>
                        </tr>
                        @if($cc)
                            <tr>
                                <td>Quality Assurance Reports</td>
                                <td width="15%">{!! permSelect('view.site.qa', 'super', $rec, $cid) !!}</td>
                                <td width="15%">{!! permSelect('edit.site.qa', 'super', $rec, $cid) !!}</td>
                                <td width="15%">{!! permSelect('add.site.qa', 'add', $rec, $cid) !!}</td>
                                <td width="15%">{!! permSelect('del.site.qa', 'res', $rec, $cid) !!}</td>
                                <td width="15%">{!! permSelect('sig.site.qa', 'sig', $rec, $cid) !!}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>Inspection Reports</td>
                            <td width="15%">{!! permSelect('view.site.inspection', 'super.plan', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('edit.site.inspection', 'super', $rec, $cid) !!}</td>
                            <td width="45%" colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Maintenance Requests</td>
                            <td width="15%">{!! permSelect('view.site.maintenance', 'super.plan', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('edit.site.maintenance', 'super', $rec, $cid) !!}</td>
                            <td width="45%" colspan="3">&nbsp;</td>
                        </tr>
                    </table>

                    <h5 class="font-green-haze" style="font-size: 16px">Other Documents
                        <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                           data-content="Grants ability to view or modify documents which belong to your company."
                           data-original-title="General Documents"> <i class="fa fa-question-circle font-grey-silver"></i>
                        </a>
                    </h5>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <td>Safe Work Method Statements</td>
                            <td width="15%">{!! permSelect('view.wms', ($sub1) ? 'own' : 'all', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('edit.wms', ($sub1) ? 'own' : 'all', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('add.wms', 'add', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('del.wms', 'arc', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('sig.wms', 'sig', $rec, $cid) !!}</td>
                        </tr>
                        <tr>
                            <td>Toolbox Talks</td>
                            <td width="15%">{!! permSelect('view.toolbox', ($sub1) ? 'own' : 'all', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('edit.toolbox', ($sub1) ? 'own' : 'all', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('add.toolbox', 'add', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('del.toolbox', 'res', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('sig.toolbox', 'sig', $rec, $cid) !!}</td>
                        </tr>
                        <tr>
                            <td>Safety Data Sheets (SDS)</td>
                            <td width="15%">{!! permSelect('view.sds', 'all', $rec, $cid) !!}</td>
                            @if (false)
                                <td width="15%">{!! permSelect('edit.sds', 'all', $rec, $cid) !!}</td>
                                <td width="15%">{!! permSelect('add.sds', 'add', $rec, $cid) !!}</td>
                                <td width="15%">{!! permSelect('del.sds', 'del', $rec, $cid) !!}</td>
                                <td width="15%"></td>
                            @else
                                <td width="60%" colspan="4"></td>
                            @endif
                        </tr>
                    </table>

                    <h5 class="font-green-haze" style="font-size: 16px">Management Reports / Exports
                        <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                           data-content="Grants ability to view management reports and exports which belong to your company."
                           data-original-title="Management Reports / Exports"> <i class="fa fa-question-circle font-grey-silver"></i>
                        </a>
                    </h5>

                    @if ($sub1)
                        <table class="table table-bordered table-striped">
                            @if ($cc)
                                <tr>
                                    <td>Management Reports</td>
                                    <td width="15%">{!! permSelect('view.manage.report', 'all', $rec, $cid) !!}</td>
                                    <td width="60%" colspan="4"></td>
                                </tr>
                            @endif
                            <tr>
                                <td>Export Site Data</td>
                                <td width="15%">{!! permSelect('view.site.export', 'super', $rec, $cid) !!}</td>
                                <td width="60%" colspan="4"></td>
                            </tr>
                        </table>
                    @endif


                    <h5 class="font-green-haze" style="font-size: 16px">Messages / Alerts
                        <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                           data-content="Grants ability to communicate via messages / alert to other users which belong to your company or child company."
                           data-original-title="Messages / Alerts"> <i class="fa fa-question-circle font-grey-silver"></i>
                        </a>
                    </h5>

                    <table class="table table-bordered table-striped">
                        <tr>
                            <td>Alert Nofications</td>
                            <td width="15%">{!! permSelect('view.notify', 'all', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('edit.notify', 'all', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('add.notify', 'add', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('del.notify', 'del', $rec, $cid) !!}</td>
                            <td width="15%"></td>
                        </tr>
                        @if ($cc)
                            <tr>
                                <td>Safety Tips</td>
                                <td width="15%">{!! permSelect('view.safetytip', 'all', $rec, $cid) !!}</td>
                                <td width="15%">{!! permSelect('edit.safetytip', 'all', $rec, $cid) !!}</td>
                                <td width="15%">{!! permSelect('add.safetytip', 'add', $rec, $cid) !!}</td>
                                <td width="15%">{!! permSelect('del.safetytip', 'del', $rec, $cid) !!}</td>
                                <td width="15%"></td>
                            </tr>
                        @endif
                    </table>

                    @if ($cc)
                        <h5 class="font-green-haze" style="font-size: 16px">Miscellaneous
                            <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                               data-content="Grants ability to view / modify miscellaneous features for this website"
                               data-original-title="Miscellaneous"> <i class="fa fa-question-circle font-grey-silver"></i>
                            </a>
                        </h5>

                        <table class="table table-bordered table-striped">
                            <tr>
                                <td>Equipment Allocation</td>
                                <td width="15%">{!! permSelect('view.equipment', 'all', $rec, $cid, $dis) !!}</td>
                                <td width="15%">{!! permSelect('edit.equipment', 'all', $rec, $cid, $dis) !!}</td>
                                <td width="15%">{!! permSelect('add.equipment', 'add', $rec, $cid, $dis) !!}</td>
                                <td width="15%">{!! permSelect('del.equipment', 'del', $rec, $cid, $dis) !!}</td>
                                <td width="15%"></td>
                            </tr>
                            <tr>
                                <td>Equipment Stocktake</td>
                                <td width="15%">{!! permSelect('view.equipment.stocktake', 'all', $rec, $cid, $dis) !!}</td>
                                <td width="15%">{!! permSelect('edit.equipment.stocktake', 'all', $rec, $cid, $dis) !!}</td>
                                <td width="45%" colspan="3"></td>
                            </tr>
                        </table>
                    @endif

                    <h5 class="font-green-haze" style="font-size: 16px">Configuration / Settings
                        <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                           data-content="Grants ability to view / modify configuration settings for this website"
                           data-original-title="Configuration / Settings"> <i class="fa fa-question-circle font-grey-silver"></i>
                        </a>
                    </h5>

                    <table class="table table-bordered table-striped">
                        <tr>
                            <td>Settings</td>
                            <td width="15%">{!! permSelect('view.settings', 'all', $rec, $cid) !!}</td>
                            <td width="15%">{!! permSelect('edit.settings', 'all', $rec, $cid) !!}</td>
                            <td width="45%" colspan="3"></td>
                        </tr>
                    </table>
                </div>
            </div>
            @if (Auth::user()->isCompany($user->company->parent_company))
                <div class="form-actions right">
                    <button type="submit" class="btn green">Save</button>
                </div>
            @endif
        </div>
    </div>
@endif