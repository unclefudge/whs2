@inject('ozstates', 'App\Http\Utilities\Ozstates')
@inject('licenceTypes', 'App\Http\Utilities\LicenceTypes')
@inject('payrollTaxTypes', 'App\Http\Utilities\PayrollTaxTypes')
@inject('companyTypes', 'App\Http\Utilities\CompanyTypes')
@inject('companyEntityTypes', 'App\Http\Utilities\CompanyEntityTypes')
@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-users"></i> Company Profile</h1>
    </div>
@stop

@if (Auth::user()->company->status != 2)
@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->company->subscription > 1 && Auth::user()->hasAnyPermissionType('company'))
            <li><a href="/company">Companies</a><i class="fa fa-circle"></i></li>
            <li><span>Profile</span></li>
        @else
            <li><span>Company Profile</span></li>
        @endif
    </ul>
@stop
@endif

@section('content')
    {{-- BEGIN PAGE CONTENT INNER --}}
    <div class="page-content-inner">
        @if (Auth::user()->isCompany($company->id) && $company->signup_step)
            {{-- Company Signup Progress --}}
            <div class="mt-element-step">
                <div class="row step-line" id="steps">
                    <div class="col-md-3 mt-step-col first active">
                        <div class="mt-step-number bg-white font-grey">1</div>
                        <div class="mt-step-title uppercase font-grey-cascade">Business Owner</div>
                        <div class="mt-step-content font-grey-cascade">Add primary user</div>
                    </div>
                    <div class="col-md-3 mt-step-col active">
                        <div class="mt-step-number bg-white font-grey">2</div>
                        <div class="mt-step-title uppercase font-grey-cascade">Company Info</div>
                        <div class="mt-step-content font-grey-cascade">Add company info</div>
                    </div>
                    <div class="col-md-3 mt-step-col active">
                        <div class="mt-step-number bg-white font-grey">3</div>
                        <div class="mt-step-title uppercase font-grey-cascade">Workers</div>
                        <div class="mt-step-content font-grey-cascade">Add workers</div>
                    </div>
                    <div class="col-md-3 mt-step-col last">
                        <div class="mt-step-number bg-white font-grey">4</div>
                        <div class="mt-step-title uppercase font-grey-cascade">Documents</div>
                        <div class="mt-step-content font-grey-cascade">Upload documents</div>
                    </div>
                </div>
            </div>

            <div class="note note-warning">
                <b>Step 4 : Upload required documents for your company.</b><br><br>
                Documents required are determined by the information you have provided to us. Required documents have be marked 'Required'<br><br>
                Once you've added all your documents please click
                <button class="btn dark btn-outline btn-xs" href="javascript:;"> Completed Signup</button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="member-bar">
                    <!--<i class="fa fa-user ppicon-user-member-bar" style="font-size: 80px; opacity: .5; padding:5px"></i>-->
                    <i class="icon-users-member-bar"></i>
                    <div class="member-name">
                        <div class="full-name-wrap">
                            <a href="/reseller/member/member_account_details/?member_id=8013759" class="status-update">{{ $company->name }}</a>
                        </div>
                        <span class="member-number">Company ID #{{ $company->id }}</span>
                        <span class="member-split">&nbsp;|&nbsp;</span>
                        <span class="member-number">Active</span>
                        <!--<a href="/reseller/member/member_account_status/?member_id=8013759" class="member-status">Active</a>-->

                    </div>
                    @if (!$company->compliantDocs())
                        <span class='label label-danger'>Non Compliant</span>
                    @endif
                    {{--
        <ul class="member-bar-menu">
            <li class="member-bar-item active"><i class="icon-customer"></i><a class="member-bar-link" href="/reseller/member/member_account_details/?member_id=8013759" title="Profile">PROFILE</a>
            </li>
            <li class="member-bar-item "><i class="icon-domains"></i><a class="member-bar-link" href="/reseller/member/member_manage_domains/?member_id=8013759" title="Domains">DOMAINS</a>
            </li>
            <li class="member-bar-item "><i class="icon-hosting"></i><a class="member-bar-link" href="/reseller/member/member_manage_hosting/?member_id=8013759&product_type=hosting"
                                                                        title="Hosting">HOSTING</a></li>
            <li class="member-bar-item "><i class="icon-products"></i><a class="member-bar-link" href="/reseller/member/member_manage_products/?member_id=8013759"
                                                                         title="Products">PRODUCTS</a></li>
            <li class="member-bar-item "><i class="icon-login"></i><a class="member-bar-link"
                                                                      href="http://fudge.secureapi.com.au/members/member/member_login/?login_as=WiZ%2BqLpDc3enUYT%2BAATcVFL0vnmvAEl6ePHU%2FfgDwgJdA3Wdf05URqzZQZo9yMeIe899vfnaBkMGkZT%2F8sKpTFtLj%2F1I%2FB%2B71sIJjm%2B1jxSrC2I5K31%2FK1ylS2oSnLvbsf0pxUC2xZbjr7VhDuDwxAGOskD7Q%2FYzNhr8Wyk9Mxplu3K0SuvlJGfZtvJRITOCidH6DUCSVR3qyrg%2BNwGBQ1OQAkVfv6w5EmBNYh0Mc39MbqfEXio%2BYHsbpu8YByyrUZUVNDdn1cc%3D"
                                                                      target="_blank" title="Login as Customer">LOG IN</a></li>
        </ul>
        --}}
                </div>
            </div>
        </div>
        <div class="row">
            {{-- Company Details --}}
            <div class="col-lg-6 col-xs-12 col-sm-12">
                {{-- Show Company Details --}}
                <div class="portlet light" id="show_company">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-dark bold uppercase">Company Details</span>
                            @if(!$company->approved_by && $company->reportsTo()->id == Auth::user()->company_id)
                                <span class="label label-warning">Pending approval</span>
                            @endif
                        </div>
                        <div class="actions">
                            @if (Auth::user()->allowed2('sig.company', $company) && !$company->approved_by)
                                <a href="/company/{{ $company->id }}/approve" class="btn btn-circle green btn-outline btn-sm" id="but_approve">Approve</a>
                            @endif
                            <a href="#" class="btn btn-circle green btn-outline btn-sm" id="update_company">Edit</a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-3">Status:</div>
                            <div class="col-xs-9">{!! $company->status_text !!}</div>
                        </div>
                        <hr class="field-hr">
                        <div class="row">
                            <div class="col-md-3">Name:</div>
                            <div class="col-xs-9">{{ $company->name }}</div>
                        </div>
                        <hr class="field-hr">
                        @if (Auth::user()->isCompany($company->reportsTo()->id) && !Auth::user()->isCompany($company->id))
                            <div class="row">
                                <div class="col-md-3">Preferred Name:</div>
                                <div class="col-md-9">@if($company->nickname) {{ $company->nickname }} @else - @endif</div>
                            </div>
                            <hr class="field-hr">
                        @endif
                        <div class="row">
                            <div class="col-md-3">Phone:</div>
                            <div class="col-xs-9">{!! ($company->phone) ? "<a href='tel:'".preg_replace("/[^0-9]/", "", $company->phone)."> $company->phone </a>" : '-' !!}</div>
                        </div>
                        <hr class="field-hr">
                        <div class="row">
                            <div class="col-md-3">Email:</div>
                            <div class="col-xs-9">{!! ($company->email) ? "<a href='mailto:$company->email'> $company->email</a>" : '-' !!}</div>
                        </div>
                        <hr class="field-hr">
                        <div class="row">
                            <div class="col-md-3">Address:</div>
                            <div class="col-xs-9">{!! $company->address_formatted !!}</div>
                        </div>
                        <hr class="field-hr">
                        <div class="row">
                            <div class="col-md-3">Primary Contact:</div>
                            <div class="col-xs-9">@if($company->primary_user)<a href="/user/{{ $company->primary_contact()->id }}">{{ $company->primary_contact()->fullname }}</a>@else - @endif</div>
                        </div>
                        <hr class="field-hr">
                        <div class="row">
                            <div class="col-md-3">Secondary Contact:</div>
                            <div class="col-xs-9">@if($company->secondary_user)<a href="/user/{{ $company->secondary_contact()->id }}">{{ $company->secondary_contact()->fullname }}</a>@else - @endif
                            </div>
                        </div>
                        <hr class="field-hr">
                    </div>
                </div>

                {{-- Edit Company Details --}}
                <div class="portlet light" style="display: none;" id="edit_company">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-dark bold uppercase">Company Details</span>
                            @if(!$company->approved_by && $company->reportsTo()->id == Auth::user()->company_id)
                                <span class="label label-warning">Pending approval</span>
                            @endif
                        </div>
                    </div>
                    <div class="portlet-body form">
                        {!! Form::model($company, ['method' => 'PATCH', 'action' => ['Company\CompanyController@update', $company->id]]) !!}
                        {{-- Status --}}
                        <div class="row">
                            @if(Auth::user()->allowed2('del.company', $company))
                                <div class="form-group {!! fieldHasError('status', $errors) !!}">
                                    {!! Form::label('status', 'Status:', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-9">
                                        {!! Form::select('status', ['1' => 'Active', '0' => 'Inactive'], null, ['class' => 'form-control bs-select']) !!}
                                        {!! fieldErrorMessage('status', $errors) !!}
                                    </div>
                                </div>
                            @else
                                <div class="col-md-3">Status:</div>
                                <div class="col-xs-9">{!! $company->status_text !!}</div>
                            @endif
                        </div>
                        <hr class="field-hr">
                        {{-- Name --}}
                        <div class="row">
                            <div class="form-group {!! fieldHasError('name', $errors) !!}">
                                {!! Form::label('name', 'Name:', ['class' => 'col-md-3 control-label']) !!}
                                <div class="col-md-9">
                                    {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                                    {!! fieldErrorMessage('name', $errors) !!}
                                </div>
                            </div>
                        </div>
                        <hr class="field-hr">
                        {{-- Preferred Name --}}
                        @if (Auth::user()->isCompany($company->reportsTo()->id) && !Auth::user()->isCompany($company->id))
                            <div class="row">
                                <div class="form-group {!! fieldHasError('nickname', $errors) !!}">
                                    {!! Form::label('nickname', 'Preferred Name:', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-9">
                                        {!! Form::text('nickname', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('nickname', $errors) !!}
                                    </div>
                                </div>
                            </div>
                            <hr class="field-hr">
                        @endif
                        {{-- Phone --}}
                        <div class="row">
                            <div class="form-group {!! fieldHasError('phone', $errors) !!}">
                                {!! Form::label('phone', 'Phone:', ['class' => 'col-md-3 control-label']) !!}
                                <div class="col-md-9">
                                    {!! Form::text('phone', null, ['class' => 'form-control', 'required']) !!}
                                    {!! fieldErrorMessage('phone', $errors) !!}
                                </div>
                            </div>
                        </div>
                        <hr class="field-hr">
                        {{-- Email --}}
                        <div class="row">
                            <div class="form-group {!! fieldHasError('email', $errors) !!}">
                                {!! Form::label('email', 'Email:', ['class' => 'col-md-3 control-label']) !!}
                                <div class="col-md-9">
                                    {!! Form::text('email', null, ['class' => 'form-control', 'required']) !!}
                                    {!! fieldErrorMessage('email', $errors) !!}
                                </div>
                            </div>
                        </div>
                        <hr class="field-hr">
                        {{-- Adddress --}}
                        <div class="row">
                            <div class="form-group {!! fieldHasError('address', $errors) !!}">
                                {!! Form::label('address', 'Address:', ['class' => 'col-md-3 control-label']) !!}
                                <div class="col-md-9">
                                    {!! Form::text('address', null, ['class' => 'form-control', 'required']) !!}
                                    {!! fieldErrorMessage('address', $errors) !!}
                                </div>
                            </div>
                        </div>
                        <hr class="field-hr">
                        {{-- Suburb --}}
                        <div class="row">
                            <div class="form-group {!! fieldHasError('suburb', $errors) !!}">
                                {!! Form::label('suburb', 'Suburb:', ['class' => 'col-md-3 control-label']) !!}
                                <div class="col-md-9">
                                    {!! Form::text('suburb', null, ['class' => 'form-control', 'required']) !!}
                                    {!! fieldErrorMessage('suburb', $errors) !!}
                                </div>
                            </div>
                        </div>
                        <hr class="field-hr">
                        {{-- State --}}
                        <div class="row">
                            <div class="form-group {!! fieldHasError('state', $errors) !!}">
                                {!! Form::label('state', 'State:', ['class' => 'col-md-3 control-label']) !!}
                                <div class="col-md-9">
                                    {!! Form::select('state', $ozstates::all(), 'NSW', ['class' => 'form-control bs-select', 'required']) !!}
                                    {!! fieldErrorMessage('state', $errors) !!}
                                </div>
                            </div>
                        </div>
                        <hr class="field-hr">
                        {{-- Postcode --}}
                        <div class="row">
                            <div class="form-group {!! fieldHasError('postcode', $errors) !!}">
                                {!! Form::label('postcode', 'Postcode:', ['class' => 'col-md-3 control-label']) !!}
                                <div class="col-md-9">
                                    {!! Form::text('postcode', null, ['class' => 'form-control', 'required']) !!}
                                    {!! fieldErrorMessage('postcode', $errors) !!}
                                </div>
                            </div>
                        </div>
                        <hr class="field-hr">
                        {{-- Primary Contact --}}
                        <div class="row">
                            <div class="form-group {!! fieldHasError('primary_user', $errors) !!}">
                                {!! Form::label('primary_user', 'Primary Contact:', ['class' => 'col-md-3 control-label']) !!}
                                <div class="col-md-9">
                                    {!! Form::select('primary_user', $company->usersSelect('prompt'),null, ['class' => 'form-control bs-select', 'required']) !!}
                                    {!! fieldErrorMessage('primary_user', $errors) !!}
                                </div>
                            </div>
                        </div>
                        <hr class="field-hr">
                        {{-- Seconday Contact --}}
                        <div class="row">
                            <div class="form-group {!! fieldHasError('secondary_user', $errors) !!}">
                                {!! Form::label('secondary_user', 'Secondary Contact:', ['class' => 'col-md-3 control-label']) !!}
                                <div class="col-md-9">
                                    {!! Form::select('secondary_user',  array_merge(['0' => 'None'], $company->usersSelect()), null, ['class' => 'form-control bs-select', 'required']) !!}
                                    {!! fieldErrorMessage('secondary_user', $errors) !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-actions right">
                            @if ($company->status == 2)
                                <button type="submit" class="btn green"> Continue</button>
                            @else
                                <button class="btn default" id="cancel_company"> Cancel</button>
                                <button type="submit" class="btn green" id="save_company"> Save</button>
                            @endif
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>


            {{-- Business Details --}}
            <div class="col-lg-6 col-xs-12 col-sm-12">
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-dark bold uppercase">Business Details</span>
                        </div>
                        <div class="actions">
                            @if (Auth::user()->allowed2('sig.company', $company) && !$company->approved_by)
                                <a href="/company/{{ $company->id }}/approve" class="btn btn-circle green btn-outline btn-sm" id="but_approve">Approve</a>
                            @endif
                            <a href="/company/{{ $company->id }}/edit" class="btn btn-circle green btn-outline btn-sm">Edit</a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-3">Business Entity:</div>
                            <div class="col-xs-9">{{ ($company->business_entity) ? $companyEntityTypes::name($company->business_entity) : '-' }}</div>
                        </div>
                        <hr class="field-hr">
                        <div class="row">
                            <div class="col-md-3">Category:</div>
                            <div class="col-xs-9">{{ $companyTypes::name($company->category) }}</div>
                        </div>
                        <hr class="field-hr">
                        <div class="row">
                            <div class="col-md-3">ABN:</div>
                            <div class="col-xs-9">{{ ($company->abn) ? $company->abn : '-' }}</div>
                        </div>
                        <hr class="field-hr">
                        <div class="row">
                            <div class="col-md-3">GST:</div>
                            <div class="col-xs-9">@if($company->gst) Yes @elseif($company->gst == '0') No @else - @endif</div>
                        </div>
                        <hr class="field-hr">
                        <div class="row">
                            <div class="col-md-3">Payroll Tax:</div>
                            <div class="col-xs-9">@if($company->payroll_tax) {{ ($company->payroll_tax > 0 && $company->payroll_tax < 8) ? 'Exempt (' . $company->payroll_tax . ')' : 'Liable' }} @else
                                    - @endif</div>
                        </div>
                        <hr class="field-hr">
                        <div class="row">
                            <div class="col-md-3">Creditor Code:</div>
                            <div class="col-xs-9">{{ ($company->creditor_code) ? $company->creditor_code : '-' }}</div>
                        </div>
                        <hr class="field-hr">
                    </div>
                </div>
            </div>

        </div>

        {{-- --}}

        <div class="row">
            <div class="col-lg-6 col-xs-12 col-sm-12">
                <div class="portlet light ">
                    <div class="portlet-title tabbable-line">
                        <div class="caption">
                            <i class="icon-bubbles font-dark hide"></i>
                            <span class="caption-subject font-dark bold uppercase">Insurance & Contracts</span>
                        </div>
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#portlet_comments_1" data-toggle="tab"> Pending </a>
                            </li>
                            <li>
                                <a href="#portlet_comments_2" data-toggle="tab"> Approved </a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="portlet_comments_1">
                                <!-- BEGIN: Comments -->
                                <div class="mt-comments">
                                    <div class="mt-comment">
                                        <div class="mt-comment-img"><a href="#"><i class="fa fa-file-pdf-o fa-2x" style="padding-top: 10px"></i></a></div>
                                        <div class="mt-comment-body">
                                            <div class="mt-comment-info">
                                                <span class="mt-comment-author">Public Liabilty</span>
                                                <span class="mt-comment-date">19 Dec,09:50 AM</span>
                                            </div>
                                            <div class="mt-comment-text"> Policy No: GPM00034075 &nbsp; &nbsp; &nbsp; Insurer: GIO</div>
                                            <div class="mt-comment-details">
                                                @if (($company->activeCompanyDoc('1')->status == 2))
                                                    <span class="label label-warning">Pending approval</span> @endif
                                                @if (($company->activeCompanyDoc('1')->status == 3))
                                                    <span class="label label-danger">Not approved</span> @endif
                                                <ul class="mt-comment-actions">
                                                    @if (Auth::user()->allowed2('edit.company.doc.ics', $company->activeCompanyDoc('1')))
                                                        <li>
                                                            <a class="edit-file" href="#file-modal" data-toggle="modal" data-cat='1' data-action="edit"
                                                               data-doc_id="{{ $company->activeCompanyDoc('1')->id }}"
                                                               data-ref_no="{{ $company->activeCompanyDoc('1')->ref_no }}"
                                                               data-ref_name="{{ $company->activeCompanyDoc('1')->ref_name }}"
                                                               data-expiry="{{ ($company->activeCompanyDoc('1')->expiry) ? $company->activeCompanyDoc('1')->expiry->format('d/m/Y') : '' }}"
                                                               data-notes="{{ $company->activeCompanyDoc('1')->notes }}"
                                                               data-doc_name="{{ $company->activeCompanyDoc('1')->attachment }}"
                                                               data-doc_url="{{ $company->activeCompanyDoc('1')->attachment_url }}"
                                                               data-doc_status="{{ $company->activeCompanyDoc('1')->status }}">Edit</a>
                                                        </li>
                                                    @endif

                                                    <li><a href="#">Approve</a></li>
                                                    <li><a href="#">Delete</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-comment">
                                        <div class="mt-comment-img"><a href="#"><i class="fa fa-file-pdf-o fa-2x" style="padding-top: 10px"></i></a></div>
                                        <div class="mt-comment-body">
                                            <div class="mt-comment-info">
                                                <span class="mt-comment-author">Worker's Compensation</span>
                                                <span class="mt-comment-date">19 Dec,09:50 AM</span>
                                            </div>
                                            <div class="mt-comment-text"> Policy No: GPM00034075 &nbsp; &nbsp; &nbsp; Insurer: GIO<br>Category: a. Is a Propriety Limited Company (Pty Ltd)</div>
                                            <div class="mt-comment-details">
                                                <span class="mt-comment-status mt-comment-status-pending">Pending</span>
                                                <ul class="mt-comment-actions">
                                                    <li><a href="#">Quick Edit</a></li>
                                                    <li><a href="#">View</a></li>
                                                    <li><a href="#">Delete</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END: Comments -->
                            </div>
                            <div class="tab-pane" id="portlet_comments_2">
                                <!-- BEGIN: Comments -->
                                <div class="mt-comments">
                                    <div class="mt-comment">
                                        <div class="mt-comment-img">
                                            <img src="../assets/pages/media/users/avatar1.jpg"/></div>
                                        <div class="mt-comment-body">
                                            <div class="mt-comment-info">
                                                <span class="mt-comment-author">Sebastian Davidson</span>
                                                <span class="mt-comment-date">10 Dec, 09:20 AM</span>
                                            </div>
                                            <div class="mt-comment-text"> The standard chunk of Lorem Ipsum used since the 1500s</div>
                                            <div class="mt-comment-details">
                                                <span class="mt-comment-status mt-comment-status-approved">Approved</span>
                                                <ul class="mt-comment-actions">
                                                    <li><a href="#">Quick Edit</a></li>
                                                    <li><a href="#">View</a></li>
                                                    <li><a href="#">Delete</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END: Comments -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-6 col-xs-12 col-sm-12">
                <div class="portlet light ">
                    <div class="portlet-title tabbable-line">
                        <div class="caption">
                            <i class=" icon-social-twitter font-dark hide"></i>
                            <span class="caption-subject font-dark bold uppercase">Quick Actions</span>
                        </div>
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#tab_actions_pending" data-toggle="tab"> Pending </a>
                            </li>
                            <li>
                                <a href="#tab_actions_completed" data-toggle="tab"> Completed </a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_actions_pending">
                                <!-- BEGIN: Actions -->
                                <div class="mt-actions">
                                    <div class="mt-action">
                                        <div class="mt-action-img"></div>
                                        <div class="mt-action-body">
                                            <div class="mt-action-row">
                                                <div class="mt-action-info ">
                                                    <div class="mt-action-icon ">
                                                        <i class="icon-magnet"></i>
                                                    </div>
                                                    <div class="mt-action-details ">
                                                        <span class="mt-action-author">Natasha Kim</span>
                                                        <p class="mt-action-desc">Dummy text of the printing</p>
                                                    </div>
                                                </div>
                                                <div class="mt-action-datetime ">
                                                    <span class="mt-action-date">3 jun</span>
                                                    <span class="mt-action-dot bg-green"></span>
                                                    <span class="mt=action-time">9:30-13:00</span>
                                                </div>
                                                <div class="mt-action-buttons ">
                                                    <div class="btn-group btn-group-circle">
                                                        <button type="button" class="btn btn-outline green btn-sm">Appove</button>
                                                        <button type="button" class="btn btn-outline red btn-sm">Reject</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-action">
                                        <div class="mt-action-img">
                                            <img src="../assets/pages/media/users/avatar9.jpg"/></div>
                                        <div class="mt-action-body">
                                            <div class="mt-action-row">
                                                <div class="mt-action-info ">
                                                    <div class="mt-action-icon ">
                                                        <i class="icon-magnet"></i>
                                                    </div>
                                                    <div class="mt-action-details ">
                                                        <span class="mt-action-author">Tom Larson</span>
                                                        <p class="mt-action-desc">Lorem Ipsum is simply dummy text</p>
                                                    </div>
                                                </div>
                                                <div class="mt-action-datetime ">
                                                    <span class="mt-action-date">3 jun</span>
                                                    <span class="mt-action-dot bg-green"></span>
                                                    <span class="mt=action-time">9:30-13:00</span>
                                                </div>
                                                <div class="mt-action-buttons ">
                                                    <div class="btn-group btn-group-circle">
                                                        <button type="button" class="btn btn-outline green btn-sm">Appove</button>
                                                        <button type="button" class="btn btn-outline red btn-sm">Reject</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END: Actions -->
                            </div>
                            <div class="tab-pane" id="tab_actions_completed">
                                <!-- BEGIN:Completed-->
                                <div class="mt-actions">

                                    <div class="mt-action">
                                        <div class="mt-action-img">
                                            <img src="../assets/pages/media/users/avatar5.jpg"/></div>
                                        <div class="mt-action-body">
                                            <div class="mt-action-row">
                                                <div class="mt-action-info ">
                                                    <div class="mt-action-icon ">
                                                        <i class=" icon-graduation"></i>
                                                    </div>
                                                    <div class="mt-action-details ">
                                                        <span class="mt-action-author">Jason Dickens </span>
                                                        <p class="mt-action-desc">Dummy text of the printing and typesetting industry</p>
                                                    </div>
                                                </div>
                                                <div class="mt-action-datetime ">
                                                    <span class="mt-action-date">3 jun</span>
                                                    <span class="mt-action-dot bg-red"></span>
                                                    <span class="mt=action-time">9:30-13:00</span>
                                                </div>
                                                <div class="mt-action-buttons ">
                                                    <div class="btn-group btn-group-circle">
                                                        <button type="button" class="btn btn-outline green btn-sm">Appove</button>
                                                        <button type="button" class="btn btn-outline red btn-sm">Reject</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-action">
                                        <div class="mt-action-img">
                                            <img src="../assets/pages/media/users/avatar2.jpg"/></div>
                                        <div class="mt-action-body">
                                            <div class="mt-action-row">
                                                <div class="mt-action-info ">
                                                    <div class="mt-action-icon ">
                                                        <i class="icon-badge"></i>
                                                    </div>
                                                    <div class="mt-action-details ">
                                                        <span class="mt-action-author">Jan Kim</span>
                                                        <p class="mt-action-desc">Lorem Ipsum is simply dummy</p>
                                                    </div>
                                                </div>
                                                <div class="mt-action-datetime ">
                                                    <span class="mt-action-date">3 jun</span>
                                                    <span class="mt-action-dot bg-green"></span>
                                                    <span class="mt=action-time">9:30-13:00</span>
                                                </div>
                                                <div class="mt-action-buttons ">
                                                    <div class="btn-group btn-group-circle">
                                                        <button type="button" class="btn btn-outline green btn-sm">Appove</button>
                                                        <button type="button" class="btn btn-outline red btn-sm">Reject</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END: Completed -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-body form">
                    <div class="row">
                        <div class="col-md-12">
                            @if ($company->status != 2 && !(Auth::user()->isCompany($company->id) && $company->signup_step))

                            @endif

                            {{-- Insurance & Contracts --}}
                            @if(Auth::user()->allowed2('show.company.doc.ics', $company))
                                <h3 class="font-green form-section">Insurance & Contracts
                                    <button class="btn btn-xs default pull-right" id="but_show_ic_expired">Show Expired</button>
                                    <button class="btn btn-xs default pull-right" style="display:none" id="but_show_ic_current">Show Current</button>
                                </h3>
                                <div id="ic_current">
                                    {{-- Public Liabilty --}}
                                    <div class="row" style="background:#fafafa; margin-bottom: 3px; line-height: 2">
                                        @if ($company->activeCompanyDoc('1'))
                                            <div class="col-md-3">
                                                <a href="{{ $company->activeCompanyDoc('1')->attachment_url }}" style="color:#333; display: block">
                                                    <i class="fa fa-file-pdf-o" style="font-size: 20px; min-width: 35px"></i><b>Public Liability</b></a>
                                                @if (($company->activeCompanyDoc('1')->status == 2))
                                                    <span class="label label-warning" style="margin-left:30px">Pending approval</span> @endif
                                                @if (($company->activeCompanyDoc('1')->status == 3))
                                                    <span class="label label-danger" style="margin-left:30px">Not approved</span> @endif
                                            </div>
                                            <div class="col-md-2">{!! format_expiry_field($company->activeCompanyDoc('1')->expiry) !!}</div>
                                            <div class="col-md-2"><b>Policy No:</b> {{ $company->activeCompanyDoc('1')->ref_no }}</div>
                                            <div class="col-md-4"><b>Insurer:</b> {{ $company->activeCompanyDoc('1')->ref_name }}</div>
                                            <div class="col-md-1">
                                                @if (Auth::user()->allowed2('edit.company.doc.ics', $company->activeCompanyDoc('1')))
                                                    <a class="btn btn-xs default edit-file" href="#file-modal" data-toggle="modal" data-cat='1' data-action="edit"
                                                       data-doc_id="{{ $company->activeCompanyDoc('1')->id }}"
                                                       data-ref_no="{{ $company->activeCompanyDoc('1')->ref_no }}"
                                                       data-ref_name="{{ $company->activeCompanyDoc('1')->ref_name }}"
                                                       data-expiry="{{ ($company->activeCompanyDoc('1')->expiry) ? $company->activeCompanyDoc('1')->expiry->format('d/m/Y') : '' }}"
                                                       data-notes="{{ $company->activeCompanyDoc('1')->notes }}"
                                                       data-doc_name="{{ $company->activeCompanyDoc('1')->attachment }}"
                                                       data-doc_url="{{ $company->activeCompanyDoc('1')->attachment_url }}"
                                                       data-doc_status="{{ $company->activeCompanyDoc('1')->status }}">Edit</a>
                                                @endif
                                            </div>
                                        @else
                                            <div class="col-md-3"><i class="fa" style="font-size: 20px; min-width: 35px"></i><b>Public Liability</b></div>
                                            <div class="col-md-8">@if ($company->category == 'On Site Trade') <span class="font-red">Required</span> @endif</div>
                                            <div class="col-md-1">
                                                @if (Auth::user()->allowed2('add.company.doc.ics'))
                                                    <a class="btn btn-xs default edit-file" href="#file-modal" data-toggle="modal" data-cat='1' data-action="add">Add</a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    {{-- Workers Comp --}}
                                    <div class="row" style="background:#fafafa; margin-bottom: 3px; line-height: 2">
                                        @if ($company->activeCompanyDoc('2'))
                                            <div class="col-md-3">
                                                <a href="{{ $company->activeCompanyDoc('2')->attachment_url }}" style="color:#333; display: block">
                                                    <i class="fa fa-file-pdf-o" style="font-size: 20px; min-width: 35px"></i><b>Worker's Compensation</b></a>
                                                @if (($company->activeCompanyDoc('2')->status == 2))
                                                    <span class="label label-warning" style="margin-left:30px">Pending approval</span> @endif
                                                @if (($company->activeCompanyDoc('2')->status == 3))
                                                    <span class="label label-danger" style="margin-left:30px">Not approved</span> @endif
                                            </div>
                                            <div class="col-md-2">{!! ($company->activeCompanyDoc('2')) ? format_expiry_field($company->activeCompanyDoc('2')->expiry) : '-' !!}</div>
                                            <div class="col-md-2"><b>Policy No:</b> {{ $company->activeCompanyDoc('2')->ref_no }}</div>
                                            <div class="col-md-4"><b>Insurer:</b> {{ $company->activeCompanyDoc('2')->ref_name }}</div>
                                            <div class="col-md-7 visible-sm visible-xs"><b>Category:</b> {{ $company->activeCompanyDoc('2')->ref_type }}</div>
                                            <div class="col-md-1">
                                                @if (Auth::user()->allowed2('edit.company.doc.ics', $company->activeCompanyDoc('2')))
                                                    <a class="btn btn-xs default edit-file" href="#file-modal" data-toggle="modal" data-cat='2' data-action="edit"
                                                       data-doc_id="{{ $company->activeCompanyDoc('2')->id }}"
                                                       data-ref_no="{{ $company->activeCompanyDoc('2')->ref_no }}"
                                                       data-ref_name="{{ $company->activeCompanyDoc('2')->ref_name }}"
                                                       data-ref_type="{{ $company->activeCompanyDoc('2')->ref_type }}"
                                                       data-expiry="{{ ($company->activeCompanyDoc('2')->expiry) ? $company->activeCompanyDoc('2')->expiry->format('d/m/Y') : '' }}"
                                                       data-notes="{{ $company->activeCompanyDoc('2')->notes }}"
                                                       data-doc_name="{{ $company->activeCompanyDoc('2')->attachment }}"
                                                       data-doc_url="{{ $company->activeCompanyDoc('2')->attachment_url }}"
                                                       data-doc_status="{{ $company->activeCompanyDoc('2')->status }}">Edit</a>
                                                @endif
                                            </div>
                                        @else
                                            <div class="col-md-3"><i class="fa" style="font-size: 20px; min-width: 35px"></i><b>Worker's Compensation</b></div>
                                            <div class="col-md-8">{!! ($company->requiresWCinsurance()) ? '<span class="font-red">Required</span>' : '-' !!}</div>
                                            <div class="col-md-1">
                                                @if (Auth::user()->allowed2('add.company.doc.ics'))
                                                    <a class="btn btn-xs default edit-file" href="#file-modal" data-toggle="modal" data-cat='2' data-action="add">Add</a>
                                                @endif
                                            </div>
                                        @endif
                                        @if ($company->activeCompanyDoc('2'))
                                            <div class="col-md-7 hidden-sm hidden-xs pull-right"><b>Category:</b> {{ $company->activeCompanyDoc('2')->ref_type }}</div>
                                        @endif
                                    </div>

                                    {{-- Sickness & Accident Insurance --}}
                                    <div class="row" style="background:#fafafa; margin-bottom: 3px; line-height: 2">
                                        @if ($company->activeCompanyDoc('3'))
                                            <div class="col-md-3">
                                                <a href="{{ $company->activeCompanyDoc('3')->attachment_url }}" style="color:#333; display: block">
                                                    <i class="fa fa-file-pdf-o" style="font-size: 20px; min-width: 35px"></i><b>Sickness & Accident</b></a>
                                                @if (($company->activeCompanyDoc('3')->status == 2)) <span class="label label-warning"
                                                                                                           style="margin-left:30px">Pending approval</span> @endif
                                                @if (($company->activeCompanyDoc('3')->status == 3)) <span class="label label-danger"
                                                                                                           style="margin-left:30px">Not approved</span> @endif
                                            </div>
                                            <div class="col-md-2">{!! ($company->activeCompanyDoc('3')) ? format_expiry_field($company->activeCompanyDoc('3')->expiry) : '-' !!}</div>
                                            <div class="col-md-3"><b>Policy No:</b> {{ $company->activeCompanyDoc('3')->ref_no }}</div>
                                            <div class="col-md-3"><b>Insurer:</b> {{ $company->activeCompanyDoc('3')->ref_name }}</div>
                                            <div class="col-md-7 visible-sm visible-xs"><b>Category:</b> {{ $company->activeCompanyDoc('3')->ref_type }}</div>
                                            <div class="col-md-1">
                                                @if (Auth::user()->allowed2('edit.company.doc.ics', $company->activeCompanyDoc('3')))
                                                    <a class="btn btn-xs default edit-file" href="#file-modal" data-toggle="modal" data-cat='3' data-action="edit"
                                                       data-doc_id="{{ $company->activeCompanyDoc('3')->id }}"
                                                       data-ref_no="{{ $company->activeCompanyDoc('3')->ref_no }}"
                                                       data-ref_name="{{ $company->activeCompanyDoc('3')->ref_name }}"
                                                       data-ref_type="{{ $company->activeCompanyDoc('3')->ref_type }}"
                                                       data-expiry="{{ ($company->activeCompanyDoc('3')->expiry) ? $company->activeCompanyDoc('3')->expiry->format('d/m/Y') : '' }}"
                                                       data-notes="{{ $company->activeCompanyDoc('3')->notes }}"
                                                       data-doc_name="{{ $company->activeCompanyDoc('3')->attachment }}"
                                                       data-doc_url="{{ $company->activeCompanyDoc('3')->attachment_url }}"
                                                       data-doc_status="{{ $company->activeCompanyDoc('3')->status }}">Edit</a>
                                                @endif
                                            </div>
                                        @else
                                            <div class="col-md-3"><i class="fa" style="font-size: 20px; min-width: 35px"></i><b>Sickness & Accident</b></div>
                                            <div class="col-md-8">{!! ($company->requiresSAinsurance()) ? '<span class="font-red">Required</span>' : '-' !!}</div>
                                            <div class="col-md-1">
                                                @if (Auth::user()->allowed2('add.company.doc.ics'))
                                                    <a class="btn btn-xs default edit-file" href="#file-modal" data-toggle="modal" data-cat='3' data-action="add">Add</a>
                                                @endif
                                            </div>
                                        @endif
                                        @if ($company->activeCompanyDoc('3'))
                                            <div class="col-md-5 hidden-sm hidden-xs">&nbsp;</div>
                                            <div class="col-md-7 hidden-sm hidden-xs"><b>Category:</b> {{ $company->activeCompanyDoc('3')->ref_type }}</div>
                                        @endif
                                    </div>


                                    {{-- Subcontractor Statement --}}
                                    <div class="row" style="background:#fafafa; margin-bottom: 3px; line-height: 2">
                                        @if ($company->activeCompanyDoc('4'))
                                            <div class="col-md-3">
                                                <a href="{{ $company->activeCompanyDoc('4')->attachment_url }}" style="color:#333; display: block">
                                                    <i class="fa fa-file-pdf-o" style="font-size: 20px; min-width: 35px"></i><b>Subcontractors Statement</b></a>
                                                @if (($company->activeCompanyDoc('4')->status == 2)) <span class="label label-warning"
                                                                                                           style="margin-left:30px">Pending approval</span> @endif
                                                @if (($company->activeCompanyDoc('4')->status == 3)) <span class="label label-danger" style="margin-left:30px">Not approved</span> @endif
                                            </div>
                                            <div class="col-md-8">{!! ($company->activeCompanyDoc('4')) ? format_expiry_field($company->activeCompanyDoc('4')->expiry) : '-' !!}</div>
                                            <div class="col-md-1">
                                                @if (Auth::user()->allowed2('edit.company.doc.ics', $company->activeCompanyDoc('4')))
                                                    <a class="btn btn-xs default edit-file" href="#file-modal" data-toggle="modal" data-cat='4' data-action="edit"
                                                       data-doc_id="{{ $company->activeCompanyDoc('4')->id }}"
                                                       data-expiry="{{ ($company->activeCompanyDoc('4')->expiry) ? $company->activeCompanyDoc('4')->expiry->format('d/m/Y') : '' }}"
                                                       data-notes="{{ $company->activeCompanyDoc('4')->notes }}"
                                                       data-doc_name="{{ $company->activeCompanyDoc('4')->attachment }}"
                                                       data-doc_url="{{ $company->activeCompanyDoc('4')->attachment_url }}"
                                                       data-doc_status="{{ $company->activeCompanyDoc('4')->status }}">Edit</a>
                                                @endif
                                            </div>
                                        @else
                                            <div class="col-md-3"><i class="fa" style="font-size: 20px; min-width: 35px"></i><b>Subcontractors Statement</b></div>
                                            <div class="col-md-8">@if ($company->category == 'On Site Trade')<span class="font-red">Required</span> @endif{{--<a href="/company/doc/create/subcontractorstatement/{{ $company->id  }}/next" target="_blank"><i
                                                                    class="fa fa-download" style="padding-left: 20px"></i> Pre-filled form</a>--}}</div>
                                            <div class="col-md-1">
                                                @if (Auth::user()->allowed2('add.company.doc.ics'))
                                                    <a class="btn btn-xs default edit-file" href="#file-modal" data-toggle="modal" data-cat='4' data-action="add">Add</a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Trade Contract --}}
                                    <div class="row" style="background:#fafafa; margin-bottom: 3px; line-height: 2">
                                        @if ($company->activeCompanyDoc('5'))
                                            <div class="col-md-3">
                                                <a href="{{ $company->activeCompanyDoc('5')->attachment_url }}" style="color:#333; display: block">
                                                    <i class="fa fa-file-pdf-o" style="font-size: 20px; min-width: 35px"></i><b>Period Trade Contract</b></a>
                                                @if (($company->activeCompanyDoc('5')->status == 2)) <span class="label label-warning"
                                                                                                           style="margin-left:30px">Pending approval</span> @endif
                                                @if (($company->activeCompanyDoc('5')->status == 3)) <span class="label label-danger" style="margin-left:30px">Not approved</span> @endif
                                            </div>
                                            <div class="col-md-8">{!! ($company->activeCompanyDoc('5')) ? format_expiry_field($company->activeCompanyDoc('5')->expiry) : '-' !!}</div>
                                            <div class="col-md-1">
                                                @if (Auth::user()->allowed2('edit.company.doc.ics', $company->activeCompanyDoc('5')))
                                                    <a class="btn btn-xs default edit-file" href="#file-modal" data-toggle="modal" data-cat='5' data-action="edit"
                                                       data-doc_id="{{ $company->activeCompanyDoc('5')->id }}"
                                                       data-expiry="{{ ($company->activeCompanyDoc('5')->expiry) ? $company->activeCompanyDoc('5')->expiry->format('d/m/Y') : '' }}"
                                                       data-notes="{{ $company->activeCompanyDoc('5')->notes }}"
                                                       data-doc_name="{{ $company->activeCompanyDoc('5')->attachment }}"
                                                       data-doc_url="{{ $company->activeCompanyDoc('5')->attachment_url }}"
                                                       data-doc_status="{{ $company->activeCompanyDoc('5')->status }}">Edit</a>
                                                @endif
                                            </div>
                                        @else
                                            <div class="col-md-3"><i class="fa" style="font-size: 20px; min-width: 35px"></i><b>Period Trade Contract</b></div>
                                            <div class="col-md-8">@if ($company->category == 'On Site Trade')<span class="font-red">Required</span> @endif {{--<a href="/company/doc/create/tradecontract/{{ $company->id  }}/current" target="_blank"><i class="fa fa-download"
                                                                                                                                                                         style="padding-left: 20px"></i>
                                                            Pre-filled form</a>--}}</div>
                                            <div class="col-md-1">
                                                @if (Auth::user()->allowed2('add.company.doc.ics'))
                                                    <a class="btn btn-xs default edit-file" href="#file-modal" data-toggle="modal" data-cat='5' data-action="add">Add</a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <!--<div>Downloadable Pre-filled Forms:  <a href="/company/doc/create/subcontractorstatement/{{ $company->id  }}/current" target="_blank">Subcontractors Statement</a> &nbsp; -  &nbsp; <a href="/company/doc/create/tradecontract/{{ $company->id  }}/current" target="_blank">Period Trade Contract</a></div>-->
                                </div> {{-- end ic_current --}}

                                {{-- Expired Insurance + Contracts --}}
                                <div class="row" id="ic_expired" style="display:none">
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
                            @endif {{-- end Insurance + Contracts--}}

                            {{-- WHS --}}
                            @if(Auth::user()->allowed2('show.company.doc.lic', $company))
                                {{-- Licences --}}
                                <h3 class="font-green form-section">WHS Compliance
                                    <button class="btn btn-xs default pull-right" id="but_show_whs_expired">Show Expired</button>
                                </h3>
                                @if (Auth::user()->isCompany($company->reportsTo()->id) && !Auth::user()->isCompany($company->id))
                                    <div class="row" style="line-height: 2">
                                        <div class="col-md-3"><b>Requires a Contractor Licence</b></div>
                                        <div class="col-md-9">
                                            {!! ($company->licence_required) ? 'Yes' : 'No' !!}
                                            {!! ($company->licence_required != $company->requiresContractorsLicence()) ? ' &nbsp; <span class="font-red">OVERRIDDEN DEFAULT</span>' : '' !!}</div>
                                    </div>
                                @endif
                                <div id="whs_current">
                                    {{-- Contractor Licence --}}
                                    <div class="row" style="background:#fafafa; margin-bottom: 3px; line-height: 2">
                                        @if ($company->activeCompanyDoc('7'))
                                            <div class="col-md-3">
                                                <a href="{{ $company->activeCompanyDoc('7')->attachment_url }}" style="color:#333; display: block">
                                                    <i class="fa fa-file-pdf-o" style="font-size: 20px; min-width: 35px"></i><b>Contractors Licence</b></a>
                                                @if (($company->activeCompanyDoc('7')->status == 2)) <span class="label label-warning" style="margin-left:30px">Pending approval</span> @endif
                                                @if (($company->activeCompanyDoc('7')->status == 3)) <span class="label label-danger" style="margin-left:30px">Not approved</span> @endif
                                            </div>
                                            <div class="col-md-2">{!! format_expiry_field($company->activeCompanyDoc('7')->expiry) !!}</div>
                                            <div class="col-md-2"><b>Lic:</b> {{ $company->activeCompanyDoc('7')->ref_no }}</div>
                                            <div class="col-md-4"><b>Class:</b> {!! $company->contractorLicenceSBC() !!}</div>
                                            <div class="col-md-1">
                                                @if (Auth::user()->allowed2('edit.company.doc.lic', $company->activeCompanyDoc('7')))
                                                    <a class="btn btn-xs default edit-file" href="#file-modal" data-toggle="modal" data-cat='7' data-action="edit"
                                                       data-doc_id="{{ $company->activeCompanyDoc('7')->id }}"
                                                       data-ref_no="{{ $company->activeCompanyDoc('7')->ref_no }}"
                                                       data-ref_name="{{ $company->activeCompanyDoc('7')->ref_name }}"
                                                       data-notes="{{ $company->activeCompanyDoc('7')->notes }}"
                                                       data-expiry="{{ ($company->activeCompanyDoc('7')->expiry) ? $company->activeCompanyDoc('7')->expiry->format('d/m/Y') : '' }}"
                                                       data-doc_name="{{ $company->activeCompanyDoc('7')->attachment }}"
                                                       data-doc_url="{{ $company->activeCompanyDoc('7')->attachment_url }}"
                                                       data-doc_status="{{ $company->activeCompanyDoc('7')->status }}">Edit</a>
                                                @endif
                                            </div>
                                        @else
                                            <div class="col-md-3"><i class="fa" style="font-size: 20px; min-width: 35px"></i><b>Contractors Licence</b></div>
                                            <div class="col-md-8">{!! ($company->licence_required) ? '<span class="font-red">Required</span>' : '-' !!}</div>
                                            <div class="col-md-1">
                                                @if (Auth::user()->allowed2('add.company.doc.lic'))
                                                    <a class="btn btn-xs default edit-file" href="#file-modal" data-toggle="modal" data-cat='7' data-action="add">Add</a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Asbestos Licence --}}
                                    @if ($company->activeCompanyDoc('8'))
                                        <div class="row" style="background:#fafafa; margin-bottom: 3px; line-height: 2">
                                            <div class="col-md-3">
                                                <a href="{{ $company->activeCompanyDoc('8')->attachment_url }}" style="color:#333; display: block">
                                                    <i class="fa fa-file-pdf-o" style="font-size: 20px; min-width: 35px"></i><b>{{ $company->activeCompanyDoc('8')->name }}</b></a>
                                                @if (($company->activeCompanyDoc('8')->status == 2)) <span class="label label-warning" style="margin-left:30px">Pending approval</span> @endif
                                                @if (($company->activeCompanyDoc('8')->status == 3)) <span class="label label-danger" style="margin-left:30px">Not approved</span> @endif
                                            </div>
                                            <div class="col-md-2">{!! format_expiry_field($company->activeCompanyDoc('8')->expiry) !!}</div>
                                            <div class="col-md-6"><b>Class:</b> {!! $company->activeCompanyDoc('8')->ref_type !!}</div>
                                            <div class="col-md-1">
                                                @if (Auth::user()->allowed2('edit.company.doc.lic', $company->activeCompanyDoc('8')))
                                                    <a class="btn btn-xs default edit-file" href="#file-modal" data-toggle="modal" data-cat='8' data-action="edit"
                                                       data-doc_id="{{ $company->activeCompanyDoc('8')->id }}"
                                                       data-extra_lic_type='8'
                                                       data-extra_lic_class="{{ $company->activeCompanyDoc('8')->ref_type }}"
                                                       data-expiry="{{ ($company->activeCompanyDoc('8')->expiry) ? $company->activeCompanyDoc('8')->expiry->format('d/m/Y') : '' }}"
                                                       data-notes="{{ $company->activeCompanyDoc('8')->notes }}"
                                                       data-doc_name="{{ $company->activeCompanyDoc('8')->attachment }}"
                                                       data-doc_url="{{ $company->activeCompanyDoc('8')->attachment_url }}"
                                                       data-doc_status="{{ $company->activeCompanyDoc('8')->status }}">Edit</a>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Additional Licence --}}
                                    @foreach ($company->companyDocs('9', '1') as $extra)
                                        <div class="row" style="background:#fafafa; margin-bottom: 3px; line-height: 2">
                                            <div class="col-md-3">
                                                <a href="{{ $extra->attachment_url }}" style="color:#333; display: block">
                                                    <i class="fa fa-file-pdf-o" style="font-size: 20px; min-width: 35px"></i><b>{{ $extra->name }}</b></a>
                                                @if (($extra->status == 2)) <span class="label label-warning" style="margin-left:30px">Pending approval</span> @endif
                                                @if (($extra->status == 3)) <span class="label label-danger" style="margin-left:30px">Not approved</span> @endif
                                            </div>
                                            <div class="col-md-8">{!! format_expiry_field($extra->expiry) !!}</div>
                                            <div class="col-md-1">
                                                @if (Auth::user()->allowed2('edit.company.doc.lic', $extra))
                                                    <a class="btn btn-xs default edit-file" href="#file-modal" data-toggle="modal" data-cat='9' data-action="edit"
                                                       data-doc_id="{{ $extra->id }}"
                                                       data-extra_lic_type='9'
                                                       data-extra_lic_name="{{ $extra->name }}"
                                                       data-expiry="{{ ($extra->expiry) ? $extra->expiry->format('d/m/Y') : '' }}"
                                                       data-notes="{{ $extra->notes }}"
                                                       data-doc_name="{{ $extra->attachment }}"
                                                       data-doc_url="{{ $extra->attachment_url }}"
                                                       data-doc_status="{{ $extra->status }}">Edit</a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach

                                    @if(Auth::user()->allowed2('show.company.doc.whs', $company))
                                        <div class="row" style="background:#fafafa; margin-bottom: 3px; line-height: 2">
                                            @if ($company->activeCompanyDoc('6'))
                                                <div class="col-md-3">
                                                    <a href="{{ $company->activeCompanyDoc('6')->attachment_url }}" style="color:#333; display: block">
                                                        <i class="fa fa-file-pdf-o" style="font-size: 20px; min-width: 35px"></i><b>Test & Tagging</b></a>
                                                    @if (($company->activeCompanyDoc('6')->status == 2)) <span class="label label-warning" style="margin-left:30px">Pending approval</span> @endif
                                                    @if (($company->activeCompanyDoc('6')->status == 3)) <span class="label label-danger" style="margin-left:30px">Not approved</span> @endif
                                                </div>
                                                <div class="col-md-8">{!! format_expiry_field($company->activeCompanyDoc('6')->expiry) !!}</div>
                                                <div class="col-md-1">
                                                    @if (Auth::user()->allowed2('edit.company.doc.whs', $company->activeCompanyDoc('6')))
                                                        <a class="btn btn-xs default edit-file" href="#file-modal" data-toggle="modal" data-cat='6' data-action="edit"
                                                           data-doc_id="{{ $company->activeCompanyDoc('6')->id }}"
                                                           data-ref_no="{{ $company->activeCompanyDoc('6')->ref_no }}"
                                                           data-ref_name="{{ $company->activeCompanyDoc('6')->ref_name }}"
                                                           data-notes="{{ $company->activeCompanyDoc('6')->notes }}"
                                                           data-expiry="{{ ($company->activeCompanyDoc('6')->expiry) ? $company->activeCompanyDoc('6')->expiry->format('d/m/Y') : '' }}"
                                                           data-doc_name="{{ $company->activeCompanyDoc('6')->attachment }}"
                                                           data-doc_url="{{ $company->activeCompanyDoc('6')->attachment_url }}"
                                                           data-doc_status="{{ $company->activeCompanyDoc('6')->status }}">Edit</a>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="col-md-3"><i class="fa" style="font-size: 20px; min-width: 35px"></i><b>Test & Tagging</b></div>
                                                <div class="col-md-8">-</div>
                                                <div class="col-md-1">
                                                    @if (Auth::user()->allowed2('add.company.doc.whs'))
                                                        <a class="btn btn-xs default edit-file" href="#file-modal" data-toggle="modal" data-cat='6' data-action="add">Add</a>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="col-md-12" style="margin: 0px; padding: 0px">
                                        <div class="col-md-8" style="margin: 0px;">
                                        </div>
                                        <div class="col-md-4"><i class="fa" style="font-size: 20px; min-width: 35px"></i>
                                            @if (Auth::user()->allowed2('add.company.doc.lic'))
                                                <a class="btn btn-xs default edit-file" href="#file-modal" data-toggle="modal" data-cat='89' data-action="add">Add additional Licence other then
                                                    Contractors Licence</a>
                                            @endif</div>
                                    </div>
                                </div>


                                {{-- Expired Licences --}}
                                <div class="row" id="whs_expired" style="display:none">
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
                            @endif  {{-- End Licences --}}


                            @if ($company->status != 2 && !(Auth::user()->isCompany($company->id) && $company->signup_step))
                                {{-- Trade + Planner Details --}}
                                @if(Auth::user()->company->subscription > 1 && $company->id != Auth::user()->company->id)
                                    <h3 class="font-green form-section">Trade @if(Auth::user()->company->addon('planner'))& Planner @endif Details (Construction)
                                        @if((Auth::user()->hasAnyPermission2('add.trade|edit.trade') && $company->reportsTo()->id == Auth::user()->company_id))
                                            <a href="/company/{{ $company->id }}/edit/trade" class="btn btn-xs default pull-right">Edit</a>
                                        @endif
                                    </h3>
                                    <div class="row">
                                        <div class="col-md-6" style="line-height: 2">
                                            <div class="row" style="margin: 0px">
                                                <div class="col-xs-4" style="padding-left: 0px"><b>Trade(s):</b></div>
                                                <div class="col-xs-8">{{ $company->tradesSkilledInSBC() }} &nbsp;</div>
                                            </div>
                                            @if (Auth::user()->isCC())
                                                <div class="row" style="margin: 0px">
                                                    <div class="col-xs-4" style="padding-left: 0px"><b>Max Jobs:</b></div>
                                                    <div class="col-xs-8">{{ $company->maxjobs }}</div>
                                                </div>
                                            @endif
                                        </div>
                                        @if (Auth::user()->isCC())
                                            <div class="col-md-6" style="line-height: 2">
                                                @if(Auth::user()->isCC())
                                                    <div class="col-xs-4" style="padding-left: 0px"><b>Transient:</b></div>
                                                    <div class="col-xs-8">@if($company->transient) Supervised by {{ $company->supervisedBySBC() }} @else No @endif</div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif


                                {{-- Staff --}}
                                <h3 class="form-section font-green">Staff</h3>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-bordered table-hover order-column" id="table_staff">
                                            <thead>
                                            <tr class="mytable-header">
                                                <th width="5%"> #</th>
                                                <th> Name</th>
                                                <th> Phone</th>
                                                <th> Email</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                                {{-- Notes --}}
                                @if($company->notes && Auth::user()->company_id == $company->parent_company)
                                    <h3 class="font-green form-section">Notes</h3>
                                    <div class="row">
                                        <div class="col-md-12">{{ $company->notes }}</div>
                                    </div>
                                @endif
                            @endif

                            @if (Auth::user()->isCompany($company->id) && $company->signup_step)
                                <br>
                                <div class="form-actions right">
                                    <a href="/company/{{ $company->id }}/signup/6" class="btn green pull-right">Complete Signup</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div>
        <div class="pull-right" style="font-size: 12px; font-weight: 200; padding: 10px 10px 0 0">
            {!! $company->displayUpdatedBy() !!}
        </div>
    </div>

    {{-- Edit File Modal --}}
    <div class="modal fade" id="file-modal" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title" id="modal-title"></h4>
                </div>
                <div class="modal-body form">
                    {!! Form::model('company_doc', ['action' => ['Company\CompanyDocController@profile'], 'files' => true, 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'file-form']) !!}
                    {{-- @include('form-error') --}}
                    {!! Form::hidden('for_company_id', $company->id, ['class' => 'form-control']) !!}
                    {!! Form::hidden('company_id', $company->reportsTo()->id, ['class' => 'form-control']) !!}
                    {!! Form::hidden('category_id', null, ['class' => 'form-control', 'id' => 'category_id']) !!}
                    {!! Form::hidden('doc_id', null, ['class' => 'form-control', 'id' => 'doc_id']) !!}
                    {!! Form::hidden('type', null, ['class' => 'form-control', 'id' => 'type']) !!}
                    {!! Form::hidden('doc_name', null, ['class' => 'form-control', 'id' => 'doc_name']) !!}
                    {!! Form::hidden('doc_url', null, ['class' => 'form-control', 'id' => 'doc_url']) !!}
                    {!! Form::hidden('doc_status', null, ['class' => 'form-control', 'id' => 'doc_status']) !!}
                    {!! Form::hidden('name', '', ['class' => 'form-control', 'id' => 'name']) !!}
                    {!! Form::hidden('action', '', ['class' => 'form-control', 'id' => 'action']) !!}
                    <div class="row" style="margin:0px">
                        <div class="col-md-12">
                            <div class="form-body">
                                {{-- Document reference fields --}}
                                <div class="form-group {!! fieldHasError('ref_no', $errors) !!}" id="ref_no_field">
                                    {!! Form::label('ref_no', 'Policy No.', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        {!! Form::text('ref_no', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('ref_no', $errors) !!}
                                    </div>
                                </div>
                                <div class="form-group {!! fieldHasError('ref_name', $errors) !!}" id="ref_name_field">
                                    {!! Form::label('ref_name', 'Insurer', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        {!! Form::text('ref_name', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('ref_name', $errors) !!}
                                    </div>
                                </div>
                                <div class="form-group {!! fieldHasError('ref_type', $errors) !!}" id="ref_type_field">
                                    {!! Form::label('ref_type', 'Category', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        {!! Form::select('ref_type', $company->workersCompCategorySelect('prompt'), null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('ref_type', $errors) !!}
                                    </div>
                                </div>
                                {{-- Contractor licence --}}
                                <div class="form-group {!! fieldHasError('lic_no', $errors) !!}" id="lic_no_field">
                                    @if ($company->business_entity == 'Company' || $company->business_entity == 'Partnership' || $company->business_entity == 'Trading Trust')
                                        <div class="note note-warning">Licence required to be in the name of company</div>
                                    @endif
                                    {!! Form::label('lic_no', 'Licence No.', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        {!! Form::text('lic_no', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('lic_no', $errors) !!}
                                    </div>
                                </div>
                                <div class="form-group {!! fieldHasError('lic_type', $errors) !!}" id="lic_type_field">
                                    {!! Form::label('lic_type', 'Class(s)', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        <select id="lic_type" name="lic_type[]" class="form-control select2" width="100%" multiple>
                                            {!! $company->contractorLicenceOptions() !!}
                                        </select>
                                        {!! fieldErrorMessage('lic_type', $errors) !!}
                                    </div>
                                </div>
                                {{-- Additional licences --}}
                                <div class="form-group {!! fieldHasError('extra_lic_type', $errors) !!}" id="extra_lic_field">
                                    {!! Form::label('extra_lic_type', 'Type', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        {!! Form::select('extra_lic_type', ['' => 'Select type', '8' => 'Asbestos Removal', '9' => 'Other'], null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('extra_lic_type', $errors) !!}
                                    </div>
                                </div>
                                <div class="form-group {!! fieldHasError('extra_lic_class', $errors) !!}" id="extra_lic_class_field">
                                    {!! Form::label('extra_lic_class', 'Class', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        {!! Form::select('extra_lic_class', ['' => 'Select class', 'A' => 'Class A', 'B' => 'Class B'], null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('extra_lic_class', $errors) !!}
                                    </div>
                                </div>
                                <div class="form-group {!! fieldHasError('extra_lic_name', $errors) !!}" id="extra_lic_name_field">
                                    {!! Form::label('extra_lic_name', 'Licence Name', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        {!! Form::text('extra_lic_name', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('extra_lic_name', $errors) !!}
                                    </div>
                                </div>
                                {{-- Expiry --}}
                                <div class="form-group {!! fieldHasError('expiry', $errors) !!}" id="expiry_field">
                                    {!! Form::label('expiry', 'Expiry', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-5">
                                        <div class="input-group date date-picker" data-date-orientation="top right" data-date-format="dd/mm/yyyy"> <!-- data-date-start-date="+0d">-->
                                            {!! Form::text('expiry', null, ['class' => 'form-control form-control-inline', 'style' => 'background:#FFF', 'readonly']) !!}
                                            <span class="input-group-btn">
                                                <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                        {!! fieldErrorMessage('expiry', $errors) !!}
                                    </div>
                                </div>
                                {{-- File attachment --}}
                                <div class="form-group" id="file_div">
                                    {!! Form::label('document', 'Document', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7" style="padding-top: 7px;">
                                        <a href="#" target="_blank" id="doc_link"></a>
                                        @if($company->id == Auth::user()->company_id)
                                            <a href="#" id="del_cross"><i class="fa fa-times font-red" style="font-size: 15px; padding-left: 20px"></i></a>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group {!! fieldHasError('singlefile', $errors) !!}" id="file_field">
                                    {!! Form::label('singlefile', 'Document', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        <input id="singlefile" name="singlefile" type="file" class="file-loading">
                                        {!! fieldErrorMessage('singlefile', $errors) !!}
                                    </div>
                                </div>
                                <div class="form-group {!! fieldHasError('notes', $errors) !!}" id="notes_field">
                                    {!! Form::label('notes', 'Notes', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        {!! Form::textarea('notes', null, ['rows' => '3', 'class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('notes', $errors) !!}
                                    </div>
                                </div>

                                {{-- Messages --}}
                                <div id="pending_div">
                                    @if($company->id == Auth::user()->company_id)
                                        This document is <span class="label label-warning">Pending approval</span> and can be <span style="text-decoration: underline;">deleted</span> or modified if
                                        required.
                                    @endif
                                    @if (Auth::user()->allowed2('sig.company', $company))
                                        This document is <span class="label label-warning">Pending approval</span> and can be <span style="text-decoration: underline;">rejected</span> or modified if
                                        required.
                                    @endif
                                </div>
                                <div id="rejected_div">
                                    @if($company->id == Auth::user()->company_id)
                                        This document was <span class="label label-danger">Not approved</span> and can be <span style="text-decoration: underline;">deleted</span> or modified if
                                        required.
                                    @endif
                                    @if (Auth::user()->allowed2('sig.company', $company))
                                        This document was <span class="label label-danger">Not approved</span> but can still be <span style="text-decoration: underline;">accepted</span> or modified if
                                        required.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                    @if($company->id == Auth::user()->company_id)
                        <a href="" class="btn dark" id="del_doc">Delete Document</a>
                    @endif
                    @if (Auth::user()->allowed2('sig.company', $company))
                        <button class="btn dark" id="reject_doc" name="reject_doc" value="reject">Reject Document</button>
                        <button class="btn dark" id="archive_doc" name="archive_doc" value="archive">Archive</button>
                        <button type="submit" class="btn green">Approve and Save</button>
                    @else
                        <button type="submit" class="btn green">Save</button>
                    @endif
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
@stop

@section('page-level-plugins-head')
    <link href="/css/libs/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" tytype="text/css"/>
    @stop

    @section('page-level-styles-head')
            <!--<link href="/assets/pages/css/profile-2.min.css" rel="stylesheet" type="text/css"/>-->
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/js/libs/fileinput.min.js"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        /* Bootstrap Fileinput */
        $("#singlefile").fileinput({
            showUpload: false,
            allowedFileExtensions: ["pdf"],
            browseClass: "btn blue",
            browseLabel: "Browse",
            browseIcon: "<i class=\"fa fa-folder-open\"></i> ",
            //removeClass: "btn btn-danger",
            removeLabel: "",
            removeIcon: "<i class=\"fa fa-trash\"></i> ",
            uploadClass: "btn btn-info",
        });

        /* Select2 */
        $("#lic_type").select2({
            placeholder: "Select one or more",
            width: '100%',
        });
    });

    $('#update_company').click(function (e) {
        $('#show_company').hide();
        $('#edit_company').show();
    });
    $('#cancel_company').click(function (e) {
        e.preventDefault();
        $('#show_company').show();
        $('#edit_company').hide();
    });
    $('#save_company').click(function (e) {
        //e.preventDefault();
        //alert('saving');
        //$('#show_company').show();
        //$('#edit_company').hide();
    });

    $('.edit-file').click(function (e) {
        // Reset Form Errors
        var $fileform = document.getElementById('file-form');
        $(".has-error").removeClass('has-error');
        $(".help-block").text('');

        display_fields($(this).data('cat'));

        var cat_names = ['0', 'Public Liability', "Worker's Compensation", 'Sickness & Accident', 'Subcontractors Statement',
            'Period Trade Contract', 'Test & Tagging', 'Contractor Licence', 'Asbestos Licence', 'Additional Licence'];

        $(".modal-body #name").val(cat_names[$(this).data('cat')]);
        if ($(this).data('cat') == '89')
            $(".modal-body #name").val(cat_names[9]);
        $(".modal-body #expiry").val($(this).data('expiry'));
        $(".modal-body #action").val($(this).data('action'));
        $(".modal-body #category_id").val($(this).data('cat'));
        $(".modal-body #doc_status").val($(this).data('doc_status'));
        $(".modal-body #notes").val($(this).data('notes'));
        $(".modal-title").html('<b>' + $("#name").val() + '</b>');

        if ($(this).data('action') == 'add')
            $("#doc_id").val('');


        // Set Doc_id
        if ($(this).data('action') == 'edit') {
            $("#doc_id").val($(this).data('doc_id'));
            $("#doc_name").val($(this).data('doc_name'));
            $("#doc_url").val($(this).data('doc_url'));
            $("#notes_field").val($(this).data('doc_notes'));
            $("#del_doc").show();
            $("#del_doc").attr('href', '/company/doc/profile-destroy/' + $(this).data('doc_id'));
            $("#file_field").hide();
            $("#file_div").show();
            $(".modal-body #doc_link").html($(this).data('doc_name'));
            $(".modal-body #doc_link").attr('href', $(this).data('doc_url'));
            if ($(this).data('doc_status') == 1) {
                $("#rejected_div").hide();
                $("#reject_doc").hide();
                $("#pending_div").hide();
                $("#archive_doc").show();
            }
            if ($(this).data('doc_status') == 2) {
                $("#reject_doc").show();
                $("#pending_div").show();
                $("#rejected_div").hide();
                $("#archive_doc").hide();
            }
            if ($(this).data('doc_status') == 3) {
                $("#rejected_div").show();
                $("#reject_doc").hide();
                $("#pending_div").hide();
                $("#archive_doc").hide();
            }
        } else {
            $("#file_field").show();
            $("#pending_div").hide();
            $("#rejected_div").hide();
            $("#del_doc").hide();
            $("#reject_doc").hide();
            $("#file_div").hide();
        }

        if ($(this).data('action') == 'del')
            $("#del_id").val($(this).data('doc_id'));

        if ($(this).data('cat') == '1') {
            $("#ref_no").val($(this).data('ref_no'));
            $("#ref_name").val($(this).data('ref_name'));
        }
        if ($(this).data('cat') == '2') {
            $("#ref_no").val($(this).data('ref_no'));
            $("#ref_name").val($(this).data('ref_name'));
            $('#ref_type').val($(this).data('ref_type')).prop('selected', true);
        }
        if ($(this).data('cat') == '3') {
            $("#ref_no").val($(this).data('ref_no'));
            $("#ref_name").val($(this).data('ref_name'));
            $('#ref_type').val($(this).data('ref_type')).prop('selected', true);
        }
        if ($(this).data('cat') == '7') {
            $("#lic_no").val($(this).data('ref_no'));
        }
        if ($(this).data('cat') < 6) { // Doc type ICS
            $("#type").val('ics');
        }
        if ($(this).data('cat') == 6) { // Doc type WHS
            $("#type").val('whs');
        }
        if (($(this).data('cat') > 6 && $(this).data('cat') < 10) || $(this).data('cat') == 89) { // Doc type LIC
            $("#type").val('lic');
        }
        if ($(this).data('cat') < 8) {
            $("#extra_lic_type").val('');
        }
        if ($(this).data('cat') == '8') {
            $('#extra_lic_type').val($(this).data('extra_lic_type')).prop('selected', true);
            $("#extra_lic_class").val($(this).data('extra_lic_class'));
        }
        if ($(this).data('cat') == '9') {
            $('#extra_lic_type').val($(this).data('extra_lic_type')).prop('selected', true);
            $("#extra_lic_name").val($(this).data('extra_lic_name'));
        }
    });

    $('#del_cross').click(function (e) {
        $("#file_field").show();
        $("#file_div").hide();
    });

    // Expired Licence button
    $('#but_show_whs_expired').click(function (e) {
        if ($("#but_show_whs_expired").html() == 'Show Expired')
            $("#but_show_whs_expired").html("Show Current");
        else
            $("#but_show_whs_expired").html("Show Expired");
        $('#whs_expired').toggle();
        $('#whs_current').toggle();
    });

    // Expired Insurance + Contracts button
    $('#but_show_ic_expired').click(function (e) {
        if ($("#but_show_ic_expired").html() == 'Show Expired')
            $("#but_show_ic_expired").html("Show Current");
        else
            $("#but_show_ic_expired").html("Show Expired");
        $('#ic_expired').toggle();
        $('#ic_current').toggle();
    });

    // ExpiredTest & Tagging button
    $('#but_show_ett_expired').click(function (e) {
        if ($("#but_show_ett_expired").html() == 'Show Expired')
            $("#but_show_ett_expired").html("Show Current");
        else
            $("#but_show_ett_expired").html("Show Expired");
        $('#ett_expired').toggle();
        $('#ett_current').toggle();
    });

    // Toggle Additional Licence Name
    $('#extra_lic_field').change(function (e) {
        $('#extra_lic_name_field').hide();
        $("#extra_lic_class_field").hide();
        if ($('#extra_lic_type').val() == '8') {
            $(".modal-body #category_id").val(8);
            $("#extra_lic_class_field").show();
        }
        if ($('#extra_lic_type').val() == '9') {
            $(".modal-body #category_id").val(9);
            $("#extra_lic_name_field").show();
        }
    });


    function display_fields(cat) {
        //alert(cat);
        $('#ref_no_field').hide();
        $("#ref_name_field").hide();
        $("#ref_type_field").hide();
        $("#lic_no_field").hide();
        $("#lic_type_field").hide();
        $("#extra_lic_field").hide();
        $("#extra_lic_class_field").hide();
        $("#extra_lic_name_field").hide();

        if (cat == '1') { // Public Liability
            $('#ref_no_field').show();
            $("#ref_name_field").show();
        }
        if (cat == '2' || cat == '3') { // Worker's Compensation, Sickness & Accident
            $('#ref_no_field').show();
            $("#ref_name_field").show();
            $("#ref_type_field").show();
        }
        if (cat == '7') { // Contractor Licence
            $("#lic_no_field").show();
            $("#lic_type_field").show();
        }
        if (cat == '8') { // Asbestos Licence
            $("#extra_lic_field").show();
            $("#extra_lic_class_field").show();
        }
        if (cat == '9') { // Additional Licence
            $("#extra_lic_field").show();
            $("#extra_lic_name_field").show();
        }
        if (cat == '89') { // New Additional Licence
            $("#extra_lic_field").show();
        }
    }

    var table_staff = $('#table_staff').DataTable({
        processing: true,
        serverSide: true,
        //bFilter: false,
        //bLengthChange: false,
        ajax: {
            'url': '/company/dt/staff',
            'type': 'GET',
            'data': function (d) {
                d.company_id = {{ $company->id }};
            }
        },
        columns: [
            {data: 'action', name: 'action', orderable: false, searchable: false},
            {data: 'full_name', name: 'full_name'},
            {data: 'phone', name: 'phone', orderable: false},
            {data: 'email', name: 'email', orderable: false},
        ],
        order: [
            [1, "asc"]
        ]
    });

    var table_whs_expired = $('#table_whs_expired').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: {
            'url': '/company/doc/dt/expired',
            'type': 'GET',
            'data': function (d) {
                d.for_company_id = {{ $company->id }};
                d.type = 'whs';
            }
        },
        columns: [
            {data: 'name', name: 'd.name'},
            {data: 'nicedate', name: 'd.expiry'},
        ],
        order: [
            [0, "asc"]
        ]
    });

    var table_ic_expired = $('#table_ic_expired').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: {
            'url': '/company/doc/dt/expired',
            'type': 'GET',
            'data': function (d) {
                d.for_company_id = {{ $company->id }};
                d.type = 'insurance_contract';
            }
        },
        columns: [
            {data: 'name', name: 'd.name'},
            {data: 'nicedate', name: 'd.expiry'},
        ],
        order: [
            [0, "asc"]
        ]
    });

            @if (count($errors) > 0)
    var errors = {!! $errors !!};
    if (errors.FORM == 'company') {
        $('#show_company').hide();
        $('#edit_company').show();
    }

    console.log(errors)
    @endif
    {{--
    // Show Modal on errors
    @if (count($errors) > 0)
      display_fields({{ old('category_id') }})
    $('#file-modal').modal('show');
    $(".modal-title").html("<b>{{ old('name') }}</b>");
    $("#pending_div").hide();
    $("#rejected_div").hide();
    @if (old('action') == 'edit')
    @if (old('status') == '2')
      $("#pending_div").show();
    @elseif (old('status') == '3')
      $("#rejected_div").show();
    @endif
    $(".modal-body #doc_link").html("{{ old('doc_name') }}");
    $(".modal-body #doc_link").attr('href', "{{ old('doc_url') }}");
    $("#file_field").hide();
    $("#del_doc").show();
    @else
    $("#file_div").hide();
    $("#pending_div").hide();
    $("#rejected_div").hide();
    $("#reject_doc").hide();
    $("#del_doc").hide();
    @endif
    @endif
    --}}
</script>
@stop