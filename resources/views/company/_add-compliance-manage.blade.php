{{-- Edit Compliance Manaement --}}
<div class="portlet light" style="display: none;" id="add_compliance">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">Compliance Management</span>
        </div>
    </div>
    <div class="portlet-body form">
        {!! Form::model('compliance_override', ['action' => ['Company\CompanyController@storeCompliance', $company->id], 'class' => 'horizontal-form']) !!}
        {{-- Hidden Required Doc Fields --}}
        @foreach ($overrideTypes::companySelect() as $type => $name)
            <?php $cat = substr($type, 2) ?>
            @if (is_numeric($cat))
                {!! Form::hidden("ot_$type", ($company->requiresCompanyDoc($cat, 'system')) ? 1 : 0, ['id' => "ot_$type"]) !!}
            @endif
        @endforeach

        {{-- Add New Override --}}
        <div class="row">
            <div class="form-group {!! fieldHasError("compliance_type", $errors) !!} {!! fieldHasError('duplicate_override', $errors) !!}">
                {!! Form::label('compliance_type', 'Override Type:', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    {!! Form::select('compliance_type',$overrideTypes::companySelect(), null, ['class' => 'form-control bs-select', 'id' => 'compliance_type']) !!}
                    {!! fieldErrorMessage("compliance_type", $errors) !!}
                    {!! fieldErrorMessage('duplicate_override', $errors) !!}
                </div>
            </div>
        </div>
        <div style="display: none" id="add_compliance_fields">
            {{-- Required --}}
            <div id="add_compliance_required">
                <hr class="field-hr">
                <div class="row">
                    <div class="form-group {!! fieldHasError("required", $errors) !!}">
                        {!! Form::label('required', 'Required:', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-9">
                            {!! Form::select('required',['0' => 'No', '1' => 'Yes'], null, ['class' => 'form-control bs-select', 'id' => 'required']) !!}
                            {!! fieldErrorMessage('required', $errors) !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <span class="help-block"> By default this document <span id="creq_yes"><b>IS</b></span><span id="creq_not">is <b>NOT</b></span> <b>REQUIRED</b> for this company to be compliant</span>
                    </div>
                </div>
            </div>

            {{-- Reason --}}
            <hr class="field-hr">
            <div class="row">
                <div class="form-group {!! fieldHasError("reason", $errors) !!}">
                    {!! Form::label("reason", 'Reason:', ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-9">
                        {!! Form::textarea('reason', null, ['rows' => '2', 'class' => 'form-control', 'required']) !!}
                        {!! fieldErrorMessage("reason", $errors) !!}
                    </div>
                </div>
            </div>

            {{-- Expiry --}}
            <hr class="field-hr">
            <div class="row">
                <div class="form-group {!! fieldHasError("expiry", $errors) !!}">
                    {!! Form::label('expiry', 'Expiry:', ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-9">
                        <div class="input-group date date-picker">
                            {!! Form::text("expiry", null, ['class' => 'form-control form-control-inline', 'style' => 'background:#FFF', 'data-date-format' => "dd-mm-yyyy", 'placeholder' => 'Leave blank to never expire', 'readonly']) !!}
                            <span class="input-group-btn"><button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button></span>
                        </div>
                        {!! fieldErrorMessage('expiry', $errors) !!}
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="form-actions right">
            <button class="btn default" onclick="cancelForm(event, 'compliance')">Cancel</button>
            <button type="submit" class="btn green" id="save_compliance" style="display: none"> Save</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>