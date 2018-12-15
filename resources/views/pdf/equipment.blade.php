<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Equipment List</title>
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
    <div class="page22">
        <div class="row">
            <div class="col-xs-8">
                <h3 style="margin: 0px">Equipment List</h3>
            </div>
            <div class="col-xs-4">
                <h6>
                    <b>Date: {{ \Carbon\Carbon::today()->format('d/m/Y') }}</b>
                </h6>
            </div>
        </div>
        <hr style="margin: 5px 0px">
        <br>
        <?php $row_count = 0; ?>
        @foreach ($equipment as $equip)
            <?php $row_count ++ ?>
            <div class="row">
                <div class="col-md-12"><b>{{ $equip->name }} ({{ $equip->total }})</b></div>
            </div>
            @foreach ($equip->locations() as $location)
                <?php $row_count ++ ?>
                @if ($row_count > 50)
                    <? $row_count = 0 ?>
                    <div class="page"></div>
                    <div class="row">
                        <div class="col-xs-8"><h3 style="margin: 0px">Equipment List</h3></div>
                        <div class="col-xs-4"><h6><b>Date: {{ \Carbon\Carbon::today()->format('d/m/Y') }}</b></h6></div>
                    </div>
                    <hr style="margin: 5px 0px">
                @endif
                @if ($location->equipment($equip->id)->qty)
                    <div class="row">
                        <div class="col-xs-1 text-right">{{ $row_count }}-{{ $location->equipment($equip->id)->qty }}</div>
                        <div class="col-xs-11">{{ $location->name2 }}</div>
                    </div>
                @endif
            @endforeach
        @endforeach
    </div>
</div>
</body>
</html>