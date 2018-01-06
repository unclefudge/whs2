<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Work Method Statement - {{-- $doc->name --}}</title>
    <link href="{{ asset('/') }}/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('/') }}/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <style>
        @import url(http://fonts.googleapis.com/css?family=PT+Sans);
        @import url(https://fonts.googleapis.com/css?family=Martel+Sans);

        @page {
            margin: .7cm .7cm
        }

        body, h2, h3, h4, h5, h6 {
            font-family: 'PT Sans', serif;
        }

        h1 {
            font-family: 'Martel Sans', sans-serif;
            font-weight: 700;
        }

        body {
            font-size: 10px;
        }

    </style>
</head>

<body>
<div class="container">
    <div class="row">
        <div class="col-xs-8"><h1>{{ $doc->company->name }}</h1></div>
        <div class="col-xs-4">
            <div class="col-xs-12">
                <small>{{ $doc->company->address }}</small>
            </div>
            <div class="col-xs-12">
                <small>{{  $doc->company->suburb_state_postcode }}</small>
            </div>
            <div class="col-xs-12">
                <small>{{  $doc->company->phone }}</small>
            </div>
            <div class="col-xs-12">
                <small>{{  $doc->company->email }}</small>
            </div>
        </div>
    </div>
    <h4>Safe Work Method Statement
        <small class="pull-right" style="font-size: 10px; margin-top: 15px">version {{ $doc->version }}</small>
    </h4>
    <hr style="margin: 0px">
    <h5 style="vertical-align: bottom;">Activity / Task: <span style="font-size: 18px">&nbsp; <b>{{ $doc->name }}</b></span> <span class="pull-right" style="margin-top: 5px">Project / Location: All Jobs</span></h5>
    <br>
    @if (!$doc->master)
    <div class="row">
        <div class="col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading" style="padding: 1px 15px">
                    <h6><b>Company:</b> <span style="font-size: 15px"> &nbsp; &nbsp; {{ $doc->company->name }}</span></h6>
                </div>
                <div class="panel-body" style="padding: 10px 15px">
                    <div class="row">
                        <div class="col-xs-7">Signed by: {{ $doc->signedCompany->fullname }}</div>
                        <div class="col-xs-5">Date: {{ $doc->user_signed_at->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading" style="padding: 1px 15px">
                    <h6><b>Principal Contractor:</b> <span style="font-size: 15px"> &nbsp; &nbsp; {{ $doc->principleName }}</span></h6>
                </div>
                <div class="panel-body" style="padding: 10px 15px">
                    <div class="row">
                        <div class="col-xs-7">Accepted by: @if ($doc->signedPrinciple) {{ $doc->signedPrinciple->fullname }} @endif</div>
                        <div class="col-xs-5">Date: @if ($doc->signedPrinciple) {{ $doc->principle_signed_at->format('d/m/Y') }} @endif</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row" style="margin-top: 20px">
        <div class="col-xs-12">
            <div class="row" style="border: 1px solid #e7ecf1; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.05); padding: 10px 0px; margin: 0px; background: #f5f5f5; font-weight: bold">
                <div class="col-xs-2">Steps</div>
                <div class="col-xs-2">Potential Hazards</div>
                <div class="col-xs-8">Controls / Responsible Person(s)</div>
            </div>
            <br>
            <!-- Steps -->
            @foreach ($doc->steps->sortBy('order') as $step)
                <div class="row" style="border-bottom: 1px solid lightgrey; padding: 0px; margin: 0px">
                    <div class="col-xs-2">
                        <div class="row">
                            <div class="col-xs-2" style="padding: 0px">{{ $step->order }}.</div>
                            <div class="col-xs-10" style="padding-left:0px;">{{ $step->name }}</div>
                        </div>
                    </div>
                    <!-- Hazards -->
                    <div class="col-xs-2" style="padding: 0px; margin 0px">
                        <ul style="margin-left: -15px">
                            @foreach ($step->hazards as $hazard)
                                <li style="margin-bottom: 3px">{{ $hazard->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <!-- Controls -->
                    <div class="col-xs-8" style="padding: 0px; margin: 0px">
                        <ul style="margin-left: -15px">
                            @foreach ($step->controls as $control)
                                <li style="margin-bottom: 3px">
                                    <div>{{ $control->name }} <b>By: {!! $control->responsibleName !!}</b></div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <!-- Person Responsible Info -->
    <div class="row" style="margin-top: 20px">
        <div class="col-xs-6 text-right"><b>Person responsible for ensuring compliance with SWMS:</b></div>
        <div class="col-xs-6">{{ $doc->res_compliance }}</div>
    </div>
    <div class="row">
        <div class="col-xs-6 text-right"><b>Person responsible for reviewing SWMS control measures:</b></div>
        <div class="col-xs-6">{{ $doc->res_review }}</div>
    </div>
    <hr>
    <div class="row">
        <div class="col-xs-12 text-right" style="padding: 0px 20px">Document Updated: {{ $doc->updated_at->format('d/m/Y') }}</div>
    </div>
</div>
</body>
</html>