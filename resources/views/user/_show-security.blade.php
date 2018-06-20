{{-- Show Security Details --}}
<div class="portlet light" id="show_security">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">Assigned Roles
                @if ($user->hasPermission2('edit.user.security') )<span class='label label-sm label-warning'>Security Access</span>@endif
            </span>
        </div>
        <div class="actions">
            @if (Auth::user()->allowed2('edit.user', $user))
                <a href="/user/{{ $user->id }}/security" class="btn btn-circle green btn-outline btn-sm">Edit</a>
            @endif
        </div>
    </div>
    <div class="portlet-body">
        @if ($user->rolesSBC() || ($user->company->parent_company && $user->parentRolesSBC()))
            @if ($user->rolesSBC())
                <div class="row">
                    <div class="col-md-6">{{ $user->company->name }}:</div>
                    <div class="col-md-6">{{ $user->rolesSBC() }}</div>
                </div>
            @endif
            @if ($user->company->parent_company && $user->parentRolesSBC())
                @if ($user->rolesSBC())
                    <hr class="field-hr">
                @endif
                <div class="row">
                    <div class="col-md-6">{{ $user->company->reportsTo()->name }}:</div>
                    <div class="col-md-6">{{ $user->parentRolesSBC() }}</div>
                </div>
            @endif
        @endif
        <hr class="field-hr">
    </div>
</div>