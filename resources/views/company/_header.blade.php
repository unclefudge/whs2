{{-- Company Header --}}
<div class="row">
    <div class="col-md-12">
        <div class="member-bar">
            <!--<i class="fa fa-user ppicon-user-member-bar" style="font-size: 80px; opacity: .5; padding:5px"></i>-->
            <i class="icon-users-member-bar hidden-xs"></i>
            <div class="member-name">
                <div class="full-name-wrap">
                    <a href="/company/{{ $company->id }}" class="status-update">{{ $company->name }}</a>
                </div>
                <span class="member-number">Company ID #{{ $company->id }}</span>
                <span class="member-split">&nbsp;|&nbsp;</span>
                <span class="member-number">{!! ($company->status == 1) ? 'ACTIVE' : '<span class="label label-sm label-danger">INACTIVE</span>' !!}</span>
                <!--<a href="/reseller/member/member_account_status/?member_id=8013759" class="member-status">Active</a>-->
            </div>

            <?php
            $url = explode(url()->current());
            $end = end($url);

            ?>
            <ul class="member-bar-menu">
                <li class="member-bar-item "><i class="icon-profile"></i><a class="member-bar-link" href="/company/{{ $company->id }}" title="Profile">PROFILE</a>{{ $end }}</li>
                <li class="member-bar-item "><i class="icon-document"></i><a class="member-bar-link" href="/company/{{ $company->id }}/doc" title="Documents">
                        <span class="hidden-xs hidden-sm">DOCUMENTS</span><span class="visible-xs visible-sm">DOCS</span></a></li>
                <li class="member-bar-item active"><i class="icon-staff"></i><a class="member-bar-link" href="/company/{{ $company->id }}/staff" title="Staff">STAFF</a></li>
            </ul>
        </div>
    </div>
</div>
