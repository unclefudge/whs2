<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subcontractors Statement</title>
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
            font-size: 13px;
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
    <div class="page">
        <div class="row">
            <div class="col-xs-3">
                {{--<img src="{{ url() }}/img/nsw_coatofarms.jpg" width="100px">--}}
            </div>
            <div class="col-xs-9 text-center">
                <h3 style="margin: 0px">SUBCONTRACTOR’S STATEMENT</h3>
                <h5 style="margin: 0px">REGARDING WORKER’S COMPENSATION, PAYROLL TAX AND</h5>
                <h5 style="margin: 0px">REMUNERATION (Note1 – see back of form)</h5>
            </div>
        </div>

        {{-- Intro --}}
        <div class="row">
            <div class="col-xs-12">
                <p class="text-justify">For the purposes of this Statement a “subcontractor” is a person (or other legal entity) that has entered into a contract with a “principal contractor” to carry
                    out work.</p>
                <p class="text-justify">This Statement must be signed by a “subcontractor” (or by a person who is authorised, or held out as being authorised, to sign the statement by the
                    subcontractor) referred to in
                    any of s175B Workers Compensation Act 1987, Schedule 2 Part 5 Payroll Tax Act 2007, and s127 Industrial Relations Act 1996 where the “subcontractor” has employed or engaged workers
                    or subcontractors during the period of the contract to which the form applies under the relevant Act(s). The signed Statement is to be submitted to the relevant principal
                    contractor.</p>
                <p style="font-size:14px; font-weight:700">SUBCONTRACTOR’S STATEMENT (Refer to the back of this form for Notes, period of Statement retention, and Offences under various Acts.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                {{-- Contact Details --}}
                <div style="width: 100%; display: table;">
                    <span style="display: table-cell; width: 90px;">Subcontractor: </span>
                    <span style="display: table-cell; border-bottom: 1px dotted #555555;">{{ $company->name }}</span>
                    <span class="text-right" style="display: table-cell; width: 40px;"> &nbsp; ABN: &nbsp; </span>
                    <span style="display: table-cell; width: 100px; border-bottom: 1px dotted #555555;">{{ $company->abn }}</span>
                </div>
                <div style="color:#bfbfbf; padding-left: 250px">(Business name)</div>

                <div style="width: 100%; display: table;">
                    <span style="display: table-cell; width: 20px;">of: </span>
                    <span style="display: table-cell; border-bottom: 1px dotted #555555;">{{ $company->address }} {{  $company->suburb_state_postcode }} </span>
                </div>
                <div style="color:#bfbfbf; padding-left: 300px">(Address of subcontractor)</div>

                {{-- Note 2 --}}
                <div style="width: 100%; display: table;">
                    <span style="display: table-cell; width: 190px;">has entered into a contract with: </span>
                    <span style="display: table-cell; border-bottom: 1px dotted #555555;">{{ $company->reportsToCompany()->name }}</span>
                    <span class="text-right" style="display: table-cell; width: 40px;"> &nbsp; ABN: &nbsp; </span>
                    <span style="display: table-cell; width: 100px; border-bottom: 1px dotted #555555;">{{ $company->reportsToCompany()->abn }}</span>
                </div>
                <div><span style="color:#bfbfbf; padding-left: 250px">(Business name of principal contractor)</span> <span class="pull-right"><b>(Note 2)</b></span></div>

                {{-- Note 3 --}}
                <div style="width: 100%; display: table;">
                    <span style="display: table-cell; width: 160px;">Contract number/identifier </span>
                    <span style="display: table-cell; border-bottom: 1px dotted #555555;"></span>
                </div>
                <div><span class="pull-right"><b>(Note 3)</b></span></div>

                {{-- Note 4 --}}
                <div style="width: 100%; display: table;">
                    <span style="display: table-cell; width: 240px;">This Statement applies for work between: </span>
                    @if ($data['date_from'])
                        <span style="display: table-cell;">{{ $data['date_from']->format('d/m/Y') }} &nbsp; and &nbsp; {{ $data['date_to']->format('d/m/Y') }} &nbsp; inclusive,</span>
                    @else
                        <span style="display: table-cell; width: 20px; border-bottom: 1px dotted #555555;">{{ $data['date_from']->format('d') }}</span>
                        <span style="display: table-cell; width: 20px"> &nbsp; &nbsp;/ </span>
                        <span style="display: table-cell; width: 20px; border-bottom: 1px dotted #555555;">{{ $data['date_from']->format('m') }}</span>
                        <span style="display: table-cell; width: 20px"> &nbsp; &nbsp;/ </span>
                        <span style="display: table-cell; width: 20px; border-bottom: 1px dotted #555555;">{{ $data['date_from']->format('Y') }}</span>
                        <span style="display: table-cell; width: 50px"> &nbsp; and  </span>
                        <span style="display: table-cell; width: 20px; border-bottom: 1px dotted #555555;">{{ $data['date_to']->format('d') }}</span>
                        <span style="display: table-cell; width: 20px"> &nbsp; &nbsp;/ </span>
                        <span style="display: table-cell; width: 20px; border-bottom: 1px dotted #555555;">{{ $data['date_to']->format('m') }}</span>
                        <span style="display: table-cell; width: 20px"> &nbsp; &nbsp;/ </span>
                        <span style="display: table-cell; width: 20px; border-bottom: 1px dotted #555555;">{{ $data['date_to']->format('Y') }}</span>
                        <span style="display: table-cell;"> &nbsp; inclusive,  </span>
                    @endif
                    <span class="pull-right"><b>(Note 4)</b></span>
                </div>
                <br>

                {{-- Note 5 --}}
                <div style="width: 100%; display: table;">
                    <span style="display: table-cell; width: 210px;">subject of the payment claim dated: </span>
                    <span style="display: table-cell; width: 20px; border-bottom: 1px dotted #555555;"></span>
                    <span style="display: table-cell; width: 20px"> &nbsp; &nbsp;/ </span>
                    <span style="display: table-cell; width: 20px; border-bottom: 1px dotted #555555;"></span>
                    <span style="display: table-cell; width: 20px"> &nbsp; &nbsp;/ </span>
                    <span style="display: table-cell; width: 20px; border-bottom: 1px dotted #555555;"></span>
                    <span class="pull-right"><b>(Note 5)</b></span>
                </div>
                <br>

                {{-- Declaration --}}
                <div style="width: 100%; display: table;">
                    <span style="display: table-cell; width: 10px;">I </span>
                    <span style="display: table-cell; border-bottom: 1px dotted #555555;"></span>
                    <span class="text-right" style="display: table-cell; width: 320px;"> &nbsp; a Director or a person authorised by the Subcontractor</span>
                </div>
                <div class="text-justify">on whose behalf this declaration is made, hereby declare that I am in a position to know the truth of the matters which are contained in this Subcontractor’s
                    Statement and declare the following to the best of my knowledge and belief:
                </div>
                <br>

                {{-- Dot points --}}
                <ol type="a" style="padding-left:15px">
                    {{-- Note 6 --}}
                    <li class="text-justify">The abovementioned Subcontractor has either employed or engaged workers or subcontractors during the above period of this contract. Tick <b>[ &nbsp; ]</b>
                        if true and comply with <b>(b)</b> to <b>(g)</b>
                        below, as applicable. If it is not the case that workers or subcontractors are involved or you are an exempt employer for workers compensation purposes tick <b>[ &nbsp; ]</b>
                        and only
                        complete <b>(f)</b> and <b>(g)</b> below. You must tick one box. <span class="pull-right"><b>(Note 6)</b></span>
                    </li>
                    {{-- Note 7 --}}
                    {{--
                    <li class="text-justify">All workers compensation insurance premiums payable by the Subcontractor in respect of the work done under the contract have been paid. The Certificate of
                        Currency for that insurance is attached and is dated {!! ($company->activeCompanyDoc('2') && $company->activeCompanyDoc('2')->status == 1 ) ? '<b>'.$company->activeCompanyDoc('2')->expiry->format('d/m/Y').'</b>' :  '......../......../........' !!} <span class="pull-right"><b>(Note 7)</b></span>
                    </li>--}}
                    {{-- Note 8 --}}
                    <li class="text-justify">All remuneration payable to relevant employees for work under the contract for the above period has been
                        paid. <span class="pull-right"><b>(Note 8)</b></span>
                    </li>
                    {{-- Note 9 --}}
                    <li class="text-justify">Where the Subcontractor is required to be registered as an employer under the Payroll Tax Act 2007, the Subcontractor has paid all payroll tax due in
                        respect of employees who
                        performed work under the contract, as required at the date of this Subcontractor’s Statement. <span class="pull-right"><b>(Note 9)</b></span>
                    </li>
                    {{-- Note 10 --}}
                    <li class="text-justify">Where the Subcontractor is also a principal contractor in connection with the work, the Subcontractor has in its capacity of principal contractor been
                        given a written
                        Subcontractor’s Statement by its subcontractor(s) in connection with that work for the period stated above. <span class="pull-right"><b>(Note 10)</b></span>
                    </li>
                </ol>

                {{-- Signature --}}
                <div style="width: 100%; display: table;">
                    <span style="display: table-cell; width: 80px;">f. &nbsp; Signature </span>
                    <span style="display: table-cell; border-bottom: 1px dotted #555555;"></span>
                    <span class="text-right" style="display: table-cell; width: 80px;"> &nbsp; Full name &nbsp; </span>
                    <span style="display: table-cell; width: 250px; border-bottom: 1px dotted #555555;"></span>
                </div>
                <br>

                {{-- Position --}}
                <div style="width: 100%; display: table;">
                    <span style="display: table-cell; width: 110px;">g. &nbsp;Position/Title </span>
                    <span style="display: table-cell; border-bottom: 1px dotted #555555;"></span>
                    <span class="text-right" style="display: table-cell; width: 50px;"> &nbsp; Date; &nbsp;</span>
                    <span style="display: table-cell; width: 20px; border-bottom: 1px dotted #555555;"></span>
                    <span style="display: table-cell; width: 20px"> &nbsp; &nbsp;/ </span>
                    <span style="display: table-cell; width: 20px; border-bottom: 1px dotted #555555;"></span>
                    <span style="display: table-cell; width: 20px"> &nbsp; &nbsp;/ </span>
                    <span style="display: table-cell; width: 20px; border-bottom: 1px dotted #555555;"></span>
                </div>
                <br>

                {{-- Footer Note --}}
                <div class="text-justify"><b>NOTE:</b> Where required above, this Statement must be accompanied by the relevant Certificate of Currency to comply with section 175B of the Workers
                    Compensation Act 1987.
                </div>

            </div>
        </div>


    </div>

    {{-- Page 2 --}}
    <div class="page">
        <div class="row">
            <div class="col-xs-12" style="font-size:11px">
                <h5 class="text-center" style="margin-top: 0px; padding-top: 0px"><b>Notes</b></h5>
                {{-- Dot points --}}
                <ol style="padding-left:20px">
                    <li>
                        <p>This form is prepared for the purpose of section 175B of the <i>Workers Compensation Act 1987, Schedule 2 Part 5 Payroll Tax Act 2007</i> and section 127 of the <i>Industrial
                                Relation
                                Act 1996</i>. If this form is completed in accordance with these provisions, a principal contractor is relieved of liability for workers compensation premiums, payroll
                            tax and
                            remuneration payable by the subcontractor.</p>
                        <p>
                            A principal contractor can be generally defined to include any person who has entered into a contract for the carrying out of work by another person (or other legal entity
                            called <b>the subcontractor</b>) and where employees of the subcontractor are engaged in carrying out the work which is in connection with the principal contractor’s
                            business.</p>
                    </li>
                    <li>For the purpose of this Subcontractor’s Statement, a principal contractor is a person (or other legal entity), who has entered into a contract with another
                        person (or other legal entity) referred to as the subcontractor, and employees/workers of that subcontractor will perform the work under contract. The work must be connected to
                        the business undertaking of the principal contractor.
                    </li>
                    <li>Provide the unique contract number, title, or other information that identifies the contract.</li>
                    <li>
                        <p>In order to meet the requirements of s127 <i>Industrial Relations Act 1996</i>, a statement in relation to remuneration must state the period to which the
                            statement relates. For sequential Statements ensure that the dates provide continuous coverage.</p>
                        <p>Section 127(6) of the <i>Industrial Relations Act 1996 defines remuneration ‘as remuneration or other amounts payable to relevant employees by legislation, or under an
                                industrial
                                instrument, in connection with work done by the employees.’</i></p>
                        <p>Section 127(11) <i>of the Industrial Relations Act 1996 states ‘to avoid doubt, this section extends to a principal contractor who is the owner or occupier of a building for
                                the
                                carrying out of work in connection with the building so long as the building is owned or occupied by the principal contractor in connection with a business undertaking
                                of the
                                principal contractor.’</i></p>
                    </li>
                    <li>Provide the date of the most recent payment claim.</li>
                    <li>For Workers Compensation purposes an exempt employer is an employer who pays less than $7500 annually, who does not employ an apprentice or trainee and is
                        not a member of a group.
                    </li>
                    <li>In completing the Subcontractor’s Statement, a subcontractor declares that workers compensation insurance premiums payable up to and including the date(s)
                        on the Statement have been paid, and all premiums owing during the term of the contract will be paid.
                    </li>
                    <li>In completing the Subcontractor’s Statement, a subcontractor declares that all remuneration payable to relevant employees for work under the contract has
                        been paid.
                    </li>
                    <li>In completing the Subcontractor’s Statement, a subcontractor declares that all payroll tax payable relating to the work undertaken has been paid.</li>
                    <li>It is important to note that a business could be both a subcontractor and a principal contractor, if a business ‘in turn’ engages subcontractors to carry
                        out the work. If your business engages a subcontractor you are to also obtain Subcontractor’s Statements from your subcontractors.
                    </li>
                </ol>

                <h5 class="text-center"><b>Statement Retention</b></h5>
                <p>The principal contractor receiving a Subcontractor’s Statement must keep a copy of the Statement for the periods stated in the respective legislation. This is currently up to seven
                    years.</p>

                <div style="border: 1px solid; background-color:#bfbfbf; padding:10px">
                    <h5 class="text-center" style="margin: 0px;"><b>Offences in respect of a false Statement</b></h5>
                    In terms of s127(8) of the Industrial Relations Act 1996, a person who gives the principal contractor a written statement knowing it to be false is guilty of an offence if:
                    <ol type="a">
                        <li>the person is the subcontractor;</li>
                        <li>the person is authorised by the subcontractor to give the statement on behalf of the subcontractor; or</li>
                        <li>the person holds out or represents that the person is authorised by the subcontractor to give the statement on
                            behalf of the subcontractor.
                        </li>
                    </ol>
                    In terms of s175B of the <i>Workers Compensation Act</i> and clause 18 of Schedule 2 of the <i>Payroll Tax Act 2007</i> a person who gives the principal contractor a written
                    statement knowing it
                    to be false is guilty of an offence.
                </div>

                <h5 class="text-center"><b>Further Information</b></h5>
                <p>For more information, visit the WorkCover website <a>www.workcover.nsw.gov.au</a>, Office of State Revenue website <a>www.osr.nsw.gov.au</a> , or Office of Industrial Relations, Department of
                    Commerce website <a>www.commerce.nsw.gov.au</a> . Copies of the Workers Compensation Act 1987, the Payroll Tax Act 2007 and the Industrial Relations Act 1996 can be found at
                    www.legislation.nsw.gov.au.</p>
            </div>
        </div>
    </div>


</div>
</body>
</html>