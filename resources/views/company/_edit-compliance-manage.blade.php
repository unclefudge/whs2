{{-- Edit  Compliance Manaement  --}}
<div class="portlet light" style="display: none;" id="edit_compliance">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">Compliance Management</span>
        </div>
        <div class="actions">
            @if (Auth::user()->allowed2('edit.compliance.manage', $company) && $company->status)
                <button class="btn btn-circle green btn-outline btn-sm" onclick="addForm('compliance')">Add</button>
            @endif
        </div>
    </div>
    <div class="portlet-body form">
        {{-- Current Overrides --}}
        @if ($company->complianceOverrides()->count())
            {!! Form::model('company', ['method' => 'POST', 'action' => ['Company\CompanyController@updateCompliance', $company->id]]) !!}
            @foreach ($company->complianceOverrides() as $over)
                {{-- Overtpe Type --}}
                <div class="row">
                    <div class="form-group {!! fieldHasError("compliance_type-$over->id", $errors) !!}">
                        {!! Form::label("compliance_type-$over->id", 'Override Type:', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-9">
                            {!! Form::text("compliance_text-$over->id", $overrideTypes::name($over->type), ['class' => 'form-control', 'required', 'disabled']) !!}
                            {!! Form::hidden("compliance_type-$over->id", $over->id) !!}
                            {!! fieldErrorMessage("compliance_type-$over->id", $errors) !!}
                        </div>
                    </div>
                </div><br>

                {{-- Required --}}
                @if ($over->type != 'cdu')
                    <div class="row">
                        <div class="form-group {!! fieldHasError("required-$over->id", $errors) !!}">
                            {!! Form::label("required-$over->id", 'Required:', ['class' => 'col-md-3 control-label']) !!}
                            <div class="col-md-9">
                                {!! Form::select("required-$over->id",['0' => 'No', '1' => 'Yes'], $over->required, ['class' => 'form-control bs-select', 'id' => "required-$over->id"]) !!}
                                {!! fieldErrorMessage("required-$over->id", $errors) !!}
                                <?php $cat = substr($over->type, 2) ?>
                                <span class="help-block"> By default this document {!! ($company->requiresCompanyDoc($cat, 'system')) ? '<b>IS</b>' : 'is <b>NOT</b>' !!} <b>REQUIRED</b> for this company to be compliant</span>
                            </div>
                        </div>
                    </div><br>
                @endif

                {{-- Reason --}}
                <div class="row">
                    <div class="form-group {!! fieldHasError("reason-$over->id", $errors) !!}">
                        {!! Form::label("reason-$over->id", 'Reason:', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-9">
                            {!! Form::textarea("reason-$over->id", $over->reason, ['rows' => '2', 'class' => 'form-control', 'required']) !!}
                            {!! fieldErrorMessage("reason-$over->id", $errors) !!}
                        </div>
                    </div>
                </div><br>

                {{-- Expiry --}}
                <div class="row">
                    <div class="form-group {!! fieldHasError("expiry-$over->id", $errors) !!}">
                        {!! Form::label("expiry-$over->id", 'Expiry:', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-9">
                            <div class="input-group date date-picker">
                                {!! Form::text("expiry-$over->id", ($over->expiry) ? $over->expiry->format('d/m/Y') : null, ['class' => 'form-control form-control-inline', 'style' => 'background:#FFF', 'data-date-format' => "dd-mm-yyyy", 'placeholder' => 'Leave blank to never expire', 'readonly']) !!}
                                <span class="input-group-btn"><button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button></span>
                            </div>
                            {!! fieldErrorMessage("expiry-$over->id", $errors) !!}
                        </div>
                    </div>
                </div><br>

                {{-- Delete --}}
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="mt-checkbox-list">
                                <label class="mt-checkbox mt-checkbox-outline pull-right"> Mark to be Deleted
                                    <input type="checkbox" value="{{ $over->id }}" name="co_del[]"/>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                @if (!$loop->last)
                    <hr class="field-hr"> @endif
            @endforeach

            <div class="form-actions right">
                <button class="btn default" onclick="cancelForm(event, 'compliance')">Cancel</button>
                <button type="submit" class="btn green"> Save</button>
            </div>
            {!! Form::close() !!}
        @else
            <div class="row">
                <div class="col-md-12">Currenty no overrides are set. Use
                    <button class="btn btn-circle green btn-outline btn-sm" onclick="addForm('compliance')">Add</button>
                    button to create one.
                </div>
            </div>
        @endif

    </div>
</div>