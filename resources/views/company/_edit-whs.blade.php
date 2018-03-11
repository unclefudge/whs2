{{-- Edit WHS Details --}}
<div class="portlet light" style="display: none;" id="edit_whs">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">WHS Compliance</span>
        </div>
    </div>
    <div class="portlet-body form">
        {!! Form::model('company', ['method' => 'POST', 'action' => ['Company\CompanyController@updateWHS', $company->id], 'class' => 'form-horizontal', 'role' => 'form']) !!}
        <div class="row form">
            <div class="col-md-12">
                <div class="form-body">
                    {{--Licence Required --}}
                    <div class="form-group {!! fieldHasError('licence_required', $errors) !!}" id="ref_no_field">
                        {!! Form::label('licence_required', 'Requires a Contractor Licence:', ['class' => 'col-md-6 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::select('licence_required',['0' => 'No', '1' => 'Yes'],$company->licence_required, ['class' => 'form-control bs-select', 'id' => 'licence_required']) !!}
                            {!! fieldErrorMessage('licence_required', $errors) !!}
                        </div>
                        {!! Form::hidden('requiresContractorsLicence', $company->requiresContractorsLicence(), ['id' => 'requiresContractorsLicence']) !!}
                    </div>
                    <div style="display: none" id="overide_div">
                        <div class="note note-warning">
                            <p id="req_yes">Company <span style="text-decoration: underline">doesn't</span> require a licence but you have set to <b>REQUIRED</b></p>
                            <p id="req_no">Company requires a licence but you have set to <b>NOT REQUIRED</b></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions right">
            <button class="btn default" onclick="cancelForm(event, 'whs')">Cancel</button>
            <button type="submit" class="btn green"> Save</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>