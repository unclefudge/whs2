<div class="portlet light" style="display: none;" id="edit_business">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">Business Details</span>
        </div>
        <div class="actions">
            @if (Auth::user()->allowed2('sig.company', $company) && !$company->approved_by)
                <a href="/company/{{ $company->id }}/approve" class="btn btn-circle green btn-outline btn-sm" id="but_approve">Approve</a>
            @endif
        </div>
    </div>
    <div class="portlet-body form">
        {!! Form::model($company, ['method' => 'POST', 'action' => ['Company\CompanyController@updateBusiness', $company->id]]) !!}
        {{-- Business Entity --}}
        <div class="row">
            <div class="form-group {!! fieldHasError('business_entity', $errors) !!}">
                {!! Form::label('business_entity', 'Business Entity:', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    {!! Form::select('business_entity',$companyEntityTypes::all(), $company->business_entity, ['class' => 'form-control bs-select', 'required']) !!}
                    {!! fieldErrorMessage('business_entity', $errors) !!}
                </div>
            </div>
        </div>
        {{-- Category --}}
        @if(Auth::user()->isCompany($company->reportsTo()->id))
            <hr class="field-hr">
            <div class="row">
                <div class="form-group {!! fieldHasError('category', $errors) !!}">
                    {!! Form::label('category', "Category:", ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-9">
                        {!! Form::select('category',$companyTypes::all(), $company->category, ['class' => 'form-control bs-select', 'required']) !!}
                        {!! fieldErrorMessage('category', $errors) !!}
                        <span class="help-block"> Only viewable by parent company</span>
                    </div>
                </div>
            </div>
        @endif
        <hr class="field-hr">
        {{-- ABN --}}
        <div class="row">
            <div class="form-group {!! fieldHasError('abn', $errors) !!}">
                {!! Form::label('abn', 'ABN:', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    {!! Form::text('abn', null, ['class' => 'form-control', 'required']) !!}
                    {!! fieldErrorMessage('abn', $errors) !!}
                </div>
            </div>
        </div>
        <hr class="field-hr">
        {{-- GST --}}
        <div class="row">
            <div class="form-group {!! fieldHasError('gst', $errors) !!}">
                {!! Form::label('gst', 'GST:', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    {!! Form::select('gst',['1' => 'Yes', '0' => 'No'], $company->gst, ['class' => 'form-control bs-select', 'required']) !!}
                    {!! fieldErrorMessage('gst', $errors) !!}
                </div>
            </div>
        </div>
        @if (Auth::user()->isCC())
            <hr class="field-hr">
            {{-- Payroll Tax --}}
            <div class="row">
                <div class="form-group {!! fieldHasError('payroll_tax', $errors) !!}">
                    {!! Form::label('payroll_tax', 'Payroll Tax:', ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-9">
                        {!! Form::select('payroll_tax',$payrollTaxTypes::all(), $company->payroll_tax, ['class' => 'form-control bs-select']) !!}
                        {!! fieldErrorMessage('payroll_tax', $errors) !!}
                        <span class="help-block"> Only viewable by parent company</span>
                    </div>
                </div>
            </div>
            <hr class="field-hr">
            <div class="row">
                <div class="form-group {!! fieldHasError('superannuation', $errors) !!}">
                    {!! Form::label('superannuation', 'Superannuation:', ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-9">
                        {!! Form::select('superannuation', ['' => 'Select option', 'Liable' => 'Liable', 'Non Liable' => 'Non Liable'], $company->superannuation, ['class' => 'form-control bs-select']) !!}
                        {!! fieldErrorMessage('superannuation', $errors) !!}
                        <span class="help-block"> Only viewable by parent company</span>
                    </div>
                </div>
            </div>
            <hr class="field-hr">
            <div class="row">
                <div class="form-group {!! fieldHasError('creditor_code', $errors) !!}">
                    {!! Form::label('creditor_code', 'Creditor Code:', ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-9">
                        {!! Form::text('creditor_code', null, ['class' => 'form-control', 'required']) !!}
                        {!! fieldErrorMessage('creditor_code', $errors) !!}
                        <span class="help-block"> Only viewable by parent company</span>
                    </div>
                </div>
            </div>
        @endif

        <br>
        <div class="form-actions right">
            <button class="btn default" onclick="cancelForm(event, 'business')">Cancel</button>
            <button type="submit" class="btn green"> Save</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>