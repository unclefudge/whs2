{{-- Company Header --}}
<div class="row">
    <div class="col-md-12">
        <div class="member-bar">
            <!--<i class="fa fa-user ppicon-user-member-bar" style="font-size: 80px; opacity: .5; padding:5px"></i>-->
            <i class="icon-users-member-bar hidden-xs"></i>
            <div class="member-name">
                <div class="full-name-wrap">{{ $company->name }}</div>
                <span class="member-number">Company ID #{{ $company->id }}</span>
                <span class="member-split">&nbsp;|&nbsp;</span>
                <span class="member-number">{!! ($company->status == 1) ? 'ACTIVE' : '<span class="label label-sm label-danger">INACTIVE</span>' !!}</span>
                <!--<a href="/reseller/member/member_account_status/?member_id=8013759" class="member-status">Active</a>-->
            </div>

            <?php
            $active_profile = $active_doc = $active_user = '';
            list($first, $rest) = explode('/', Request::path(), 2);
            if (!ctype_digit($rest)) {
                list($cid, $rest) = explode('/', $rest, 2);
                $active_doc = (preg_match('/^doc*/', $rest)) ? 'active' : '';
                $active_user = (preg_match('/^user*/', $rest)) ? 'active' : '';
            } else
                $active_profile = 'active';
            ?>
            <ul class="member-bar-menu">
                <li class="member-bar-item {{ $active_profile }}"><i class="icon-profile"></i><a class="member-bar-link" href="/company/{{ $company->id }}" title="Profile">PROFILE</a></li>
                @if (Auth::user()->companyDocTypeSelect('view', $company, 'all'))
                    <li class="member-bar-item {{ $active_doc }}"><i class="icon-document"></i><a class="member-bar-link" href="/company/{{ $company->id }}/doc" title="Documents">
                        <span class="hidden-xs hidden-sm">DOCUMENTS</span><span class="visible-xs visible-sm">DOCS</span></a></li>
                @endif
                @if (Auth::user()->permissionLevel('view.user', Auth::user()->company_id) || Auth::user()->permissionLevel('edit.user', Auth::user()->company_id))
                    <li class="member-bar-item {{ $active_user }}"><i class="icon-staff"></i><a class="member-bar-link" href="/company/{{ $company->id }}/user" title="Users">USERS</a></li>
                @endif
            </ul>
        </div>
    </div>
</div>
