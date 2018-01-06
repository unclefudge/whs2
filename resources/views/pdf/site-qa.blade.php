<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quality Assurance</title>
    <link href="{{ asset('/') }}/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('/') }}/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <style>
        @import url(http://fonts.googleapis.com/css?family=PT+Sans);
        /*@import url(https://fonts.googleapis.com/css?family=Martel+Sans);*/

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
            font-size: 10px;
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
    </style>
</head>

<body>
<div class="container">
    @foreach($data as $qa)
        <div class="page">
            <div class="row">
                <div class="col-xs-6">
                    <img src="{!! URL::to('/') !!}/img/logo-capecod2.png">
                </div>
                <div class="col-xs-6">
                    <p>JOB NAME: {{ $site->name }}<br>
                        ADDRESS: {{ $site->full_address }}</p>
                </div>
            </div>
            <div class="row" style="padding-top: 5px">
                <div class="col-xs-12 ">
                    <br>
                    <h4 style="margin: 0px"><b>{{ $qa->name }}</b></h4>
                </div>
            </div>
            <br>

            <!-- Items -->
            <div class="row" style="border: 1px solid lightgrey; background-color: #f0f6fa; font-weight: bold; overflow: hidden; padding:5px">
                <div class="col-xs-1"></div>
                <div class="col-xs-9">Inspection Item</div>
                <div class="col-xs-2">Checked Date</div>
            </div>

            @foreach($qa->items as $item_num => $item_data )
                <div class="row" style="border-style: none solid solid; border-width: 1px; border-color:  lightgrey; overflow: hidden; padding:2px;">
                    @if($item_data['status'] == '1')
                        <div class="col-xs-1"><i class="fa fa-check-square-o" style="color:#32c5d2; font-size: 16px; padding-top: 5px"></i></div>
                    @elseif($item_data['status'] == '-1')
                        <div class="col-xs-1">N/A</div>
                    @else
                        <div class="col-xs-1"><i class="fa fa-square-o" style="color:#e7505a; font-size: 16px; padding-top: 5px"></i></div>
                    @endif
                    <div class="col-xs-9">{{ $item_data['name'] }}<br>{{ $item_data['done_by'] }}</div>
                    <div class="col-xs-2">{{ $item_data['sign_by'] }}<br>{{ $item_data['sign_at'] }}</div>
                </div>
            @endforeach
            <br><br>

            <!-- Notes -->
            @if ($qa->actions)
                <h6 style="margin: 0px"><b>Notes</b></h6>
                <hr style="margin: 3px 0px">
                @foreach($qa->actions as $action_id => $action_data )
                    <div class="row" style="overflow: hidden; padding:5px;">
                        <div class="col-xs-3">{{ $action_data['created_at'] }} - {{ $action_data['created_by'] }}</div>
                        <div class="col-xs-9">{{ $action_data['action'] }}</div>
                    </div>
                @endforeach
                <br><br>
            @endif

            {{-- Report Signatures --}}
            <h6 style="margin: 0px"><b>QUALITY ASSURANCE SIGN-OFF</b></h6>
            <hr style="margin: 3px 0px 5px 0px">
            <p>The above inspection items have been checked by the site construction supervisor and conform to the Cape Cod standard set.</p>
            <div class="row" style="overflow: hidden; padding:1px">
                <div class="col-xs-3" style="text-align: right">Site Supervisor</div>
                <div class="col-xs-9">@if ($qa->super_sign_by){{ $qa->super_sign_by }}, &nbsp; {{ $qa->super_sign_at }}@endif</div>
            </div>
            <div class="row" style="overflow: hidden; padding:1px">
                <div class="col-xs-3" style="text-align: right">Construction Manager</div>
                <div class="col-xs-9">@if ($qa->manager_sign_by){{ $qa->manager_sign_by }}, &nbsp; {{ $qa->manager_sign_at }}@endif</div>
            </div>

        </div>
    @endforeach
</div>
</body>
</html>