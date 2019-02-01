{{-- Compliance Docs --}}
<?php
$isCompliant = $user->isCompliant();
$compliantDocs = $user->compliantDocs();
?>
<div class="col-lg-6 col-xs-12 col-sm-12 pull-right">
    @if (Auth::user()->allowed2('view.user.acc', $user) || true)
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-dark bold uppercase">Compliance Documents</span>
                </div>
                <div class="actions">
                    @if(count($user->missingDocs()) && (Auth::user()->isCompany($user->company_id) && Auth::user()->allowed2('add.user.doc')) ||
                            (Auth::user()->isCompany($user->company->reportsTo()->id) && Auth::user()->allowed2('add.user.doc') && $user->company->parentUpload()))
                        <a href="/user/{{ $user->id }}/doc/upload" class="btn btn-circle green btn-outline btn-sm">Upload</a>
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                @if (count($compliantDocs))
                    <div class="row">
                        <div class="col-md-12">
                            @if ($isCompliant)
                                <b>All compliance documents have been submited and approved:</b>
                            @else
                                <b>The following {!! count($compliantDocs) !!} documents are required to be compliant:</b>
                            @endif
                        </div>

                        @foreach ($compliantDocs as $type => $name)

                            @if ($user->activeUserDoc($type) && $user->activeUserDoc($type)->status == 1)
                                <div class="col-xs-8"><i class="fa fa-check" style="width:35px; padding: 4px 15px; {!! ($isCompliant) ? 'color: #26C281' : '' !!}"></i>
                                    <a href="{!! $user->activeUserDoc($type)->attachment_url !!}" class="linkDark" target="_blank">{{ $name }}</a>
                                </div>
                                <div class="col-xs-4">
                                    @if (!$isCompliant)
                                        <span class="label label-success label-sm">Accepted</span>
                                    @endif
                                </div>
                            @endif

                            @if ($user->activeUserDoc($type) && $user->activeUserDoc($type)->status == 2)
                                <div class="col-xs-8"><i class="fa fa-question" style="width:35px; padding: 4px 15px"></i>
                                    <a href="{!! $user->activeUserDoc($type)->attachment_url !!}" class="linkDark" target="_blank">{{ $name }}</a>
                                </div>
                                <div class="col-xs-4">
                                    @if (!$isCompliant)
                                        <span class="label label-warning label-sm">Pending Approval</span>
                                    @endif
                                </div>
                            @endif

                            @if ($user->activeUserDoc($type) && $user->activeUserDoc($type)->status == 3)
                                <div class="col-xs-8"><i class="fa fa-question" style="width:35px; padding: 4px 15px"></i>
                                    <a href="{!! $user->activeUserDoc($type)->attachment_url !!}" class="linkDark" target="_blank">{{ $name }}</a>
                                </div>
                                <div class="col-xs-4">
                                    @if (!$isCompliant)
                                        <span class="label label-danger label-sm">Rejected</span>
                                    @endif
                                </div>
                            @endif

                            @if (!$user->activeUserDoc($type))
                                <div class="col-xs-8">
                                    <i class="fa fa-times" style="width:35px; padding: 4px 15px"></i> {{ $name }}
                                    @if(Auth::user()->isCompany($user->company_id) && Auth::user()->allowed2('add.user.doc'))
                                        <a href="/user/{{ $user->id  }}/doc/create"><i class="fa fa-upload" style="padding-left: 10px"></i> Upload</a>
                                    @endif
                                </div>
                                <div class="col-xs-4 font-red">{!! (!$isCompliant) ? 'Not submitted' : '' !!}</div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="row">
                        <div class="col-md-12">No documents are required to be compliant.</div>
                    </div>
                @endif

                @if ($user->activeUserDoc(3) || $user->activeUserDoc(4))
                    <hr>
                    <b>Additional Documents</b><br>
                    <div class="row">
                        @foreach ([3 => 'Contractor Licence', '4' => 'Supervisor Licence'] as $type => $name)
                            @if ($user->activeUserDoc($type) && $user->activeUserDoc($type)->status == 1)
                                <div class="col-xs-8"><i class="fa fa-check" style="width:35px; padding: 4px 15px; {!! ($isCompliant) ? 'color: #26C281' : '' !!}"></i>
                                    <a href="{!! $user->activeUserDoc($type)->attachment_url !!}" class="linkDark" target="_blank">{{ $name }}</a>
                                </div>
                                <div class="col-xs-4">
                                    @if (!$isCompliant)
                                        <span class="label label-success label-sm">Accepted</span>
                                    @endif
                                </div>
                            @endif

                            @if ($user->activeUserDoc($type) && $user->activeUserDoc($type)->status == 2)
                                <div class="col-xs-8"><i class="fa fa-question" style="width:35px; padding: 4px 15px"></i>
                                    <a href="{!! $user->activeUserDoc($type)->attachment_url !!}" class="linkDark" target="_blank">{{ $name }}</a>
                                </div>
                                <div class="col-xs-4">
                                    @if (!$isCompliant)
                                        <span class="label label-warning label-sm">Pending Approval</span>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                {{-- Notify if User is notimated as Supervisor for Company Contract Licence--}}
                <?php $cl_classes = $user->requiredContractorLicencesSBC(); ?>
                @if ($cl_classes)
                    <hr>
                    <b>Listed as supervisor for following Contractor Licence class(s)</b><br>{{ $cl_classes }}
                @endif


                {{-- Non-compliant docs needing Approval --}}
                @if (Auth::user()->companyDocTypeSelect('view', $user->company, 'all') && count($user->nonCompliantDocs('array', 2)))
                    <br>
                    <a href="/user/{{ $user->id }}/doc" class="btn btn-warning">Some documents require approval</a>
                @endif

            </div>
        </div>
    @endif
</div>