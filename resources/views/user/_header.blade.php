{{-- Company Header --}}
<div class="row">
    <div class="col-md-12">
        <div class="member-bar">
            <!--<i class="fa fa-user ppicon-user-member-bar" style="font-size: 80px; opacity: .5; padding:5px"></i>-->
            <i class="icon-user-member-bar hidden-xs"></i>
            <div class="member-name">
                <div class="full-name-wrap">{{ $user->full_name }}</div>
                <span class="member-number">User ID #{{ $user->id }}</span>
                <span class="member-split">&nbsp;|&nbsp;</span>
                <span class="member-number">{!! ($user->status == 1) ? 'ACTIVE' : '<span class="label label-sm label-danger">INACTIVE</span>' !!}</span>
                <!--<a href="/reseller/member/member_account_status/?member_id=8013759" class="member-status">Active</a>-->
            </div>

            <?php
            $active_profile = $active_doc = $active_security = '';
            list($first, $rest) = explode('/', Request::path(), 2);
            if (!ctype_digit($rest)) {
                list($uid, $rest) = explode('/', $rest, 2);
                $active_doc = (preg_match('/^doc*/', $rest)) ? 'active' : '';
                $active_security = (preg_match('/^security*/', $rest)) ? 'active' : '';
            } else
                $active_profile = 'active';
            ?>
            <ul class="member-bar-menu">
                <li class="member-bar-item {{ $active_profile }}"><i class="icon-profile"></i><a class="member-bar-link" href="/user/{{ $user->id }}" title="Profile">PROFILE</a></li>
                <li class="member-bar-item {{ $active_doc }}"><i class="icon-document"></i><a class="member-bar-link" href="/user/{{ $user->id }}/doc" title="Documents">
                        <span class="hidden-xs hidden-sm">DOCUMENTS</span><span class="visible-xs visible-sm">DOCS</span></a></li>
                @if (Auth::user()->allowed2('edit.user', $user) && Auth::user()->company->status == 1)
                    <li class="member-bar-item {{ $active_security }}"><i class="icon-lock"></i><a class="member-bar-link" href="/user/{{ $user->id }}/security" title="Security">SECURITY</a></li>
                @endif
            </ul>
        </div>
    </div>
</div>
