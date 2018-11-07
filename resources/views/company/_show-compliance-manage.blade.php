<div class="portlet light" id="show_compliance">
    <div class="portlet-title tabbable-line">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">Compliance Management</span>
        </div>
        <div class="actions">
            @if (Auth::user()->allowed2('edit.compliance.manage', $company) && $company->status)
                <button class="btn btn-circle green btn-outline btn-sm" onclick="editForm('compliance')">Edit</button>
            @endif
        </div>
    </div>
    <div class="portlet-body">
        {{-- Current Overrides --}}
        @if ($company->complianceOverrides()->count())
            @foreach ($company->complianceOverrides() as $over)
                <div class="row">
                    <div class="col-md-2">Override:</div>
                    <div class="col-md-10">
                        {{ $overrideTypes::name($over->type) }}
                        @if (($over->type != 'cdu'))
                            {!! ($over->required) ? "<b>IS REQUIRED</b>" : "is <span class='font-red'><b>NOT REQUIRED</b>" !!}
                            <?php $cat = substr($over->type, 2) ?>
                            @if ((($company->requiresCompanyDoc($cat, 'system')) && $over->required) || (!$company->requiresCompanyDoc($cat, 'system') && !$over->required))
                                &nbsp; <span class="label label-warning label-sm">Default</span>
                            @endif
                        @endif
                    </div>
                    {{--}}<div class="col-md-2"><button class="btn btn-xs dark delete_leave" data-id="{{ $over->id }}" data-reason="{{ $over->reason }}"><i class="fa fa-trash"></i></button></div>--}}
                </div>
                <div class="row">
                    <div class="col-md-2">Reason:</div>
                    <div class="col-md-10">{{ $over->reason  }}</div>
                </div>
                <div class="row">
                    <div class="col-md-2">Set by:</div>
                    <div class="col-md-6">{{ $over->updatedBy->name  }}</div>
                    <div class="col-md-4">Expires: {{ ($over->expiry) ? $over->expiry->format('d/m/Y') : 'never'  }}</div>
                </div>
                <hr class="field-hr">
            @endforeach
        @else
            <div class="row">
                <div class="col-md-12">Company uses the default system compliance requirements</div>
            </div>
        @endif
    </div>
</div>
