<div class="portlet light" id="show_swms">
    <div class="portlet-title tabbable-line">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">Site Attendance</span>
            <span class="caption-helper">(last 60 days)</span>
        </div>
    </div>
    <div class="portlet-body">
        <?php
        $now = \Carbon\Carbon::now();
        $days60 = $now->subDays(60)->toDateTimeString();
        ?>
        @if ($user->siteAttendance()->whereDate('date', '>', $days60)->count())
            <div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible1="0">
                <div class="row">
                    @foreach ($user->siteAttendance()->whereDate('date', '>', $days60)->orderBy('date', 'DESC')->get() as $attend)
                        <div class="col-xs-3">
                            <small>{{ $attend->date->format('d M h:i a') }}</small>
                        </div>
                        <div class="col-xs-9">
                            <?php $site = \App\Models\Site\Site::find($attend->site_id) ?>
                            <small>{{ $site->suburb }} - {{ $site->address }}</small>
                        </div>
                    @endforeach
                </div>
                <hr class="field-hr">
            </div>
        @elseif ($user->siteAttendance()->count())
            <?php
            $attend = $user->siteAttendance()->orderBy('date', 'DESC')->first();
            $site = \App\Models\Site\Site::find($attend->site_id);
            ?>
            <div class="row">
                <div class="col-xs-12">Last attendance was {{ $attend->date->format('d/m/Y') }} @ {{ $site->suburb }} - {{ $site->address }}</div>
            </div>
        @else
            <div class="row">
                <div class="col-xs-12">No attendance found</div>
            </div>
        @endif
    </div>
</div>
