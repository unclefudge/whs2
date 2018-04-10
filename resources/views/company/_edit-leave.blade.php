{{-- Edit Company Leave --}}
<div class="portlet light" style="display: none;" id="edit_leave">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">Company Leave</span>
        </div>
    </div>
    <div class="portlet-body form">
        {!! Form::model('company', ['method' => 'POST', 'action' => ['Company\CompanyController@updateLeave', $company->id]]) !!}

        {{-- Leave --}}
        @if ($company->leave()->whereDate('to', '>', date('Y-m-d'))->first())
            @foreach($company->leave()->whereDate('to', '>', date('Y-m-d'))->get() as $leave)
                <div class="row">
                    <div class="form-group {!! fieldHasError("from-$leave->id", $errors) !!}">
                        {!! Form::label("from-$leave->id", 'Leave From:', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-9">
                            <div class="input-group date date-picker input-daterange" data-date-format="dd/mm/yyyy">
                                {!! Form::text("from-$leave->id", $leave->from->format('d/m/Y'), ['class' => 'form-control', 'readonly', ($leave->from->lt(Carbon\Carbon::now())) ? 'disabled' : '', 'style' => 'background:#FFF']) !!}
                                <span class="input-group-addon"> to </span>
                                {!! Form::text("to-$leave->id", $leave->to->format('d/m/Y'), ['class' => 'form-control', 'readonly', ($leave->from->lt(Carbon\Carbon::now())) ? 'disabled' : '', 'style' => 'background:#FFF']) !!}
                            </div>
                            {!! fieldErrorMessage("start_date-$leave->id", $errors) !!}
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="form-group {!! fieldHasError("notes-$leave->id", $errors) !!}">
                        {!! Form::label("notes-$leave->id", 'Notes:', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-9">
                            {!! Form::textarea("notes-$leave->id", $leave->notes, ['rows' => '2', 'class' => 'form-control', 'required']) !!}
                            {!! fieldErrorMessage("notes-$leave->id", $errors) !!}
                        </div>
                    </div>
                </div>
                @if(!$loop->last)
                    <hr class="field-hr">
                @endif
            @endforeach
        @else
            No scheduled leave
        @endif


        <br>
        <div class="form-actions right">
            <button class="btn default" onclick="cancelForm(event, 'leave')">Cancel</button>
            <button type="submit" class="btn green"> Save</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>