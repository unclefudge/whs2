<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Site Plan</title>
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
    @foreach($sitedata as $siteplan)
        <div class="page22">
            <?php $site = App\Models\Site\Site::find($siteplan->site_id) ?>
            <div class="row">
                <div class="col-xs-8">
                    <h3 style="margin: 0px">{{ $site->name }}
                        <small>site: {{ $site->code }}</small>
                    </h3>{{ $site->address }}, {{  $site->suburb_state_postcode }}</div>
                <div class="col-xs-4">
                    <h6><b>Supervisor:</b> {{ $site->supervisorsSBC() }}</h6>
                </div>
            </div>
            <hr style="margin: 5px 0px">
            <br>
            <div class="row" style="border: 1px solid lightgrey; background-color: #f0f6fa; font-weight: bold; overflow: hidden;">
                <div class="col-xs-2">Date</div>
                <div class="col-xs-3">Company</div>
                <div class="col-xs-3">Attendance</div>
            </div>
            @foreach($siteplan->attendance as $day => $company)
                <div class="row" style="border-bottom: 1px solid lightgrey;">
                    <div class="col-xs-2">{{ $day }}</div>
                    <div class="col-xs-3">
                        @foreach($company as $company_name => $data)
                            {{ $company_name }} <br>
                        @endforeach
                    </div>
                    <div class="col-xs-3">
                        @foreach($company as $company_name => $data)
                            <?php $c = count($data); $x = 1;  ?>
                            @foreach ($data as $user_id => $name)
                                @if ($user_id != 'tasks')
                                    {{ $name }}@if ($x < $c), @endif
                                @endif
                                <?php $x ++ ?>
                            @endforeach
                            <br>
                        @endforeach
                    </div>
                </div>

            @endforeach


            {{--
        @foreach($siteplan->weeks as $week_num => $week_data )
            <div class="row" style="border: 1px solid lightgrey; background-color: #f0f6fa; font-weight: bold; overflow: hidden;">
                <div class="col-xs-2 border-right">{!! $week_data[0][1] !!}</div>
                <div class="col-xs-2 border-right">{!! $week_data[0][2] !!}</div>
                <div class="col-xs-2 border-right">{!! $week_data[0][3] !!}</div>
                <div class="col-xs-2 border-right">{!! $week_data[0][4] !!}</div>
                <div class="col-xs-2 border-right">{!! $week_data[0][5] !!}</div>
                <div class="col-xs-1 border-right">{!! $week_data[0][6] !!}</div>
                <div class="col-xs-1">{!! $week_data[0][7] !!}</div>
            </div>
            <div class="row" style="border: 1px solid lightgrey; background-color: #fff; font-weight: bold; overflow: hidden;">
                <div class="col-xs-2 border-right">{!! $week_data[1][1] !!}</div>
                <div class="col-xs-2 border-right">{!! $week_data[1][2] !!}</div>
                <div class="col-xs-2 border-right">{!! $week_data[1][3] !!}</div>
                <div class="col-xs-2 border-right">{!! $week_data[1][4] !!}</div>
                <div class="col-xs-2 border-right">{!! $week_data[1][5] !!}</div>
                <div class="col-xs-1 border-right">{!! $week_data[1][6] !!}</div>
                <div class="col-xs-1">{!! $week_data[1][7] !!}</div>
            </div>
        @endforeach
        --}}
        </div>
    @endforeach
</div>
</body>
</html>