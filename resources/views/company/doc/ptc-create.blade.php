@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->company->subscription > 1 && Auth::user()->hasAnyPermissionType('company'))
            <li><a href="/company">Companies</a><i class="fa fa-circle"></i></li>
        @endif
        <li><a href="/company/{{ $company->id }}/doc">Documents</a><i class="fa fa-circle"></i></li>
        <li><span>Upload</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">

        @include('company/_header')

        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-dark bold uppercase"> Period Trade Contract </span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model('companydoc', ['action' => ['Company\CompanyDocController@store', $company->id], 'class' => 'horizontal-form', 'files' => true]) !!}
                        @include('form-error')

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    {{-- Policy --}}
                                    <div class="form-group {!! fieldHasError('ref_no', $errors) !!}" style="display: none" id="fields_policy">
                                        {!! Form::label('ref_no', 'Policy No', ['class' => 'control-label']) !!}
                                        {!! Form::text('ref_no', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('ref_no', $errors) !!}
                                    </div>

                                    {{-- Category --}}
                                    <div class="form-group {!! fieldHasError('ref_type', $errors) !!}" style="display: none" id="fields_category">
                                        {!! Form::label('ref_type', 'Category', ['class' => 'control-label']) !!}
                                        {!! Form::select('ref_type', $company->workersCompCategorySelect('prompt'), null, ['class' => 'form-control bs-select']) !!}
                                        {!! fieldErrorMessage('ref_type', $errors) !!}
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-md-12">
                                    <h5 style="margin: -20px 0 0 0">SCHEDULE</h5><br>
                                </div>
                            </div>

                            {{-- Schedule 1. Date --}}
                            <table class="table" style="padding: 0px; margin: 0px">
                                <tr>
                                    <td width="5%" style="margin:5px 0 0 0; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">1.</h5></td>
                                    <td class="pad5" style="border: 0px">
                                        <h4 style="margin: 0px">Date</h4>
                                        <hr style="margin: 5px 0px 5px 0px">
                                        AN AGREEMENT DATED
                                    </td>
                                </tr>
                            </table>

                            {{-- Schedule 2. Principle Contractor --}}
                            <table class="table" style="padding: 0px; margin: 0px">
                                <tr>
                                    <td width="5%" style="margin:5px 0 0 0; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">2.</h5></td>
                                    <td class="pad5" style="border: 0px">
                                        <h4 style="margin: 0px">Principal contractor</h4>
                                        <hr style="margin: 5px 0px 0px 0px">
                                    </td>
                                </tr>
                                <tr>
                                    <td width="5%" class="pad0" style="border: 0px"><h5 style="margin: 0px">&nbsp;</h5></td>
                                    <td class="pad5" style="border: 0px">
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">NAME </span>
                                            <span style="display: table-cell;">{{ $company->reportsTo()->name }}</span>
                                        </div>
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">ADDRESS </span>
                                            <span style="display: table-cell;">{!! $company->reportsTo()->address_formatted !!}<br></span>
                                        </div>
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">ABN </span>
                                            <span style="display: table-cell; width: 200px;">{{ $company->reportsTo()->abn }}</span>
                                            <span style="display: table-cell;">ACN </span>
                                        </div>
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">PHONE </span>
                                            <span style="display: table-cell">{{ $company->reportsTo()->phone }}</span>
                                        </div>
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">EMAIL </span>
                                            <span style="display: table-cell">{!! ($company->reportsTo()->id == 3) ? 'accounts1@capecode.com.au' : $company->reportsTo()->email !!}</span>
                                        </div>
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 190px;">HIA MEMBER NUMBER </span>
                                            <span style="display: table-cell">&nbsp;</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>


                            {{-- Schedule 3. Tade Contractor --}}
                            <table class="table" style="padding: 0px; margin: 0px">
                                <tr>
                                    <td width="5%" style="margin:5px 0 0 0; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">3.</h5></td>
                                    <td class="pad5" style="border: 0px">
                                        <h4 style="margin: 0px">Trade contractor</h4>
                                        <hr style="margin: 5px 0px 0px 0px">
                                    </td>
                                </tr>
                                <tr>
                                    <td width="5%" class="pad0" style="border: 0px"><h5 style="margin: 0px">&nbsp;</h5></td>
                                    <td class="pad5" style="border: 0px">
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">NAME </span>
                                            <span style="display: table-cell">{{ $company->name }}</span>
                                        </div>
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">ADDRESS </span>
                                            <span style="display: table-cell">{!! $company->address_formatted !!}<br></span>
                                        </div>
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">ABN </span>
                                            <span style="display: table-cell; width: 200px;">{{ $company->abn }}</span>
                                            <span style="display: table-cell;">ACN </span>
                                        </div>
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">PHONE </span>
                                            <span style="display: table-cell">{{ $company->phone }}</span>
                                        </div>
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">EMAIL </span>
                                            <span style="display: table-cell">{{ $company->email }}</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            {{-- Schedule 4 --}}
                            <table class="table" style="padding: 0px; margin: 0px">
                                <tr>
                                    <td width="5%" style="margin:5px 0px 0px 0px; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">4.</h5></td>
                                    <td class="pad0" style="border: 0px;">
                                        In consideration of:
                                        <ol type="a" style="padding-left:25px; margin-bottom: 5px">
                                            <li>the <b>trade contractor</b> agreeing to quote for <b>trade works</b> whenever asked by the <b>principal contractor</b>, and</li>
                                            <li>the <b>principal contractor</b> agreeing to pay, on demand by the <b>trade contractor</b>, the sum of $1,</li>
                                        </ol>
                                        the parties agree that the period trade contract conditions overleaf are deemed to be incorporated into each <b>trade contract</b> for a period of 12 months from the date of this agreement.
                                    </td>
                                </tr>
                            </table>

                            {{-- Schedule 5 --}}
                            <table class="table" style="padding: 0px; margin: 0px">
                                <tr>
                                    <td width="5%" style="margin:5px 0 0 0; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">5.</h5></td>
                                    <td class="pad0" style="border: 0px">
                                        The <b>trade contractor</b> acknowledges and agrees that:
                                        <ol type="a" style="padding-left:25px; margin-bottom: 5px">
                                            <li>the <b>principal contractor</b> has not made any representation, and</li>
                                            <li>the <b>trade contractor</b> has not relied on any representation made by the <b>principal contractor</b>,</li>
                                        </ol>
                                        as to the availability of work or the number of work orders that will be issued by the <b>principal contractor</b>.
                                    </td>
                                </tr>
                            </table>

                            {{-- Schedule 6 --}}
                            <table class="table" style="padding: 0px; margin: 0px">
                                <tr>
                                    <td width="5%" style="margin:5px 0 0 0; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">6.</h5></td>
                                    <td class="pad5" style="border: 0px">
                                        The parties agree that for each <b>trade contract</b> the scope of the <b>trade works</b>, the commencement and completion dates of the <b>trade works</b> and the price of <b>trade
                                            works</b> will be set out:
                                        <ol type="a" style="padding-left:25px">
                                            <li>in a quote from the <b>trade contractor</b> that is accepted by the <b>principal contractor</b>;</li>
                                            <li>in a work order issued by the <b>principal contractor</b> that is accepted by the <b>trade contractor</b>; or</li>
                                            <li>as otherwise evidenced in writing and signed by the parties.</li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>





                            <div class="form-actions right">
                                <a href="/company/{{ $company->id }}/doc" class="btn default"> Back</a>
                                <button type="submit" name="save" value="save" class="btn green" id="upload" style="display: none;">Upload</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
    </div>
@stop

@section('page-level-plugins-head')
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css"/>
    <link href="/css/libs/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/js/libs/fileinput.min.js"></script>
    <!--<script src="/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>-->
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script>
    $.ajaxSetup({
        headers: {'X-CSRF-Token': $('meta[name=token]').attr('value')}
    });


    $(document).ready(function () {

        /* Select2 */
        $("#lic_type").select2({
            placeholder: "Select one or more",
            width: '100%',
        });
    });

    $('.date-picker').datepicker({
        autoclose: true,
        clearBtn: true,
        format: 'dd/mm/yyyy',
    });

</script>
@stop