<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Work Method Statement - {{-- $doc->name --}}</title>
    <link href="{{ asset('/') }}/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('/') }}/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <style>
        @import url(http://fonts.googleapis.com/css?family=PT+Sans);
        /*@import url(https://fonts.googleapis.com/css?family=Martel+Sans);*/

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

        div.page {
            page-break-after: always;
            page-break-inside: avoid;
        }

        td.pad5 {
            padding: 5px !important;
            line-height: 1em !important;
        }

        header {
            position: fixed;
            top: -60px;
            left: 0px;
            right: 0px;
            height: 50px;
        }

        footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 20px;
        }

        footer .pagenum:before {
            content: counter(page);
        }

    </style>
</head>

<body>
<div class="container">
    <table class="table" style="border: 0px; padding: 0px; margin: 0px;">
        <tr>
            <td width="70%"><span style="font-size: 28px">{{ $doc->company->name }}</span></td>
            <td>
                <span style="font-size: 8px; line-height: 1em">{{ $doc->company->address }}<br>{{ $doc->company->suburb_state_postcode }}<br>{{ $doc->company->phone }}<br>{{ $doc->company->email }}</span>
            </td>
        </tr>
    </table>

    <h4>Safe Work Method Statement
        <small class="pull-right" style="font-size: 10px; margin-top: 15px">version {{ $doc->version }}</small>
    </h4>
    <hr style="margin: 0px">
    <h5 style="vertical-align: bottom;">Activity / Task: <span style="font-size: 18px">&nbsp; <b>{{ $doc->name }}</b></span> <span class="pull-right" style="margin-top: 5px">Project / Location: All Jobs</span>
    </h5>
    <br>
    @if (!$doc->master)

        <table class="table" style="border: 0px; padding: 0px; margin: 0px;">
            <thead style="border: 0px">
            <tr style="border: 0px; background-color: #f5f5f5; font-weight: bold; padding: 0px; margin: 0px;">
                <th width="48%" style="border: 1px solid #ddd; padding-left: 15px"><span style="font-size: 12px"><b>Company:</b> &nbsp; &nbsp; {{ $doc->company->name }}</span></th>
                <th width="4%" style="border: 0px; background: #FFF"></th>
                <th width="48%" style="border: 1px solid #ddd; padding-left: 15px"><span style="font-size: 12px"><b>Principal Contractor:</b> &nbsp; &nbsp; {{ $doc->principleName }}</span></th>
            </tr>
            </thead>
            <tbody>
            <tr style="border: 0px">
                <td style="border: 1px solid #ddd">
                    <table class="table" style="border: 0px; padding: 0px; margin: 0px;">
                        <tr style="border: 0px;">
                            <td width="60%" style="border: 0px;">Signed by: {{ $doc->signedCompany->fullname }}</td>
                            <td style="border: 0px;">Date: {{ $doc->user_signed_at->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </td>
                <td style="border: 0px">&nbsp;</td>
                <td style="border: 1px solid #ddd">
                    <table class="table" style="padding: 0px; margin: 0px;">
                        <tr>
                            <td width="60%" style="border: 0px;">Accepted by: @if ($doc->signedPrinciple) {{ $doc->signedPrinciple->fullname }} @endif</td>
                            <td style="border: 0px;">Date: @if ($doc->signedPrinciple) {{ $doc->principle_signed_at->format('d/m/Y') }} @endif</td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
        <br><br>
    @endif

    {{-- Items --}}
    <table class="table table-bordered" style="padding: 0px; margin: 0px;">
        <thead>
        <tr style="background-color: #f5f5f5; font-weight: bold;">
            <th width="15%">Steps</th>
            <th width="20%">Potential Hazards</th>
            <th>Controls / Responsible Person(s)</th>
        </tr>
        </thead>
        <tbody>
        <!-- Steps -->
        @foreach ($doc->steps->sortBy('order') as $step)
            <tr>
                <td class="pad5">
                    <div class="row" style="margin: 0px;">
                        <div class="col-xs-2" style="padding: 0px">{{ $step->order }}.</div>
                        <div class="col-xs-10" style="padding-left:0px;">{{ $step->name }}</div>
                    </div>
                </td>
                <!-- Hazards -->
                <td class="pad5">
                    <ul style="margin-left: -15px">
                        @foreach ($step->hazards as $hazard)
                            <li style="margin-bottom: 3px">{{ $hazard->name }}</li>
                        @endforeach
                    </ul>
                </td>
                <!-- Controls -->
                <td class="pad5">
                    <ul style="margin-left: -15px">
                        @foreach ($step->controls as $control)
                            <li style="margin-bottom: 3px">
                                <div>{{ $control->name }} <b>By: {!! $control->responsibleName !!}</b></div>
                            </li>
                        @endforeach
                    </ul>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>

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
        <div class="col-xs-12" style="padding: 0px 20px">Document Created: {{ $doc->updated_at->format('d/m/Y') }}</div>
    </div>
</div>
</body>
</html>