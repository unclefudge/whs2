<div class="portlet light">
    <div class="portlet-title tabbable-line">
        <div class="caption">
            <i class="icon-bubbles font-dark hide"></i>
            <span class="caption-subject font-dark bold uppercase">Insurance & Contracts</span>
        </div>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#portlet_comments_1" data-toggle="tab"> Current </a></li>
            <li><a href="#portlet_comments_2" data-toggle="tab"> Expired </a></li>
        </ul>
    </div>

    <div class="portlet-body">
        <div class="tab-content">
            <div class="tab-pane active" id="portlet_comments_1">
                <div class="mt-comments">
                    {{-- Insurance & Contracts --}}
                    <?php $cat_types = ['1' => 'Public Liability', '2' => "Worker's Compensation", '3' => 'Sickness & Accident', '4' => 'Subcontactors Statement', '5' => 'Period Trade Contract'] ?>
                    @foreach ($cat_types as $cat_type => $doc_name)
                        <?php $doc = $company->activeCompanyDoc($cat_type) ?>
                        <div class="mt-comment" style="padding: 5px" id="show_doc{{ $cat_type }}">
                            <div class="mt-comment-img">
                                @if ($doc)
                                    <a href="{{ $doc->attachment_url }}" target="_blank"><i class="fa fa-file-pdf-o fa-2x" style="padding-top: 10px"></i></a>
                                @else
                                    <i class="fa fa-ban fa-2x" style="color: #ccc; padding-top: 5px"></i>
                                @endif</div>
                            <div class="mt-comment-body">
                                <div class="mt-comment-info">
                                    <span class="mt-comment-author">
                                        {!! ($doc) ? "<a href='$doc->attachment_url' target='_blank' style='color:#000'>$doc_name</a>" : $doc_name !!}
                                        {!! ($doc && $doc->status == 2) ?  '<span class="label label-warning label-sm">Pending approval</span>' : '' !!}
                                        {!! ($doc && $doc->status == 3) ?  '<span class="label label-danger label-sm">Not approved</span>' : '' !!}
                                    </span>
                                    <span class="mt-comment-date">{!! ($doc) ?  $doc->expiry->format('d/m/Y'): $company->requiresCompanyDocText($cat_type) !!}</span>
                                </div>
                                <div class="mt-comment-text">
                                    @if ($doc && in_array($cat_type, [1, 2, 3]))
                                        Policy No: {{ $doc->ref_no }} &nbsp; &nbsp; &nbsp; Insurer:  {{ $doc->ref_name }}
                                    @endif
                                </div>
                                <div class="mt-comment-details">
                                    <ul class="mt-comment-actions">
                                        @if ($doc && in_array($doc->status, [2,3]) && Auth::user()->allowed2('edit.company.ics', $doc) && $company->id == Auth::user()->company_id)
                                            <li>
                                                <button class="btn btn-xs dark" id="del_doc" data-doc_id="{{ $doc->id }}">Delete</button>
                                            </li>
                                        @endif
                                        @if (!$doc && Auth::user()->allowed2('add.company.ics', $company))
                                            <li>
                                                <button class="btn btn-xs btn-primary" onclick="editForm('doc{{ $cat_type }}')">Add</button>
                                            </li>
                                        @endif
                                        @if ($doc && Auth::user()->allowed2('edit.company.ics', $doc))
                                            <li>
                                                <button class="btn btn-xs btn-primary" onclick="editForm('doc{{ $cat_type }}')">Edit</button>
                                            </li>
                                        @endif

                                    </ul>
                                </div>
                            </div>
                            <hr style="margin: 5px 0px 0px 0px">
                        </div>

                        {{-- Edit Doc --}}
                        <div class="mt-comment" style="display: none" id="edit_doc{{ $cat_type }}">
                            {!! Form::model($doc, ['action' => ['Company\CompanyDocController@profileICS'], 'files' => true, 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'file-form']) !!}
                            {!! Form::hidden('doc_id', ($doc) ? $doc->id : 'new', ['class' => 'form-control', 'id' => 'doc_id']) !!}
                            {!! Form::hidden('category_id', $cat_type, ['class' => 'form-control', 'id' => 'category_id']) !!}
                            {!! Form::hidden('name', $doc_name, ['class' => 'form-control', 'id' => 'doc_name']) !!}
                            {!! Form::hidden('for_company_id', $company->id, ['class' => 'form-control']) !!}
                            {!! Form::hidden('company_id', $company->reportsTo()->id, ['class' => 'form-control']) !!}
                            <div class="row form">
                                <div class="col-md-12">
                                    <h3>
                                        {{ $doc_name }}
                                        {!! ($doc && $doc->status == 2) ?  '<span class="label label-warning label-sm">Pending approval</span>' : '' !!}
                                        {!! ($doc && $doc->status == 3) ?  '<span class="label label-danger label-sm">Not approved</span>' : '' !!}
                                    </h3>
                                    <div class="form-body">
                                        @if (in_array($cat_type, [1,2,3]))
                                            {{-- Document reference fields --}}
                                            <div class="form-group {!! fieldHasError('ref_no', $errors) !!}" id="ref_no_field">
                                                {!! Form::label('ref_no', 'Policy No:', ['class' => 'col-md-3 control-label']) !!}
                                                <div class="col-md-9">
                                                    {!! Form::text('ref_no', null, ['class' => 'form-control', 'required']) !!}
                                                    {!! fieldErrorMessage('ref_no', $errors) !!}
                                                </div>
                                            </div>
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
                                                {!! Form::label('ref_type', 'Category:', ['class' => 'col-md-3 control-label', 'required']) !!}
                                                <div class="col-md-9">
                                                    {!! Form::select('ref_type', $company->workersCompCategorySelect('prompt'), null, ['class' => 'form-control']) !!}
                                                    {!! fieldErrorMessage('ref_type', $errors) !!}
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Expiry --}}
                                        <div class="form-group {!! fieldHasError('expiry', $errors) !!}" id="expiry_field">
                                            {!! Form::label('expiry', 'Expiry', ['class' => 'col-md-3 control-label']) !!}
                                            <div class="col-md-5">
                                                <div class="input-group date date-picker" data-date-orientation="top right" data-date-format="dd/mm/yyyy">
                                                    <!-- data-date-start-date="+0d">-->
                                                    {!! Form::text('expiry', ($doc) ? $doc->expiry->format('d/m/Y') : null, ['class' => 'form-control form-control-inline', 'style' => 'background:#FFF', 'readonly', 'required']) !!}
                                                    <span class="input-group-btn">
                                                                        <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                                                    </span>
                                                </div>
                                                {!! fieldErrorMessage('expiry', $errors) !!}
                                            </div>
                                        </div>
                                        {{-- File attachment --}}
                                        <div class="form-group {!! fieldHasError('singlefile', $errors) !!}">
                                            {!! Form::label('singlefile', 'Document', ['class' => 'col-md-3 control-label']) !!}
                                            @if ($doc)
                                                <div class="col-md-9" style="padding-top: 7px;" id="file_field">
                                                    <a href="{{ $doc->attachment_url }}" target="_blank" id="doc_link">{{ $doc->attachment }}</a>
                                                    {{--
                                                    @if(in_array($doc->status, [2,3]) && $company->id == Auth::user()->company_id)
                                                        <a href="#" id="del_cross"><i class="fa fa-times font-red" style="font-size: 15px; padding-left: 20px"></i></a>
                                                    @endif
                                                    --}}
                                                </div>
                                                {!! fieldErrorMessage('singlefile', $errors) !!}
                                            @else
                                                <div class="col-md-9" style="padding-top: 7px;" id="file_div">
                                                    <input id="singlefile" name="singlefile" type="file" class="file-loading">
                                                    {!! fieldErrorMessage('singlefile', $errors) !!}
                                                </div>
                                            @endif
                                        </div>

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
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="tab-pane" id="portlet_comments_2" style="height: 380px">
                {{-- Expired Docs --}}
                <div class="col-md-12">
                    <table class="table table-striped table-bordered table-hover order-column" id="table_ic_expired">
                        <thead>
                        <tr class="mytable-header">
                            <th> Document</th>
                            <th width="10%"> Expired</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
