{{-- Show Company Leave --}}
<div class="portlet light" id="show_leave">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">Company Leave</span>
        </div>
        <div class="actions">
            @if (Auth::user()->allowed2('edit.company.leave', $company) && $company->status)
                <button class="btn btn-circle green btn-outline btn-sm" onclick="editForm('leave')">Edit</button>
            @endif
        </div>
    </div>
    <div class="portlet-body">
        @if ($company->leave()->whereDate('to', '>', date('Y-m-d'))->first())
            @foreach($company->leave()->whereDate('to', '>', date('Y-m-d'))->get() as $leave)
                <div class="row">
                    <div class="col-md-3">{{ $leave->from->format('M j') }}
                        - {!! ($leave->from->format('M') == $leave->to->format('M')) ? $leave->to->format('j') : $leave->to->format('M j') !!}</div>
                    <div class="col-md-9">{{ $leave->notes }}</div>
                </div>
                <hr class="field-hr">
            @endforeach
        @else
            No scheduled leave
        @endif

    </div>
</div>