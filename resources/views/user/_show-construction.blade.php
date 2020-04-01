{{-- Show Contruction Details --}}
<div class="portlet light" id="show_construction">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">Construction</span>
        </div>
        <div class="actions">
            @if (Auth::user()->allowed2('edit.user.construction', $user) && $user->status)
                <button class="btn btn-circle green btn-outline btn-sm" onclick="editForm('construction')">Edit</button>
            @endif
        </div>
    </div>
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-3">Attends Sites:</div>
            <div class="col-xs-9">{!! ($user->onsite) ? 'Yes' : 'No' !!} &nbsp;</div>
        </div>
        <hr class="field-hr">
        @if ($user->onsite)
            <div class="row">
                <div class="col-md-3">Trades:</div>
                <div class="col-xs-9">{!! ($user->tradesSkilledInSBC()) ? $user->tradesSkilledInSBC() : '-' !!} &nbsp;</div>
            </div>
            <hr class="field-hr">
            <div class="row">
                <div class="col-md-3">Apprentice:</div>
                <div class="col-xs-9">{!! ($user->apprentice) ? 'Yes' : 'No' !!} &nbsp;</div>
            </div>
            <hr class="field-hr">
            @if ($user->apprentice)
                <div class="row">
                    <div class="col-md-3">Apprenticeship Start Date:</div>
                    <div class="col-xs-9">{!! $user->apprentice_start->format('d/m/Y') !!} &nbsp;</div>
                </div>
                <hr class="field-hr">
            @endif
        @endif
    </div>
</div>