{{-- Edit Company Leave --}}
<div class="portlet light" style="display: none;" id="add_leave">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">Company Leave</span>
        </div>
    </div>
    <div class="portlet-body form">
        {!! Form::model('company_leave', ['action' => ['Company\CompanyController@storeLeave', $company->id], 'class' => 'horizontal-form']) !!}

        {{-- Leave --}}
        <div class="row">
            <div class="form-group {!! fieldHasError("from", $errors) !!}">
                {!! Form::label("from", 'Leave From:', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    <div class="input-group date date-picker input-daterange" data-date-format="dd/mm/yyyy">
                        {!! Form::text("from", null, ['class' => 'form-control', 'readonly', 'style' => 'background:#FFF']) !!}
                        <span class="input-group-addon"> to </span>
                        {!! Form::text("to", null, ['class' => 'form-control', 'readonly', 'style' => 'background:#FFF']) !!}
                    </div>
                    {!! fieldErrorMessage("start_date", $errors) !!}
                </div>
            </div>
        </div>
        <hr class="field-hr">
        <div class="row">
            <div class="form-group {!! fieldHasError("notes", $errors) !!}">
                {!! Form::label("notes", 'Notes:', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    {!! Form::textarea('notes', null, ['rows' => '2', 'class' => 'form-control', 'required']) !!}
                    {!! fieldErrorMessage("notes", $errors) !!}
                </div>
            </div>
        </div>
        <br>
        <div class="form-actions right">
            <button class="btn default" onclick="cancelForm(event, 'leave')">Cancel</button>
            <button type="submit" class="btn green"> Save</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>