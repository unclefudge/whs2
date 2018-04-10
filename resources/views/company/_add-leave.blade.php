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
        @if ($company->leave()->whereDate('to', '>', date('Y-m-d'))->first())
            <div class="mt-comments">
                @foreach($company->leave()->whereDate('to', '>', date('Y-m-d'))->get() as $leave)
                    <hr style="margin: 5px 0px 0px 0px">
                    <div class="mt-comment" style="padding: 5px" id="show_leave-{{ $leave->id }}">
                        <div class="mt-comment-body" style="padding-left: 0px">
                            <div class="mt-comment-info">
                                <div class="row">
                                    <div class="col-md-3">{{ $leave->from->format('M j') }}
                                        - {!! ($leave->from->format('M') == $leave->to->format('M')) ? $leave->to->format('j') : $leave->to->format('M j') !!}</div>
                                    <div class="col-md-9">{{ $leave->notes }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="form-actions right">
            <button class="btn default" onclick="cancelForm(event, 'leave')">Cancel</button>
            <button type="submit" class="btn green"> Save</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>