<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upcoming Job Start Dates</title>
    <link href="{{ asset('/') }}/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('/') }}/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <style>
        @import url(http://fonts.googleapis.com/css?family=PT+Sans);

        @page {
            margin: .7cm .7cm
        }

        body, h1, h2, h3, h4, h5, h6 {
            font-family: 'PT Sans', serif;
        }

        h1 {
            /*font-family: 'Martel Sans', sans-serif;*/
            font-weight: 700;
        }

        body {
            font-size: 8px;
        }

        div.page {
            page-break-after: always;
            page-break-inside: avoid;
        }

        .row-striped:nth-of-type(odd) {
            background-color: #ffffff;
        }

        .row-striped:nth-of-type(even) {
            background-color: #f4f4f4;
        }

        .border-right {
            border-right: 1px solid lightgrey;
            margin-bottom: -999px;
            padding-bottom: 999px;
        }
        td.pad5, th.pad5 {
            padding: 5px !important;
            line-height: 1em !important;
        }

    </style>
</head>

<body>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <h3 style="margin: 0px">Upcoming Job Start Dates</h3>
        </div>
    </div>
    <hr style="margin: 5px 0px 15px 0px">

    <table class="table table-striped table-bordered table-hover order-column" id="table1" style="padding: 0px; margin: 0px">
        <thead>
        <tr style="background-color: #f0f6fa; font-weight: bold; overflow: hidden;">
            <th width="5%" class="pad5">Start Date</th>
            <th width="5%" class="pad5">Job #</th>
            <th width="15%" class="pad5">Site</th>
            <th width="15%" class="pad5">Supervisor</th>
            <th class="pad5">Company Allocated</th>
            <th width="5%" class="pad5">Contract Sent</th>
            <th width="5%" class="pad5">Contract Signed</th>
            <th width="5%" class="pad5">Deposit Paid</th>
            <th width="3%" class="pad5">ENG</th>
            <th width="3%" class="pad5">CC</th>
            <th width="3%" class="pad5">HBCF</th>
            <th width="15%" class="pad5">Consultant</th>
        </tr>
        </thead>
        <tbody>
        @foreach($startdata as $row)
            <tr>
                <td class="pad5">{!! $row['date'] !!}</td>
                <td class="pad5">{!! $row['code'] !!}</td>
                <td class="pad5">{!! $row['name'] !!}</td>
                <td class="pad5">{!! $row['supervisor'] !!}</td>
                <td class="pad5">{!! $row['company'] !!}</td>
                <td class="pad5">{!! $row['contract_sent'] !!}</td>
                <td class="pad5">{!! $row['contract_signed'] !!}</td>
                <td class="pad5">{!! $row['deposit_paid'] !!}</td>
                <td class="pad5">{!! $row['eng'] !!}</td>
                <td class="pad5">{!! $row['cc'] !!}</td>
                <td class="pad5">{!! $row['hbcf'] !!}</td>
                <td class="pad5">{!! $row['consultant'] !!}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{--
    <div class="row" style="border: 1px solid lightgrey; background-color: #f0f6fa; font-weight: bold; overflow: hidden;">
        <div class="col-xs-3" style="padding: 0px">
            <div class="col-xs-3 border-right" style="padding-left: 3px">Start Date</div>
            <div class="col-xs-2 border-right" style="padding-left: 3px">Job #</div>
            <div class="col-xs-7 border-right" style="padding-left: 3px">Site</div>
        </div>
        <div class="col-xs-3" style="padding: 0px">
            <div class="col-xs-4 border-right" style="padding-left: 3px;">Supervisor</div>
            <div class="col-xs-8 border-right" style="padding-left: 3px">Company Allocated</div>
        </div>
        <div class="col-xs-3" style="padding: 0px">
            <div class="col-xs-4 border-right" style="padding-left: 3px">Contract Sent</div>
            <div class="col-xs-4 border-right" style="padding-left: 3px">Contract Signed</div>
            <div class="col-xs-4 border-right" style="padding-left: 3px">Deposit Paid</div>
        </div>
        <div class="col-xs-3" style="padding: 0px">
            <div class="col-xs-2 border-right" style="padding-left: 3px">ENG</div>
            <div class="col-xs-2 border-right" style="padding-left: 3px">CC</div>
            <div class="col-xs-2 border-right" style="padding-left: 3px">HBCF</div>
            <div class="col-xs-6" style="padding-left: 3px">Consultant</div>
        </div>
    </div>
    @foreach($startdata as $row)
        <div class="row row-striped" style="border-style: none solid solid; border-width: 1px; border-color:  lightgrey; overflow: hidden;">
            <div class="col-xs-3" style="padding: 0px">
                <div class="col-xs-3 border-right" style="padding-left: 3px">{!! $row['date'] !!}</div>
                <div class="col-xs-2 border-right" style="padding-left: 3px">{!! $row['code'] !!}</div>
                <div class="col-xs-7 border-right" style="padding-left: 3px">{!! $row['name'] !!}</div>
            </div>
            <div class="col-xs-3" style="padding: 0px">
                <div class="col-xs-4 border-right" style="padding-left: 3px">{!! $row['supervisor'] !!}</div>
                <div class="col-xs-8 border-right" style="padding-left: 3px">{!! $row['company'] !!}</div>
            </div>
            <div class="col-xs-3" style="padding: 0px">
                <div class="col-xs-4 border-right" style="padding-left: 3px">{!! $row['contract_sent'] !!}</div>
                <div class="col-xs-4 border-right" style="padding-left: 3px">{!! $row['contract_signed'] !!}</div>
                <div class="col-xs-4 border-right" style="padding-left: 3px">{!! $row['deposit_paid'] !!}</div>
            </div>
            <div class="col-xs-3" style="padding: 0px">
                <div class="col-xs-2 border-right" style="padding-left: 3px">{!! $row['eng'] !!}</div>
                <div class="col-xs-2 border-right" style="padding-left: 3px">{!! $row['cc'] !!}</div>
                <div class="col-xs-2 border-right" style="padding-left: 3px">{!! $row['hbcf'] !!}</div>
                <div class="col-xs-6" style="padding-left: 3px">{!! $row['consultant'] !!}</div>
            </div>
        </div>
    @endforeach
    --}}
</div>
</body>
</html>