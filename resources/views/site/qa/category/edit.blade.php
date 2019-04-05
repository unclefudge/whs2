@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-file-text-o"></i> Quality Assurance Reports</h1>
    </div>
@stop
@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('site'))
            <li><a href="/site">Sites</a><i class="fa fa-circle"></i></li>
        @endif
        @if (Auth::user()->hasAnyPermissionType('site.qa'))
            <li><a href="/site/qa">Quality Assurance</a><i class="fa fa-circle"></i></li>
            <li><a href="/site/qa/templates">Templates</a><i class="fa fa-circle"></i></li>
            <li><a href="/site/qa/category">Categories</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Edit Category</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-pencil "></i>
                            <span class="caption-subject font-green-haze bold uppercase">Edit Category</span>
                            <span class="caption-helper">ID: {{ $cat->id }}</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model($cat, ['method' => 'PATCH', 'action' => ['Site\SiteQaCategoryController@update', $cat->id], 'class' => 'horizontal-form','id'=>'qa_form']) !!}
                        @include('form-error')

                        <div class="form-body">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {!! fieldHasError('name', $errors) !!}">
                                        {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                                        {!! Form::text('name', $cat->name, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('name', $errors) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions right">
                            <a href="/site/qa/category" class="btn default"> Back</a>
                            <button type="submit" class="btn green"> Save</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    @stop <!-- END Content -->


@section('page-level-plugins-head')
@stop

@section('page-level-plugins')
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script>
    $(document).ready(function () {

    });
</script>
@stop

