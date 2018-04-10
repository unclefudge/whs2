{{-- Show Company Leave --}}
<div class="portlet light" id="show_leave">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">Company Leave</span>
        </div>
        <div class="actions">
            @if (Auth::user()->allowed2('edit.company.leave', $company) && $company->status)
                <button class="btn btn-circle green btn-outline btn-sm" onclick="addForm('leave')">Add</button>
            @endif
        </div>
    </div>
    <div class="portlet-body">
        @if ($company->leave()->whereDate('to', '>', date('Y-m-d'))->first())
            <div class="mt-comments">
                @foreach($company->leave()->whereDate('to', '>', date('Y-m-d'))->get() as $leave)
                    <div class="mt-comment" style="padding: 5px" id="show_leave-{{ $leave->id }}">
                        <div class="mt-comment-body" style="padding-left: 0px">
                            <div class="mt-comment-info">
                                <div class="row">
                                    <div class="col-md-3">{{ $leave->from->format('M j') }}
                                        - {!! ($leave->from->format('M') == $leave->to->format('M')) ? $leave->to->format('j') : $leave->to->format('M j') !!}</div>
                                    <div class="col-md-9">{{ $leave->notes }}</div>
                                </div>
                            </div>
                            <div class="mt-comment-details">
                                <ul class="mt-comment-actions">
                                    <li>
                                        <button class="btn btn-xs dark delete_leave" data-id="{{ $leave->id }}"
                                                data-date="{{ $leave->from->format('M j') }} - {{ ($leave->from->format('M') == $leave->to->format('M')) ? $leave->to->format('j') : $leave->to->format('M j') }}"
                                                data-note="{{ $leave->notes }}">Delete</button>
                                    </li>
                                    <li>
                                        <button class="btn btn-xs btn-primary" onclick="editForm('leave')">Edit</button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <hr style="margin: 5px 0px 0px 0px">
                    </div>
                @endforeach
            </div>
        @else
            No scheduled leave
        @endif

    </div>
</div>