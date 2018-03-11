<div class="portlet light" id="show_whs">
    <div class="portlet-title tabbable-line">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">WHS Compliance</span>
        </div>
        <div class="actions">
            @if (Auth::user()->allowed2('edit.company.whs', $company))
                <button class="btn btn-circle green btn-outline btn-sm" onclick="editForm('whs')">Edit</button>
            @endif
        </div>
    </div>
    <div class="portlet-body">
        {{-- Licence equired --}}
        <div class="row">
            <div class="col-md-12">
                This company {!! ($company->licence_required) ? 'requires' : 'does not require' !!} a Contractor Licence. &nbsp;
                {!! ($company->licence_required != $company->requiresContractorsLicence()) ? ' &nbsp; <span class="font-red">OVERRIDDEN</span>' : '' !!}
            </div>
        </div>
        <hr class="field-hr">
    </div>
</div>
