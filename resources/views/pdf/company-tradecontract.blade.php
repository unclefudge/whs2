<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Period Trade Contract</title>
    <link href="{{ asset('/') }}/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('/') }}/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <style>
        @import url(http://fonts.googleapis.com/css?family=PT+Sans);

        body, h1, h2, h3, h4, h5, h6 {
            font-family: 'PT Sans', serif;
        }

        h1 {
            font-weight: 700;
        }

        body {
            font-size: 11px;
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
            <div class="col-xs-12">
                <h4 style="margin: 0px">Period Trade Contract <span class="pull-right" style="font-size:18px">SCHEDULE</span></h4>
                <hr style="margin: 5px 0px 10px 0px">
            </div>
        </div>

        {{-- Schedule 1. Date --}}
        <div class="row">
            <div class="col-xs-1"><h5 style="margin: 0px">1.</h5></div>
            <div class="col-xs-11" style="padding-left:0px;">
                <h5 style="margin: 0px">Date</h5>
                <hr style="margin: 5px 0px 15px 0px">
            </div>
        </div>

        {{-- Schedule 2. Principle Contractor --}}
        <div class="row">
            <div class="col-xs-1"><h5 style="margin: 0px">2.</h5></div>
            <div class="col-xs-11" style="padding-left:0px;">
                <h5 style="margin: 0px">Principal contractor</h5>
                <hr style="margin: 5px 0px 10px 0px">
                <div class="col-xs-2">NAME</div>
                <div class="col-xs-10">{{ $company->reportsTo()->name }}</div>
                <div class="col-xs-2">ADDRESS</div>
                <div class="col-xs-10">{{ $company->reportsTo()->address }}</div>
                <div class="col-xs-2">SUBURB</div>
                <div class="col-xs-4">{{ $company->reportsTo()->suburb }}</div>
                <div class="col-xs-1">STATE</div>
                <div class="col-xs-2">{{ $company->reportsTo()->state }}</div>
                <div class="col-xs-3">POSTCODE &nbsp; &nbsp; {{ $company->reportsTo()->postcode }}</div>
                <div class="col-xs-2">ABN</div>
                <div class="col-xs-4">{{ $company->reportsTo()->abn }}</div>
                <div class="col-xs-2">&nbsp;</div>
                <div class="col-xs-4">&nbsp;</div>
                <div class="col-xs-2">PHONE</div>
                <div class="col-xs-4">{{ $company->reportsTo()->phone }}</div>
                <div class="col-xs-1">EMAIL</div>
                <div class="col-xs-5">{!! ($company->reportsTo()->id == 3) ? 'accounts1@capecode.com.au' : $company->reportsTo()->email !!}</div>
                <div class="col-xs-4">HIA MEMBER NUMBER</div>
                <div class="col-xs-6">&nbsp;</div>
            </div>
        </div>

        {{-- Schedule 3. Tade Contractor --}}
        <br>
        <div class="row">
            <div class="col-xs-1"><h5 style="margin: 0px">3.</h5></div>
            <div class="col-xs-11" style="padding-left:0px;">
                <h5 style="margin: 0px">Trade contractor</h5>
                <hr style="margin: 5px 0px 10px 0px">
                <div class="col-xs-2">NAME</div>
                <div class="col-xs-10">{{ $company->name }}</div>
                <div class="col-xs-2">ADDRESS</div>
                <div class="col-xs-10">{{ $company->address }}</div>
                <div class="col-xs-2">SUBURB</div>
                <div class="col-xs-4">{{ $company->suburb }}</div>
                <div class="col-xs-1">STATE</div>
                <div class="col-xs-2">{{ $company->state }}</div>
                <div class="col-xs-3">POSTCODE &nbsp; &nbsp; {{ $company->postcode }}</div>
                <div class="col-xs-2">ABN</div>
                <div class="col-xs-4">{{ $company->abn }}</div>
                <div class="col-xs-2">&nbsp;</div>
                <div class="col-xs-4">&nbsp;</div>
                <div class="col-xs-2">PHONE</div>
                <div class="col-xs-4">{{ $company->phone }}</div>
                <div class="col-xs-1">EMAIL</div>
                <div class="col-xs-5">{{ $company->email }}</div>
            </div>
        </div>

        {{-- Schedule 4 --}}
        <br>
        <div class="row">
            <div class="col-xs-1"><h5 style="margin: 0px">4.</h5></div>
            <div class="col-xs-11" style="padding-left:0px;">
                In consideration of:
                <ol type="a" style="padding-left:15px; margin-bottom: 5px">
                    <li>the <b>trade contractor</b> agreeing to quote for <b>trade works</b> whenever asked by the <b>principal contractor</b>, and</li>
                    <li>the <b>principal contractor</b> agreeing to pay, on demand by the <b>trade contractor</b>, the sum of $1,</li>
                </ol>
                the parties agree that the period trade contract conditions overleaf are deemed to be incorporated into each <b>trade contract</b> for a period of 12 months from the date of this agreement.
            </div>
        </div>

        {{-- Schedule 5 --}}
        <br>
        <div class="row">
            <div class="col-xs-1"><h5 style="margin: 0px">5.</h5></div>
            <div class="col-xs-11" style="padding-left:0px;">
                The <b>trade contractor</b> acknowledges and agrees that:
                <ol type="a" style="padding-left:15px; margin-bottom: 5px">
                    <li>the <b>principal contractor</b> has not made any representation, and</li>
                    <li>the <b>trade contractor</b> has not relied on any representation made by the <b>principal contractor</b>,</li>
                </ol>
                as to the availability of work or the number of work orders that will be issued by the <b>principal contractor</b>.
            </div>
        </div>

        {{-- Schedule 6 --}}
        <br>
        <div class="row">
            <div class="col-xs-1"><h5 style="margin: 0px">6.</h5></div>
            <div class="col-xs-11" style="padding-left:0px;">
                The parties agree that for each <b>trade contract</b> the scope of the <b>trade works</b>, the commencement and completion dates of the <b>trade works</b> and the price of <b>trade
                    works</b> will be set out:
                <ol type="a" style="padding-left:15px">
                    <li>in a quote from the <b>trade contractor</b> that is accepted by the <b>principal contractor</b>;</li>
                    <li>in a work order issued by the <b>principal contractor</b> that is accepted by the <b>trade contractor</b>; or</li>
                    <li>as otherwise evidenced in writing and signed by the parties.</li>
                </ol>
            </div>
        </div>

        {{-- Schedule 7 --}}
        <div class="row">
            <div class="col-xs-1"><h5 style="margin: 0px">7.</h5></div>
            <div class="col-xs-11" style="padding-left:0px;">The parties agree that this agreement does not form a contract to carry out work. The obligation to carry out work arises on the formation
                of a <b>trade contract</b>
                as described in paragraph 6 above.
            </div>
        </div>

        {{-- Schedule 8 --}}
        <br>
        <div class="row">
            <div class="col-xs-1"><h5 style="margin: 0px">8.</h5></div>
            <div class="col-xs-11" style="padding-left:0px;"><b>"Defects liability period"</b> in a <b>trade contract</b> means a period of 12 weeks from the practical completion of the work under the
                <b>head contract</b>.
            </div>
        </div>

        {{-- Schedule 9 --}}
        <br>
        <div class="row">
            <div class="col-xs-1"><h5 style="margin: 0px">9.</h5></div>
            <div class="col-xs-11" style="padding-left:0px;">
                <h5 style="margin: 0px">INFORMATION TO BE COMPLETED BY THE TRADE CONTRACTOR</h5>
                <hr style="margin: 5px 0px 10px 0px">
                <div class="col-xs-4" style="padding-left:0px; font-size:10px">LICENCE NO (if required)</div>
                <div class="col-xs-5">{!! ($company->activeCompanyDoc('7') && $company->activeCompanyDoc('7')->status == 1) ? $company->activeCompanyDoc('7')->ref_no : "&nbsp;<hr style='margin:0px'>" !!}</div>
                <div class="col-xs-3">&nbsp;</div>
            </div>
            <div class="col-xs-1">&nbsp;</div>
            <div class="col-xs-11" style="padding-left:0px; line-height:1.7em">
                <div class="col-xs-4" style="padding-left:0px; font-size:10px">PUBLIC LIABILITY INSURANCE</div>
                <div class="col-xs-8" style="padding:0px;">
                    <div class="col-xs-7">Company: {!! ($company->activeCompanyDoc('1') && $company->activeCompanyDoc('1')->status == 1) ? " &nbsp;".$company->activeCompanyDoc('1')->ref_name :
                    "&nbsp; &nbsp; <span style='border-bottom: 1px solid #e9edef;'> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </span>" !!}</div>
                    <div class="col-xs-5">Policy No: {!! ($company->activeCompanyDoc('1') && $company->activeCompanyDoc('1')->status == 1) ? " &nbsp; ".$company->activeCompanyDoc('1')->ref_no :
                    "&nbsp; &nbsp; <span style='border-bottom: 1px solid #e9edef;'>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>" !!}</div>
                    <div class="col-xs-7">Current to: {!! ($company->activeCompanyDoc('1') && $company->activeCompanyDoc('1')->status == 1) ? $company->activeCompanyDoc('1')->expiry->format('d/m/Y') :
                    "&nbsp; &nbsp;<span style='border-bottom: 1px solid #e9edef;'>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </span>" !!}</div>
                    <div class="col-xs-5">&nbsp;</div>
                </div>
            </div>
            <div class="col-xs-1">&nbsp;</div>
            <div class="col-xs-11" style="padding-left:0px; line-height:1.7em">
                <div class="col-xs-4" style="padding-left:0px; font-size:10px">WORKERS COMPENSATION INSURANCE</div> {{-- Workers Compensation Insurance --}}
                <div class="col-xs-8" style="padding:0px;">
                    <div class="col-xs-7">Company: {!! ($company->activeCompanyDoc('2') && $company->activeCompanyDoc('2')->status == 1) ? " &nbsp;".$company->activeCompanyDoc('2')->ref_name :
                    "&nbsp; &nbsp; <span style='border-bottom: 1px solid #e9edef;'> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </span>" !!}</div>
                    <div class="col-xs-5">Policy No: {!! ($company->activeCompanyDoc('2') && $company->activeCompanyDoc('2')->status == 1) ? " &nbsp; ".$company->activeCompanyDoc('2')->ref_no :
                    "&nbsp; &nbsp; <span style='border-bottom: 1px solid #e9edef;'>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>" !!}</div>
                    <div class="col-xs-7">Current to: {!! ($company->activeCompanyDoc('2') && $company->activeCompanyDoc('2')->status == 1) ? $company->activeCompanyDoc('2')->expiry->format('d/m/Y') :
                    "&nbsp; &nbsp;<span style='border-bottom: 1px solid #e9edef;'>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </span>" !!}</div>
                    <div class="col-xs-5">&nbsp;</div>
                </div>
            </div>
            <div class="col-xs-1">&nbsp;</div>
            <div class="col-xs-11" style="padding-left:0px; line-height:1.7em">
                <div class="col-xs-4" style="padding-left:0px; font-size:10px">SICKNESS & ACCIDENT INSURANCE</div> {{-- Sickness & Accident Insurance --}}
                <div class="col-xs-8" style="padding:0px;">
                    <div class="col-xs-7">Company: {!! ($company->activeCompanyDoc('3') && $company->activeCompanyDoc('3')->status == 1) ? " &nbsp;".$company->activeCompanyDoc('3')->ref_name :
                    "&nbsp; &nbsp; <span style='border-bottom: 1px solid #e9edef;'> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </span>" !!}</div>
                    <div class="col-xs-5">Policy No: {!! ($company->activeCompanyDoc('3') && $company->activeCompanyDoc('3')->status == 1) ? " &nbsp; ".$company->activeCompanyDoc('3')->ref_no :
                    "&nbsp; &nbsp; <span style='border-bottom: 1px solid #e9edef;'>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>" !!}</div>
                    <div class="col-xs-7">Current to: {!! ($company->activeCompanyDoc('3') && $company->activeCompanyDoc('3')->status == 1) ? $company->activeCompanyDoc('3')->expiry->format('d/m/Y') :
                    "&nbsp; &nbsp;<span style='border-bottom: 1px solid #e9edef;'>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </span>" !!}</div>
                    <div class="col-xs-5">&nbsp;</div>
                </div>
            </div>
            <div class="col-xs-1">&nbsp;</div>
            <div class="col-xs-11" style="padding-left:0px;">
                <div class="col-xs-4" style="padding-left:0px;"><br>ABN: &nbsp; {{ $company->abn }}</div>
                <div class="col-xs-8" style="padding-left:0px;">
                    <div class="col-xs-12"><br>ARE YOU REGISTERED FOR GST? &nbsp; @if($company->gst) Yes @elseif($company->gst == '0') No @else YES / NO @endif</div>
                </div>
            </div>
        </div>

        {{-- Signature --}}
        <br>
        <div class="row">
            <div class="col-xs-12" style="font-size:10px">
                THE PARTIES AGREE that the period trade contract conditions referred to above are those that appear on the next page (see conditions)
            </div>
            <div class="col-xs-5"><br><br><br>
                <hr style="margin: 0px">
                Principle Contractor's Signature
            </div>
            <div class="col-xs-2"></div>
            <div class="col-xs-5"><br><br><br>
                <hr style="margin: 0px">
                Trade Contractor's Signature
            </div>
        </div>
    </div>

    {{-- Page 2 --}}
    <div class="page">
        <div class="row">
            <div class="col-xs-12">
                <h4 style="margin: 0px">Period Trade Contract Conditions</h4>
                <hr style="margin: 5px 0px 5px 0px">
            </div>
        </div>
        <div class="row" style="font-size: 9px">
            {{-- Column 1 --}}
            <div class="col-xs-6">
                <h6>1. TRADE WORKS</h6>
                <ol type="a" style="padding-left:15px">
                    <li>The <b>trade contractor</b> must carry out and complete the <b> trade works</b>:
                        <ol type="i" style="padding-left:15px">
                            <li>in a proper, skillful and tradesperson like manner to the satisfaction of the <b>principal contractor</b> acting reasonably;</li>
                            <li>in accordance with the specifications and the law; and</li>
                            <li>at the reasonable times directed by the <b> principal contractor</b>.</li>

                        </ol>
                    </li>
                    <li>If the <b>trade contractor</b> discovers any inconsistency, ambiguity or discrepancy in or between the plans and the specifications, the <b>trade contractor</b> must
                        immediately seek the <b>principal contractor's</b> direction as to the interpretation to be followed.
                    </li>
                    <li>The <b>trade contractor</b> must supply everything necessary to carry out the <b>trade works</b>.</li>
                    <li>The <b>trade contractor</b> may employ or engage others to carry out some or all of the <b>trade works</b>. Use of sub-contractors does not relieve the trade contractor from liability under
                        this <b>trade contract</b>.
                    </li>
                </ol>
                <h6>2. VARIATIONS</h6>
                <ol type="a" style="padding-left:15px">
                    <li>The <b>trade contractor</b> must not:
                        <ol type="i" style="padding-left:15px">
                            <li>make any changes to the <b>trade works</b>;</li>
                            <li>carry out any extra work; or</li>
                            <li>leave any detail of the <b>trade works</b> unfinished,</li>
                        </ol>
                        unless so directed in writing by the principal contractor.
                    </li>
                    <li>The <b>principal contractor</b> may, by giving a written direction, require the <b>trade contractor</b> to carry out a variation, which is necessary for completion of the works
                        under the <b>head contract</b>.
                    </li>
                    <li>The price of a <b>variation</b> is:
                        <ol type="i" style="padding-left:15px">
                            <li>that agreed by the parties; or</li>
                            <li>failing agreement, an amount determined by the <b>principal contractor</b> acting reasonably applying reasonable trade rates and prices and, where the <b>variation</b>
                                involves additional work,
                                an allowance for the <b>trade contractor's</b> profit, overheads and administrative costs.
                            </li>
                        </ol>
                    </li>
                    <li>The contract price is to be adjusted by the price of a <b>variation</b> at the next payment.</li>
                </ol>

                <h6>3. ACCEPTANCE OF BASE WORK</h6>
                <ol type="a" style="padding-left:15px">
                    <li>Unless sub-clause 3(b) applies, on commencing to carry out the <b>trade works</b> the <b>trade contractor</b> is:
                        <ol type="i" style="padding-left:15px">
                            <li>deemed to have accepted the <b>base work</b> as satisfactory; and</li>
                            <li>is not entitled to payment or compensation for additional work carried out as a result of unsatisfactory <b>base work</b> or for conditions which differ materially from
                                those which should
                                have been observed or anticipated by a prudent, competent and experienced contractor.
                            </li>
                        </ol>
                    </li>
                    <li>On commencing to carry out the <b>trade works</b>:
                        <ol type="i" style="padding-left:15px">
                            <li>the <b>trade contractor</b> agrees and accepts the obligation to fully inspect and satisfy itself of the condition of the <b>base works</b> and all site conditions,
                                risks, contingencies and
                                other circumstances which might affect its carrying out of the <b>trade works</b>; and
                            </li>
                            <li>if the <b>trade contractor</b> considers the <b>base works</b> are unsatisfactory or conditions at the site prevent it from commencing the <b>trade works</b>, then it
                                must immediately give the <b>principal contractor</b> written notice. The <b>principal contractor</b> agrees to promptly give the <b>trade contractor</b> a written
                                instruction concerning the time for commencement of the <b>trade works</b>.
                            </li>
                        </ol>

                    </li>
                </ol>

                <h6>4. WARRANTIES</h6>
                The <b>trade contractor</b> warrants that:
                <ol type="a" style="padding-left:15px">
                    <li>the <b>trade works</b> will be carried out in a proper, skillful and tradesperson like manner and in accordance with the contract;</li>
                    <li>materials supplied by it will be suitable, neww and free of defects; and</li>
                    <li>it holds all licences required to carry out the <b>trade works</b>,</li>
                </ol>

                <h6>5. DEFECTS LIABILITY PERIOD</h6>
                <ol type="a" style="padding-left:15px">
                    <li>The <b>trade contractor</b> must, at its own cost, make good any work that does not conform with the requirements of this <b>trade contract</b> before the end of the <b>defects
                            liability period</b>.
                    </li>
                    <li>The <b>principal contractor</b> may, in writing, direct the <b>trade contractor</b> to correct, remove or replace any non-conforming work before or during the defects liability
                        period,
                    </li>
                    <li>If the <b>trade contractor</b> does not comply with such a direction, the principal contractor may have that work carried out by others and the cost is a debt due and payable
                        by the <b>trade contractor</b> to the <b>principal contractor</b>.
                    </li>
                </ol>
            </div>

            {{-- Column 2 --}}
            <div class="col-xs-6">
                <div style="padding-top: 4px">d. &nbsp; In addition to exercising other rights and remedies, the <b>principal contractor</b> may set-off such debt against a retention held and any
                    amount due or which becomes payable
                    to the <b>trade contractor</b> in connection with this <b>trade contract</b>.
                </div>

                <h6>6. INDEMNITY</h6>
                <ol type="a" style="padding-left:15px">
                    <li>The <b>trade contractor</b> indemnifies the <b>principle contractor</b> against:
                        <ol type="i" style="padding-left:15px">
                            <li>loss or damage to property (including the <b>trade works</b>);</li>
                            <li>claims in respect of personal injury or death, arising out of, connected to or as a consequence of the <b>trade contractor</b>:
                                <ol type="A" style="padding-left:15px">
                                    <li>carrying out or failing to carry out the <b>trade works</b>; or</li>
                                    <li>breaching this <b>trade contract</b>;</li>
                                </ol>
                            </li>
                            <li>any liability that the <b>principal contractor</b> may suffer or incur under a statutory warranty in connection with the <b>trade works</b>.</li>
                        </ol>
                    </li>
                    <li>The indemnity given under this clause is reduced to the extent that the loss or damage resulted from any act or omission or the <b>principal contractor</b> or their agents.
                    </li>
                </ol>

                <h6>7. INSURANCE</h6>
                <ol type="a" style="padding-left:15px">
                    <li>The <b>trade contractor</b> must take out prior to commencing, and maintain until completion of the <b>trade works</b>, the following:
                        <ol type="i" style="padding-left:15px">
                            <li>workers compensation or any like insurance as required by law;</li>
                            <li>public liability insurance to an amount not less than $5,000,000; and</li>
                            <li>except as set out below personal accident and disability insurance providing cover at least equivalent to that provided to an employee under insurance
                                referred to in sub-clause 7(a)(i).
                            </li>
                        </ol>
                    </li>
                    <li>Sub-clause 7(a)(iii) does not apply where the <b>trade contractor</b>:
                        <ol type="i" style="padding-left:15px">
                            <li>does not personally carry out any part of the <b>trade works</b> on the site; or</li>
                            <li>establishes, to the <b>principal contractor's</b> satisfaction, that it is covered by workers compensation insurance taken out by the <b>principal contractor</b>.</li>
                        </ol>
                    </li>
                    <li>The <b>trade contractor</b> must, when asked by the <b>principal contractor</b>, produce evidence of the existence and currency of any insurances.</li>
                </ol>

                <h6>8. HEALTH AND SAFETY</h6>
                <ol type="a" style="padding-left:15px">
                    <li>In carrying out the <b>trade works</b>, the <b>trade contractor</b> and its agents and employees must observe all relevant workplace health and safety laws.</li>
                    <li>The <b>trade contractor</b> must, whenever carrying out the <b>trade works</b>, ensure that:
                        <ol type="i" style="padding-left:15px">
                            <li>no person (whether employed or not) is exposed to risk to their health and safety; and</li>
                            <li>the <b>trade works</b> are carried out using an appropriate safety management system.</li>
                        </ol>
                    </li>
                </ol>

                <h6>9. DAMAGE AND SITE CLEANING</h6>
                <ol type="a" style="padding-left:15px">
                    <li>The <b>trade contractor</b> is responsible for:
                        <ol type="i" style="padding-left:15px">
                            <li>any damage caused by the <b>trade contractor</b> and its agants or employees;</li>
                            <li>keeping the <b>trade contractor's</b> areas clear and tidy at all times; and</li>
                            <li>the removal of its tools, plant and equipment, and if required the removal of debris and refuse, arising out of the <b>trade works</b>.</li>
                        </ol>
                    </li>
                    <li>If the <b>trade contractor</b> fails to comply with sub-clause 9(a) within a reasonable period after being so directed by the <b>principal contractor</b>, the <b>principal
                            contractor</b> may rectify the
                        noncompliance and this cost becomes a debt due and payable by the <b>trade contractor</b> to the <b>principal contractor</b>.
                    </li>
                </ol>

                <h6>10. PAYMENT</h6>
                <ol type="a" style="padding-left:15px">
                    <li>The <b>principal contractor</b> may require from the <b>trade contractor</b>, as a precondition to payment:
                        <ol type="i" style="padding-left:15px">
                            <li>a signed statutory declaration that all its subcontractors and employees have been paid all amounts then due for work under this <b>trade contract</b>;</li>
                            <li>an appropriate statutory declaration regarding payment of all workers compensation premiums and payroll tax in connection with the <b>trade works</b>.</li>
                        </ol>
                    </li>
                    <li>Any payment, other than a final payment, by the <b>principal contractor</b> to the <b>trade contractor</b> is payment on account only and is not evidence of the value of work
                        or that work has been
                        satisfactorily carried out.
                    </li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Page 3 --}}
    <div class="page">
        <div class="row" style="font-size: 9px">
            {{-- Column 1 --}}
            <div class="col-xs-6">
                <h6>11. EXTENSON OF TIME</h6>
                The <b>trade contractor</b> is entitled to an extension of time to complete the <b>trade works</b> as determined by the <b>principal contractor</b> acting reasonably if:
                <ol type="a" style="padding-left:15px">
                    <li>the trade works are delayed by:
                        <ol type="i" style="padding-left:15px">
                            <li>an act, default or omission of the <b>principal contractor</b> beyond the control of the <b>trade contractor</b>, or</li>
                            <li>the execution of a variation directed pursuant to sub-clause2(b); or</li>
                            <li>an unforeseeable act, event or circumstance which was beyond the control and without the fault or negligence of the <b>trade contractor</b>, which by the exercise of
                                reasonable diligence the
                                <b>trade contractor</b> was unable to prevent and for which the <b>principal contractor</b> has received an extension of time under the head contract, and
                            </li>
                        </ol>
                    </li>
                    <li>the <b>trade contractor</b> gives the <b>principal contractor</b> written notice claiming the extension of time as soon as possible but no more than 5 days of the cause
                        occurring.
                    </li>
                </ol>

                <h6>11. FREEDOM OF ASSOCATION AND COMPLIANCE WITH INDUSTRIAL LAWS</h6>
                A party or its agent must not support an industrial organisation to:
                <ol type="a" style="padding-left:15px">
                    <li>participate in any form of unauthorised industrial action or secondary boycott that affects the <b>trade works</b>, or</li>
                    <li>except as required by law, demand or force any other person carrying out work on the site to:
                        <ol type="i" style="padding-left:15px">
                            <li>join a union;</li>
                            <li>make contributions to a specified superannuation fund; or</li>
                            <li>make payments for redundancy or long service leave into a specified fund.</li>
                        </ol>
                    </li>
                </ol>

                <h6>13. DEFAULT</h6>
                <ol type="a" style="padding-left:15px">
                    A party is in default of this <b>trade contract</b> if it:
                    <li>is in substantial breach of this <b>trade contract</b>,</li>
                    <li>becomes insolvent, bankrupt or makes an assignment of that party's estate for the benefit of creditors;</li>
                    <li>makes an arrangement or composition with its creditors, or</li>
                    <li>being a company, goes into liquidation.</li>
                </ol>

                <h6>14. SUSPENSION</h6>
                <ol type="a" style="padding-left:15px">
                    <li>If work under the <b>head contract</b> has been suspended, the <b>principal contractor</b> may, by giving written notice to the <b>trade contractor</b>, immediately suspend the
                        <b>trade works</b>.
                    </li>
                    <li>If the <b>principal contractor</b> fails to make payment to the <b>trade contractor</b> as required by this <b>trade contract</b>, the <b>trade contractor</b> may suspend the
                        <b>trade works</b> where and to the extent permitted by the relevant <b>Security of Payment legislation</b>.
                    </li>
                </ol>

                <h6>15. ENDING THS TRADE CONTRACT</h6>
                If a party remains in default 3 working days after the other party has given it written notice requiring the default to be remedied then, without prejudice to any other rights or
                remedies, the
                other party may, by giving a further written notice, end this trade Contract.

                <h6>16. HEAD CONTRACT ENDED</h6>
                If the <b>head contract</b> is ended for any reason, the <b>principal contractor</b> may, by giving written notice to the <b>trade contractor</b>, end this <b>trade contract</b> and
                the <b>trade contractor</b> is:
                <ol type="a" style="padding-left:15px">
                    <li>entitled to be paid for work carried out prior and up to the date of termination plus reasonable costs incurred attributable to the termination; but</li>
                    <li>not entitled to make any claim for loss of profit.</li>
                </ol>
            </div>
            {{-- Column 2 --}}
            <div class="col-xs-6">
                <h6>17. ADMINISTRATION</h6>
                The trade contractor or its representative must:
                <ol type="a" style="padding-left:15px">
                    <li>attend site meetings if called on to do so,</li>
                    <li>observe all directions given by the <b>principal contractor</b> under this trade contract; and</li>
                    <li>co-operate with all workers and other contractors on the <b>site</b>.</li>
                </ol>

                <h6>18. INTELECTUAL PROPERTY RIGHTS</h6>
                <ol type="a" style="padding-left:15px">
                    <li><b>Intellectual property rights</b> in any plans or designs supplied by the <b>principal contractor</b> to the <b>trade contractor</b> remains with the <b>principal
                            contractor</b>.
                    </li>
                    <li>The <b>trade contractor</b> must not reproduce or use any plans or designs, in whole or in part, other than for the purpose of completing the <b>trade works</b>.</li>
                </ol>

                <h6>19. DEFINITIONS</h6>
                In this <b>trade contract</b>:<br><br>
                <p>"<b>base work</b>" means the <b>site</b> conditions including work carried out by others on or over which the <b>trade contractor</b> is to carry out the <b>trade works</b>;</p>
                <p>"<b>head contract</b>" means the contract between the <b>principal contractor</b> and its client which includes the <b>trade works</b> as part of its scope of work,</p>
                <p>"<b>intellectual property rights</b>" means any patent, registered design, trademark or name, copyright or other protected right;</p>
                <p>"<b>Security of Payment legislation</b>" means the security of payment Acts in force as at 1 October 2016 in the following Australian jurisdictions or its
                    equivalent, updated, amended or replacement legislation:
                <ol type="a" style="padding-left:15px">
                    <li>New South Wales: Building and Construction Industry Security of Payment Act 1999;</li>
                    <li>Victoria: Building and Construction industry Security of Payments Act 2002;</li>
                    <li>Queensland: Building and Construction Industry Payments Act 2004;</li>
                    <li>Western Australia: Western Construction Contracts Act 2004;</li>
                    <li>Northern Territory: Northern Territory Construction Contracts (Security of Payments) Act 2004;</li>
                    <li>Tasmania: Building and Construction Industry Security of Payment Act 2009;</li>
                    <li>South Australia: Building and Construction industry Security of Payment Act 2009;</li>
                    <li>Australian Capital Territory: Building and Construction Industry (Security of Payment) Act 2009;</li>
                </ol>
                </p>
                <p>"<b>site</b>" means the address in the Schedule where the works under the <b>head contract</b> are carried out;</p>
                <p>"<b>trade contract</b>" means this agreement between the <b>principal contractor</b> and the <b>trade contractor</b>,</p>
                <p>"<b>trade works</b>" means the work to be carried out by the <b>trade contractor</b> as described in the schedule;</p>
                <p>"<b>variation</b>" means to vary the scope of the <b>trade works</b> described in the Schedule by:
                <ol type="a" style="padding-left:15px">
                    <li>carrying out additional work;</li>
                    <li>omitting any part of the <b>trade works</b>; or</li>
                    <li>changing the scope of the <b>trade works</b>.</li>
                </ol>
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>