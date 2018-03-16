{{-- Show Company Details --}}
<div class="portlet light" id="show_construction">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">Construction</span>
        </div>
        <div class="actions">
            @if (Auth::user()->allowed2('edit.company.con', $company) && $company->status)
                <button class="btn btn-circle green btn-outline btn-sm" onclick="editForm('construction')">Edit</button>
            @endif
        </div>
    </div>
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-3">Trades:</div>
            <div class="col-xs-9">{{ $company->tradesSkilledInSBC() }} &nbsp;</div>
        </div>
        <hr class="field-hr">
        @if(Auth::user()->isCC())
            <div class="row">
                <div class="col-md-3">Planner Name:</div>
                <div class="col-md-9">@if($company->nickname) {{ $company->nickname }} @else - @endif</div>
            </div>
            <hr class="field-hr">
            <div class="row">
                <div class="col-md-3">Max Jobs:</div>
                <div class="col-md-9">{{ $company->maxjobs }}</div>
            </div>
            <hr class="field-hr">
            <div class="row">
                <div class="col-md-3">Transient:</div>
                <div class="col-xs-9">@if($company->transient) Supervised by {{ $company->supervisedBySBC() }} @else No @endif</div>
            </div>
            <hr class="field-hr">
        @endif
    </div>
</div>