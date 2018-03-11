<div class="portlet light" id="show_whs">
    <div class="portlet-title tabbable-line">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">WHS Compliance</span>
        </div>
        <div class="actions">
            @if (Auth::user()->allowed2('edit.company.whs', $company))
                <button class="btn btn-circle green btn-outline btn-sm" onclick="editForm('whs')">Edit</button>
            @endif
        </div>
    </div>
    <div class="portlet-body">
        {{-- Licence equired --}}
        @if (Auth::user()->isCompany($company->reportsTo()->id))
            <div class="row">
                <div class="col-md-12">
                    This company {!! ($company->licence_required) ? 'requires' : 'does not require' !!} a Contractor Licence. &nbsp;
                    {!! ($company->licence_required != $company->requiresContractorsLicence()) ? ' &nbsp; <span class="font-red">OVERRIDDEN</span>' : '' !!}
                </div>
            </div>
            <hr class="field-hr">
        @endif

        <div class="tab-content" style="display:none">
            <div class="tab-pane active" id="portlet_comments_1">
                <div class="mt-comments">
                    {{-- WHS Compliance --}}
                    <?php $cat_types = ['7' => 'Contractors Licence', '8' => "Test & Tagging"] ?>
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
                                    <span class="mt-comment-author">{{ $doc_name }}</span>
                                    <span class="mt-comment-date">{!! ($doc) ?  $doc->expiry->format('d/m/Y'): '' !!}</span>
                                </div>
                                <div class="mt-comment-text">
                                    @if ($doc && in_array($cat_type, [7]))
                                        Lic: {{ $doc->ref_no }} &nbsp; &nbsp; &nbsp; Class:  {!! $company->contractorLicenceSBC() !!}
                                    @endif
                                </div>
                                <div class="mt-comment-details">
                                    @if ($doc && $doc->status == 2)
                                        <span class="label label-warning">Pending approval</span> @endif
                                    @if ($doc && $doc->status == 3)
                                        <span class="label label-danger">Not approved</span> @endif
                                    <ul class="mt-comment-actions">
                                        @if ($doc && Auth::user()->allowed2('edit.company.whs', $doc))
                                            <li>
                                                <button class="btn btn-xs btn-primary" onclick="editForm('doc{{ $cat_type }}')">Edit</button>
                                            </li>
                                        @endif
                                        @if (!$doc && Auth::user()->allowed2('edit.company.whs', $company))
                                            <li>
                                                <button class="btn btn-xs btn-primary" onclick="editForm('doc{{ $cat_type }}')">Add</button>
                                            </li>
                                        @endif

                                        @if ($doc && $doc->status == '2' && Auth::user()->allowed2('sig.company.ics', $doc))
                                            <li>
                                                <button class="btn btn-xs btn-danger" id="rej_doc" data-doc_id="{{ $doc->id }}">Reject</button>
                                            </li>
                                            <li>
                                                <button class="btn btn-xs btn-success" id="app_doc" data-doc_id="{{ $doc->id }}">Approve</button>
                                            </li>
                                        @endif
                                        @if ($doc && $doc->status == 2 && Auth::user()->allowed2('edit.company.ics', $doc) && $company->id == Auth::user()->company_id)
                                            <li>
                                                <button class="btn btn-xs dark" id="del_doc">Delete</button>
                                            </li>
                                        @endif

                                    </ul>
                                </div>
                            </div>
                            <hr style="margin: 5px 0px 0px 0px">
                        </div>

                        {{-- Edit Doc --}}
                        <div class="mt-comment" style="display: none" id="edit_doc{{ $cat_type }}">
                            {!! Form::model($doc, ['action' => ['Company\CompanyDocController@profileWHS'], 'files' => true, 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'file-form']) !!}
                            {!! Form::hidden('doc_id', ($doc) ? $doc->id : 'new', ['class' => 'form-control', 'id' => 'doc_id']) !!}
                            {!! Form::hidden('category_id', $cat_type, ['class' => 'form-control', 'id' => 'category_id']) !!}
                            {!! Form::hidden('name', $doc_name, ['class' => 'form-control', 'id' => 'doc_name']) !!}
                            {!! Form::hidden('for_company_id', $company->id, ['class' => 'form-control']) !!}
                            {!! Form::hidden('company_id', $company->reportsTo()->id, ['class' => 'form-control']) !!}
                            <div class="row form">
                                <div class="col-md-12">
                                    <h3>{{ $doc_name }}</h3>
                                    <div class="form-body">
                                        @if (in_array($cat_type, [7]))
                                            @if (in_array($company->business_entity, [1,2,4])) {{-- Company, Partnership, Trading Trust --}}
                                            <div class="col-md-3">&nbsp;</div>
                                            <div class="col-md-9 font-red">Licence is required to be in the name of {!! $companyEntityTypes::name($company->business_entity) !!}</div>
                                            @endif
                                            {{-- Contractor licence --}}
                                            <div class="form-group {!! fieldHasError('lic_no', $errors) !!}" id="lic_no_field">
                                                {!! Form::label('lic_no', 'Licence No.', ['class' => 'col-md-3 control-label']) !!}
                                                <div class="col-md-9">
                                                    {!! Form::text('lic_no', null, ['class' => 'form-control', 'required']) !!}
                                                    {!! fieldErrorMessage('lic_no', $errors) !!}
                                                </div>
                                            </div>
                                            <div class="form-group {!! fieldHasError('lic_type', $errors) !!}" id="lic_type_field">
                                                {!! Form::label('lic_type', 'Class(s)', ['class' => 'col-md-3 control-label']) !!}
                                                <div class="col-md-9">
                                                    <select id="lic_type" name="lic_type[]" class="form-control select2" width="100%" multiple required>
                                                        {!! $company->contractorLicenceOptions() !!}
                                                    </select>
                                                    {!! fieldErrorMessage('lic_type', $errors) !!}
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
                                                <div class="col-md-9" style="padding-top: 7px;" id="file_div">
                                                    <a href="{{ $doc->attachment_url }}" target="_blank" id="doc_link">{{ $doc->attachment }}</a>
                                                    @if($company->id == Auth::user()->company_id)
                                                        <a href="#" id="del_cross"><i class="fa fa-times font-red" style="font-size: 15px; padding-left: 20px"></i></a>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="col-md-9" style="padding-top: 7px;" id="file_div">
                                                    <input id="singlefile" name="singlefile" type="file" class="file-loading uploadfile">
                                                    {!! fieldErrorMessage('singlefile', $errors) !!}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="form-group {!! fieldHasError('notes', $errors) !!}" id="notes_field">
                                            {!! Form::label('notes', 'Notes', ['class' => 'col-md-3 control-label']) !!}
                                            <div class="col-md-7">
                                                {!! Form::textarea('notes', null, ['rows' => '3', 'class' => 'form-control']) !!}
                                                {!! fieldErrorMessage('notes', $errors) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button class="btn default" onclick="cancelForm(event, 'doc{{ $cat_type }}')">Cancel</button>
                                <button type="submit" class="btn green"> Save</button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="tab-pane" id="portlet_comments_2" style="height: 380px">
                {{-- Expired WHS --}}
                <div class="col-md-12">
                    <table class="table table-striped table-bordered table-hover order-column" id="table_whs_expired">
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
