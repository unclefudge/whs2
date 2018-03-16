<div class="portlet light">
    <div class="portlet-title tabbable-line">
        <div class="caption">
            <i class="icon-bubbles font-dark hide"></i>
            <span class="caption-subject font-dark bold uppercase">Documents</span>
        </div>
        <!--
        <ul class="nav nav-tabs">
            <li class="active"><a href="" data-toggle="tab" id="doc_current"> Current </a></li>
            <li><a href="" data-toggle="tab" id="doc_expired"> Expired </a></li>
        </ul>-->

        <div class="actions">
            @if (Auth::user()->allowed2('edit.company', $company))
                <button class="btn btn-circle green btn-outline btn-sm" onclick="editForm('company')">Add</button>
            @endif
        </div>
        {{--}}
       <div class="actions">
           <div class="btn-group btn-group-devided" data-toggle="buttons">
               <label class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm">
                   <input type="radio" name="options" class="toggle" id="option2">Current</label>
               <label class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm">
                   <input type="radio" name="options" class="toggle" id="option2">Expired</label>
           </div>
       </div>--}}
        {!! Form::hidden('doc_status', 1, ['class' => 'form-control bs-select', 'id' => 'doc_status']) !!}
    </div>

    <div class="portlet-body">
        <!--<button class="btn btn-circle green btn-outline btn-sm pull-right" onclick="editForm('company')">Add</button>-->
        <div class="col-md-12">
            @if (($company->missingDocs()))
                <div class="alert alert-danger">
                    <div>Missing documents required to be compliant:</div>
                    <ul>
                        @foreach ($company->missingDocs() as $type => $name)
                            <li>
                                {{ $name }}
                                {!! ($company->activeCompanyDoc($type) && $company->activeCompanyDoc($type)->status == 2) ?  '<span class="label label-warning label-sm">Pending Approval</span>' : '' !!}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div class="col-md-3 pull-right">
            {!! Form::select('status', ['1' => 'Current', '0' => 'Expired'], null, ['class' => 'form-control bs-select', 'id' => 'status',]) !!}
        </div>
        <table class="table table-striped table-bordered table-hover order-column" id="table_docs">
            <thead>
            <tr class="mytable-header">
                <th width="5%"> #</th>
                <th> Document</th>
                <!--<th width="25%"> Details</th>-->
                <th width="10%"> Expiry</th>
                <th width="10%"></th>
            </tr>
            </thead>
        </table>
    </div>
</div>
