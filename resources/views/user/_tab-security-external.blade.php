<?php
$class = ['class' => 'form-control bs-select'];
$classDisable = ['class' => 'form-control bs-select', 'disabled'];
$sub1 = ($user->company->reportsToCompany()->subscription > 0) ? 1 : 0;
$sub2 = ($user->company->reportsToCompany()->subscription > 1) ? 1 : 0;
$plan = ($user->company->reportsToCompany()->addon('planner')) ? 1 : 0;
$cc = ($user->company->reportsToCompany()->id == '3') ? 1 : 0;
?>
@if (App\Models\Misc\Role2::where('company_id', $user->company->reportsToCompany()->id)->first())
    <div class="row">
        <div class="col-md-12">
            @if(Auth::user()->security && Auth::user()->company_id == $user->company->reportsToCompany()->id )
                <div class="form-group {!! fieldHasError('roles', $errors) !!}">
                    {!! Form::label('roles', 'Assigned Role(s)', ['class' => 'control-label']) !!}
                    {!! Form::select('roles', $user->company->reportsToCompany()->rolesSelect(), $user->roles2->pluck('id')->toArray(),
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
@endif

<table class="table table-striped">
    <tr>
        <td style="background: #FFF; border: 0px #e7ecf1; font-size: 18px; font-weight: 300; padding: 0;">Permissions</td>
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
<h5 class="font-green-haze" style="font-size: 16px">Accounts
    <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
       data-content="Grants ability to view or modify users, companies, clients that belong to the users own company or any 'child' company."
       data-original-title="Accounts"> <i class="fa fa-question-circle font-grey-silver"></i> </a></h5>
<table class="table table-bordered table-striped">
    <tr>
        <td>User</td>
        <td width="15%">{!! permSelect('view.user', ($sub2) ? 'our' : 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
        <td width="15%">{!! permSelect('edit.user', ($sub2) ? 'our' : 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
        <td width="15%">{!! permSelect('add.user', 'add', $user, $user->company->reportsToCompany()->id) !!}</td>
        <td width="15%">{!! permSelect('del.user', 'arc', $user, $user->company->reportsToCompany()->id) !!}</td>
        <td width="15%">{!! permSelect('sig.user', 'sig', $user, $user->company->reportsToCompany()->id) !!}</td>
    </tr>
    <tr>
        <td>Company</td>
        <td width="15%">{!! permSelect('view.company', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
        <td width="15%">{!! permSelect('edit.company', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
        @if ($sub2)
            <td width="15%">{!! permSelect('add.company', 'add', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('del.company', 'arc', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('sig.company', 'sig', $user, $user->company->reportsToCompany()->id) !!}</td>
        @else
            <td width="45%" colspan="3"></td>
        @endif
    </tr>
    @if($cc)
        <tr>
            <td>Company Acounting</td>
            <td width="15%">{!! permSelect('view.company.accounting', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('edit.company.accounting', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="45%" colspan="3"></td>
        </tr>
    @endif
</table>
@if ($sub1)
    <h5 class="font-green-haze" style="font-size: 16px">Work Site
        <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
           data-content="Grants ability to view or modify work sites and relevant trades/tasks/superviors required on the sites."
           data-original-title="Work Sites"> <i class="fa fa-question-circle font-grey-silver"></i> </a></h5>
    <table class="table table-bordered table-striped">
        <tr>
            <td>Site</td>
            <td width="15%">{!! permSelect('view.site', ($plan) ? 'super.plan' : 'super', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('edit.site', ($plan) ? 'super.plan' : 'super', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('add.site', 'add', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('del.site', 'arc', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%"></td>
        </tr>
        @if ($plan)
            <tr>
                <td>Trades / Tasks</td>
                <td width="15%">{!! permSelect('view.trade', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('edit.trade', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('add.trade', 'add', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('del.trade', 'arc', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%"></td>
            </tr>
        @endif
        <tr>
            <td>Area Supervisors</td>
            <td width="15%">{!! permSelect('view.area.super', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('edit.area.super', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="45%" colspan="3"></td>
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
                <td width="15%">{!! permSelect('view.weekly.planner', 'super.plan', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('edit.weekly.planner', 'super.plan', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="45%" colspan="3"></td>
            </tr>
            <tr>
                <td>Site</td>
                <td width="15%">{!! permSelect('view.site.planner', 'super.plan', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('edit.site.planner', 'super.plan', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="45%" colspan="3"></td>
            </tr>
            <tr>
                <td>Trade</td>
                <td width="15%">{!! permSelect('view.trade.planner', 'super.plan', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('edit.trade.planner', 'super.plan', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="45%" colspan="3"></td>
            </tr>
        </table>
    @endif

    <h5 class="font-green-haze" style="font-size: 16px">Attendance / Compliance
        <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
           data-content="Grants ability to view or edit attendance for users which belong to your company work sites."
           data-original-title="Attendance / Compliance"> <i
                    class="fa fa-question-circle font-grey-silver"></i>
        </a>
    </h5>
    <table class="table table-bordered table-striped">
        <tr>
            <td>Attendance</td>
            <td width="15%">{!! permSelect('view.attendance', ($sub2) ? 'super.company' : 'super.individual', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('edit.attendance', ($sub2) ? 'super.company' : 'super.individual', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="45%" colspan="3"></td>
        </tr>
        @if($cc)
            @if ($plan)
                <tr>
                    <td>Compliance</td>
                    <td width="15%">{!! permSelect('view.compliance', ($sub2) ? 'super.company' : 'super', $user, $user->company->reportsToCompany()->id) !!}</td>
                    <td width="15%">{!! permSelect('edit.compliance', ($sub2) ? 'super.company' : 'super', $user, $user->company->reportsToCompany()->id) !!}</td>
                    <td width="45%" colspan="3"></td>
                </tr>
            @endif
        @endif
    </table>

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
                <td width="15%">{!! permSelect('view.site.accident', ($sub2) ? 'every' : 'super.individual', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('edit.site.accident', ($sub2) ? 'every' : 'super.individual', $user, $user->company->reportsToCompany()->id) !!}</td>
            @else
                <td width="15%">{!! permSelect('view.site.accident', ($sub2) ? 'every-plan' : 'super.individual', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('edit.site.accident', ($sub2) ? 'every-plan' : 'super.individual', $user, $user->company->reportsToCompany()->id) !!}</td>
            @endif
            <td width="15%">{!! permSelect('add.site.accident', 'add', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('del.site.accident', 'res', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%"></td>
        </tr>
        <tr>
            <td>Hazard Reports</td>
            @if ($plan)
                <td width="15%">{!! permSelect('view.site.hazard', ($sub2) ? 'every' : 'super.individual', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('edit.site.hazard', ($sub2) ? 'every' : 'super.individual', $user, $user->company->reportsToCompany()->id) !!}</td>
            @else
                <td width="15%">{!! permSelect('view.site.hazard', ($sub2) ? 'every-plan' : 'super.individual', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('edit.site.hazard', ($sub2) ? 'every-plan' : 'super.individual', $user, $user->company->reportsToCompany()->id) !!}</td>
            @endif
            <td width="15%">{!! permSelect('add.site.hazard', 'add', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('del.site.hazard', 'res', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%"></td>
        </tr>
        <tr>
            <td>Safety (Risk / Hazardous Materials)</td>
            <td width="15%">{!! permSelect('view.safety.doc', ($plan) ? 'super.plan' : 'super', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('edit.safety.doc', ($plan) ? 'super.plan' : 'super', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('add.safety.doc', 'add', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('del.safety.doc', 'del', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%"></td>
        </tr>
        <tr>
            <td>General / Plans</td>
            <td width="15%">{!! permSelect('view.site.doc', ($plan) ? 'super.plan' : 'super', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('edit.site.doc', ($plan) ? 'super.plan' : 'super', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('add.site.doc', 'add', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('del.site.doc', 'del', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%"></td>
        </tr>
        @if($cc)
            <tr>
                <td>Quality Assurance Reports</td>
                <td width="15%">{!! permSelect('view.site.qa', 'super', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('edit.site.qa', 'super', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('add.site.qa', 'add', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('del.site.qa', 'res', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('sig.site.qa', 'sig', $user, $user->company->reportsToCompany()->id) !!}</td>
            </tr>
            <tr>
                <td>Asbestos Notifications</td>
                <td width="15%">{!! permSelect('view.site.asbestos', 'super', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('edit.site.asbestos', 'super', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('add.site.asbestos', 'add', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('del.site.asbestos', 'res', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%"></td>
            </tr>
        @endif
    </table>


    <h5 class="font-green-haze" style="font-size: 16px">General Documents
        <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
           data-content="Grants ability to view or modify documents which belong to your company."
           data-original-title="General Documents"> <i class="fa fa-question-circle font-grey-silver"></i>
        </a>
    </h5>
    <table class="table table-bordered table-striped">
        <tr>
            <td>Safe Work Method Statements</td>
            <td width="15%">{!! permSelect('view.wms', ($sub2) ? 'own' : 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('edit.wms', ($sub2) ? 'own' : 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('add.wms', 'add', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('del.wms', 'arc', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('sig.wms', 'sig', $user, $user->company->reportsToCompany()->id) !!}</td>
        </tr>
        <tr>
            <td>Toolbox Talks</td>
            <td width="15%">{!! permSelect('view.toolbox', ($sub2) ? 'own' : 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('edit.toolbox', ($sub2) ? 'own' : 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('add.toolbox', 'add', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('del.toolbox', 'res', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('sig.toolbox', 'sig', $user, $user->company->reportsToCompany()->id) !!}</td>
        </tr>
        <tr>
            <td>Safety Data Sheets (SDS)</td>
            <td width="15%">{!! permSelect('view.sds', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
            @if (false)
                <td width="15%">{!! permSelect('edit.sds', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('add.sds', 'add', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('del.sds', 'del', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%"></td>
            @else
                <td width="60%" colspan="4"></td>
            @endif
        </tr>
        <tr>
            <td>Company Documents</td>
            <td width="15%">{!! permSelect('view.company.doc', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('edit.company.doc', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('add.company.doc', 'add', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('del.company.doc', 'del', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%"></td>
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
                    <td width="15%">{!! permSelect('view.manage.report', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
                    <td width="60%" colspan="4"></td>
                </tr>
            @endif
            <tr>
                <td>Export Site Data</td>
                <td width="15%">{!! permSelect('view.site.export', 'super', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="60%" colspan="4"></td>
            </tr>
        </table>
    @endif

    @if ($cc)
        <h5 class="font-green-haze" style="font-size: 16px">Messages / Alerts
            <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
               data-content="Grants ability to communicate via messages / alert to other users which belong to your company or child company."
               data-original-title="Messages / Alerts"> <i class="fa fa-question-circle font-grey-silver"></i>
            </a>
        </h5>

        <table class="table table-bordered table-striped">
            <tr>
                <td>Alert Nofications</td>
                <td width="15%">{!! permSelect('view.notify', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('edit.notify', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('add.notify', 'add', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('del.notify', 'del', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%"></td>
            </tr>
            <tr>
                <td>Safety Tips</td>
                <td width="15%">{!! permSelect('view.safetytip', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('edit.safetytip', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('add.safetytip', 'add', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('del.safetytip', 'del', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%"></td>
            </tr>
            {{--
            <tr>
                <td>To Do Tasks</td>
                <td width="15%">{!! permSelect('view.todo', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('edit.todo', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%">{!! permSelect('add.todo', 'add', $user, $user->company->reportsToCompany()->id) !!}</td>
                <td width="15%"></td>
                <td width="15%"></td>
            </tr>
            --}}
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
            <td width="15%">{!! permSelect('view.settings', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('edit.settings', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="45%" colspan="3"></td>
        </tr>
        {{--
        <tr>
            <td>Roles / Permissions</td>
            <td width="15%">{!! permSelect('view.role', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('edit.role', 'all', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('add.role', 'add', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%">{!! permSelect('dell.role', 'del', $user, $user->company->reportsToCompany()->id) !!}</td>
            <td width="15%"></td>
        </tr>
        --}}
    </table>
@endif

@if (Auth::user()->security)
    <div class="margin-top-10">
        <button type="submit" class="btn green"> Save Changes</button>
        <a href="/user/{{ $user->username }}/settings/security">
            <button type="button" class="btn default"> Cancel</button>
        </a>
    </div>
@endif