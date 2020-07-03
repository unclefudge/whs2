<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Privacy Policy</title>
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
            font-weight: 700;
        }

        body {
            font-size: 10px;
            line-height: 10px;
        }

        body.pdf {
            font-size: 10px;
            line-height: 10px;
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

        td.pad0, th.pad0 {
            padding: 0px !important;
            line-height: 1em !important;
            border: 0px !important;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <h3 style="margin: 0px">Privacy Policy <span class="pull-right" style="font-size:18px"></span></h3>
            <hr style="margin: 5px 0px 10px 0px">
        </div>
    </div>


    {{-- 1. Purpose --}}
    <table class="table" style="padding: 0px; margin: 0px">
        <tr>
            <td width="5%" style="margin:5px 0 0 0; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">1.</h5></td>
            <td class="pad5" style="border: 0px">
                <h4 style="margin: 0px">Purpose</h4>
                <p>The protection of personal information in the private sector is required by the Privacy Act 1988 as amended by the Privacy Amendment (Private Sector) Act 2000 (“Act”). All employees, officers and agents of Cape Cod Australia Pty Ltd (“Cape Cod”) are
                    expected
                    to comply with the Act and Cape Cod ’s policy concerning the protection of personal information.</p>
                Cape Cod is committed to protecting the privacy of individuals. We will only use your personal information for the purpose for which it was provided, in accordance with this policy or in accordance with the Act.
            </td>
        </tr>
    </table>

    {{-- 2. Scope --}}
    <table class="table" style="padding: 0px; margin: 0px">
        <tr>
            <td width="5%" style="margin:5px 0 0 0; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">2.</h5></td>
            <td class="pad5" style="border: 0px">
                <h4 style="margin: 0px">Scope</h4>
                This Privacy Policy is governed by the Australian Privacy Principles under the Privacy Act 1988. This Policy applies to the collection, storage, maintenance and disposal of personal information gathered from participants as part of the delivery of services to
                Cape Cod customers.
            </td>
        </tr>
    </table>

    {{-- 3. Policy --}}
    <table class="table" style="padding: 0px; margin: 0px">
        <tr>
            <td width="5%" style="margin:5px 0px 0px 0px; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">3.</h5></td>
            <td class="pad0" style="border: 0px;">
                <h4 style="margin: 0px">Policy</h4>
            </td>
        </tr>
    </table>

    {{-- 3.1 Collection of data --}}
    <table class="table" style="padding: 0px; margin: 0px">
        <tr>
            <td width="5%" style="margin:5px 0 0 0; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">3.1.</h5></td>
            <td class="pad0" style="border: 0px">
                <h4 style="margin: 0px">Collection of Personal Information</h4>
                We collect personal information when persons:<br>
                <ol type="a" style="padding-left:25px; margin-bottom: 5px">
                    <li>interact with Cape Cod through the phone, in person, via email or through our website where personal details are provided;</li>
                    <li>apply for positions at or contract services to Cape Cod.</li>
                </ol>
                We collect and store personal information to:<br>
                <ol type="a" style="padding-left:25px; margin-bottom: 5px">
                    <li>carry on a business of providing services that you have requested;</li>
                    <li>keep a record of business transactions for necessary purposes such as orders, accounts, audits, taxation and Government agencies;</li>
                    <li>analyze our business transactions in order to improve our products and services;</li>
                    <li>communicate with existing customers to offer products or promotions which may meet the needs of the customer;</li>
                    <li>comply with the law or to use information as permitted under the law.</li>
                </ol>
                We collect and hold following types of personal information:<br>
                <ol type="a" style="padding-left:25px; margin-bottom: 5px">
                    <li>contact details that may include names, business names, addresses, email addresses, fax numbers, phone numbers, signatures and social media profile names;</li>
                    <li>optional personal information that has been provided by customers, including, but not limited to occupation, gender, birth date or age; and</li>
                    <li>optional surveys that provide personal information.</li>
                </ol>
            </td>
        </tr>
    </table>



    {{-- Page 2 --}}
    <div class="page"></div>

    <div class="row">
        <div class="col-xs-12">
            <h3 style="margin: 0px">Privacy Policy
                <small>continued</small>
                <span class="pull-right" style="font-size:18px"></span></h3>
            <hr style="margin: 5px 0px 10px 0px">
        </div>
    </div>



    {{-- Signature --}}
    <br><br><br><br>
    <div style="width: 100%; display: table;">
        <span style="display: table-cell; width: 280px; border-bottom: 1px solid #eee; border-top: 0px; background-color: #eee; padding:10px;">{!! nl2br($policy->contractor_signed_name) !!}</span>
        <span style="display: table-cell;">&nbsp;</span>
    </div>
    <div style="width: 100%; display: table;">
        <span style="display: table-cell; width: 300px; border-top: 0px">Trade Contractor's Signature</span>
        <span style="display: table-cell;">&nbsp;</span>
    </div>


    {{-- Page 3 --}}
    <div class="page"></div>

    <div class="row">
        <div class="col-xs-12">
            <h4 style="margin: 0px">Period Trade Contract Conditions</h4>
            <hr style="margin: 5px 0px 5px 0px">
        </div>
    </div>
</div>
</body>
</html>