{{-- Show Company Leave --}}
<div class="portlet light" id="show_leave">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">Company Leave</span>
        </div>
        <div class="actions">
            @if (Auth::user()->allowed2('add.company.leave', $company) && $company->status)
                <button class="btn btn-circle green btn-outline btn-sm" onclick="editForm('leave')">Edit</button>
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
                                        <button class="btn btn-xs dark" id="del_doc" data-doc_id="{{ $leave->id }}">Delete</button>
                                    </li>
                                    <li>
                                        <button class="btn btn-xs btn-primary" onclick="editForm('leave-{{ $leave->id }}')">Edit</button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <hr style="margin: 5px 0px 0px 0px">
                    </div>

                    {{-- Edit Leave --}}
                    {{--}}
                    <div class="mt-comment" style="display: none" id="edit_doc{{ $cat_type }}">
                        {!! Form::model($doc, ['action' => ['Company\CompanyDocController@profileWHS'], 'files' => true, 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'file-form']) !!}
                        {!! Form::hidden('doc_id', ($doc) ? $doc->id : 'new', ['class' => 'form-control', 'id' => 'doc_id']) !!}
                        {!! Form::hidden('category_id', $cat_type, ['class' => 'form-control', 'id' => 'category_id']) !!}
                        {!! Form::hidden('name', $doc_name, ['class' => 'form-control', 'id' => 'doc_name']) !!}
                        {!! Form::hidden('for_company_id', $company->id, ['class' => 'form-control']) !!}
                        {!! Form::hidden('company_id', $company->reportsTo()->id, ['class' => 'form-control']) !!}
                        <div class="row form">
                            <div class="col-md-12">
                                <h3>
                                    {{ $doc_name }}
                                    {!! ($doc && $doc->status == 2) ?  '<span class="label label-warning label-sm">Pending Approval</span>' : '' !!}
                                    {!! ($doc && $doc->status == 3) ?  '<span class="label label-danger label-sm">Not approved</span>' : '' !!}
                                </h3>
                                <div class="form-body">
                                    @if (in_array($cat_type, [1,2,3]))

                                        <div class="form-group {!! fieldHasError('ref_name', $errors) !!}" id="ref_name_field">
                                            {!! Form::label('ref_name', 'Insurer:', ['class' => 'col-md-3 control-label']) !!}
                                            <div class="col-md-9">
                                                {!! Form::text('ref_name', null, ['class' => 'form-control', 'required']) !!}
                                                {!! fieldErrorMessage('ref_name', $errors) !!}
                                            </div>
                                        </div>
                                    @endif
                                    @if (in_array($cat_type, [2,3]))
                                        <div class="form-group {!! fieldHasError('ref_type', $errors) !!}" id="ref_type_field">
                                            {!! Form::label('ref_type', 'Category:', ['class' => 'col-md-3 control-label']) !!}
                                            <div class="col-md-9">
                                                {!! Form::select('ref_type', $company->workersCompCategorySelect('prompt'), null, ['class' => 'form-control', 'required']) !!}
                                                {!! fieldErrorMessage('ref_type', $errors) !!}
                                            </div>
                                        </div>
                                    @endif

                                    <div class="form-group {!! fieldHasError('notes', $errors) !!}" id="notes_field">
                                        {!! Form::label('notes', 'Notes', ['class' => 'col-md-3 control-label']) !!}
                                        <div class="col-md-9">
                                            {!! Form::textarea('notes', null, ['rows' => '3', 'class' => 'form-control']) !!}
                                            {!! fieldErrorMessage('notes', $errors) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="modal-footer">
                            <button class="btn default" onclick="cancelForm(event, 'doc{{ $cat_type }}')">Cancel</button>
                            @if ($doc && $doc->status == '2' && Auth::user()->allowed2('sig.company.ics', $doc))
                                <button type="submit" class="btn red" name="reject_doc">Reject</button>
                                <button type="submit" class="btn green">Approve</button>
                            @else
                                @if ($doc && $doc->status == '1' && Auth::user()->allowed2('del.company.ics', $doc))
                                    <button type="submit" class="btn dark" name="archive_doc">Archive</button>
                                @endif
                                <button type="submit" class="btn green"> Save</button>
                            @endif
                        </div>
                        {!! Form::close() !!}
                    </div>--}}
                @endforeach
            </div>
        @else
            No scheduled leave
        @endif

    </div>
</div>