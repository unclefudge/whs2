<!DOCTYPE html>
<html>
<head>
    <script class="jsbin" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <meta charset=utf-8 />
    <title>JS Bin</title>
    <!--[if IE]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <style>
        article, aside, figure, footer, header, hgroup,
        menu, nav, section { display: block; }
        #timesheet-events {
        .position: absolute;
            top: 0;
            left: 36px;
            right: 0;
            bottom: 0;
        }
        .bubble_selector {
            overflow: hidden;
            cursor: pointer;
            font-size: 11px;
            line-height: 140%;
            position: absolute;
            background: transparent;
        }
        .daysheet-container {
            border: 1px solid black;
        }
        .bubble-body {
            overflow: hidden;
            cursor: pointer;
            font-size: 11px;
            line-height: 140%;
            position: absolute;
        }
        .daysheet-container {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 14.28%;
        }
        .bubble-body {
            overflow: hidden;
            position: relative;
            width: 100%;
            height: 100%;
            background: none transparent;
        }
        .bubble-frame {
            background: none transparent;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 1px;
            z-index: 10;
            border: 1px solid black;
        }
        .bubble-back {
            opacity: 0.4;
            filter: alpha(opacity=40);
            position: absolute;
            top: 1px;
            left: 0px;
            right: 0px;
            bottom: 2px;
            z-index: 10;
        }
    </style>
</head>
<body>
<div id="timesheet-events">
    <!-- Day 1 -->
    <div class="daysheet-container" style="left: 0%;">
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 28.662420382166px; left: 0px; top: 0px; width: 28px; ">
                <div class="bubble-body">
                    <div style="border-color: #0000aa !important;" class="bubble-frame"></div>
                    <div style="background-color: #0000aa !important; border-color: #0000aa !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 19.108280254777px; top: 318.28571428571px; width: 28px; left: 28px; ">
                <div class="bubble-body">
                    <div style="border-color: #0000aa !important;" class="bubble-frame"></div>
                    <div style="background-color: #0000aa !important; border-color: #0000aa !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 19.108280254777px; top: 365.7619047619px; width: 28px; left: 56px; ">
                <div class="bubble-body">
                    <div style="border-color: #0000aa !important;" class="bubble-frame"></div>
                    <div style="background-color: #0000aa !important; border-color: #0000aa !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 444.26751592357px; top: 0px; width: 28px; left: 84px; ">
                <div class="bubble-body">
                    <div style="border-color: #992200 !important;" class="bubble-frame"></div>
                    <div style="background-color: #992200 !important; border-color: #992200 !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Day 2 -->
    <div class="daysheet-container" style="left: 14.2%;">
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 85.987261146497px; left: 0px; top: 270.7619047619px; width: 114px; ">
                <div class="bubble-body">
                    <div style="border-color: #992200 !important;" class="bubble-frame"></div>
                    <div style="background-color: #992200 !important; border-color: #992200 !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Day 3 -->
    <div class="daysheet-container" style="left: 28.4%;">
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 300px; left: 0px; top: 70px; width: 114px; ">
                <div class="bubble-body">
                    <div style="border-color: #992200 !important;" class="bubble-frame"></div>
                    <div style="background-color: #992200 !important; border-color: #992200 !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 50px; left: 0px; top: 100px; width: 114px; ">
                <div class="bubble-body">
                    <div style="border-color: #992200 !important;" class="bubble-frame"></div>
                    <div style="background-color: #992200 !important; border-color: #992200 !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 50px; left: 0px; top: 100px; width: 114px; ">
                <div class="bubble-body">
                    <div style="border-color: #992200 !important;" class="bubble-frame"></div>
                    <div style="background-color: #992200 !important; border-color: #992200 !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 50px; left: 0px; top: 100px; width: 114px; ">
                <div class="bubble-body">
                    <div style="border-color: #992200 !important;" class="bubble-frame"></div>
                    <div style="background-color: #992200 !important; border-color: #992200 !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 50px; left: 0px; top: 200px; width: 114px; ">
                <div class="bubble-body">
                    <div style="border-color: #992200 !important;" class="bubble-frame"></div>
                    <div style="background-color: #992200 !important; border-color: #992200 !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 50px; left: 0px; top: 200px; width: 114px; ">
                <div class="bubble-body">
                    <div style="border-color: #992200 !important;" class="bubble-frame"></div>
                    <div style="background-color: #992200 !important; border-color: #992200 !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 50px; left: 0px; top: 300px; width: 114px; ">
                <div class="bubble-body">
                    <div style="border-color: #992200 !important;" class="bubble-frame"></div>
                    <div style="background-color: #992200 !important; border-color: #992200 !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Day 4 -->
    <div class="daysheet-container" style="left: 42.6%;">
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 19.108280254777px; left: 0px; top: 261.28571428571px; width: 11px; ">
                <div class="bubble-body">
                    <div style="border-color: #992200 !important;" class="bubble-frame"></div>
                    <div style="background-color: #992200 !important; border-color: #992200 !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 19.108280254777px; top: 304px; width: 11px; left: 11px; ">
                <div class="bubble-body">
                    <div style="border-color: #0000aa !important;" class="bubble-frame"></div>
                    <div style="background-color: #0000aa !important; border-color: #0000aa !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 19.108280254777px; top: 308.7619047619px; width: 11px; left: 22px; ">
                <div class="bubble-body">
                    <div style="border-color: #0000aa !important;" class="bubble-frame"></div>
                    <div style="background-color: #0000aa !important; border-color: #0000aa !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 19.108280254777px; top: 323px; width: 11px; left: 33px; ">
                <div class="bubble-body">
                    <div style="border-color: #0000aa !important;" class="bubble-frame"></div>
                    <div style="background-color: #0000aa !important; border-color: #0000aa !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 19.108280254777px; top: 323px; width: 11px; left: 44px; ">
                <div class="bubble-body">
                    <div style="border-color: #0000aa !important;" class="bubble-frame"></div>
                    <div style="background-color: #0000aa !important; border-color: #0000aa !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 19.108280254777px; top: 332.52380952381px; width: 11px; left: 55px; ">
                <div class="bubble-body">
                    <div style="border-color: #0000aa !important;" class="bubble-frame"></div>
                    <div style="background-color: #0000aa !important; border-color: #0000aa !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 19.108280254777px; top: 332.52380952381px; width: 11px; left: 66px; ">
                <div class="bubble-body">
                    <div style="border-color: #0000aa !important;" class="bubble-frame"></div>
                    <div style="background-color: #0000aa !important; border-color: #0000aa !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 19.108280254777px; top: 370.52380952381px; width: 11px; left: 77px; ">
                <div class="bubble-body">
                    <div style="border-color: #0000aa !important;" class="bubble-frame"></div>
                    <div style="background-color: #0000aa !important; border-color: #0000aa !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 19.108280254777px; top: 380px; width: 11px; left: 88px; ">
                <div class="bubble-body">
                    <div style="border-color: #0000aa !important;" class="bubble-frame"></div>
                    <div style="background-color: #0000aa !important; border-color: #0000aa !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 19.108280254777px; top: 384.7619047619px; width: 11px; left: 99px; ">
                <div class="bubble-body">
                    <div style="border-color: #0000aa !important;" class="bubble-frame"></div>
                    <div style="background-color: #0000aa !important; border-color: #0000aa !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Day 5 -->
    <div class="daysheet-container" style="left: 56.8%;">
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 19.108280254777px; left: 9px; top: 356.28571428571px; width: 28px; ">
                <div class="bubble-body">
                    <div style="border-color: #992200 !important;" class="bubble-frame"></div>
                    <div style="background-color: #992200 !important; border-color: #992200 !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 19.108280254777px; top: 375.28571428571px; width: 28px; left: 28px; ">
                <div class="bubble-body">
                    <div style="border-color: #992200 !important;" class="bubble-frame"></div>
                    <div style="background-color: #992200 !important; border-color: #992200 !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 19.108280254777px; top: 403.7619047619px; width: 28px; left: 56px; ">
                <div class="bubble-body">
                    <div style="border-color: #0000aa !important;" class="bubble-frame"></div>
                    <div style="background-color: #0000aa !important; border-color: #0000aa !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 315.28662420382px; top: 142.52380952381px; width: 28px; left: 84px; ">
                <div class="bubble-body">
                    <div style="border-color: #992200 !important;" class="bubble-frame"></div>
                    <div style="background-color: #992200 !important; border-color: #992200 !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Day 6 -->
    <div class="daysheet-container" style="left: 71%;">
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 458.59872611465px; left: 0px; top: 0px; width: 114px; ">
                <div class="bubble-body">
                    <div style="border-color: #992200 !important;" class="bubble-frame"></div>
                    <div style="background-color: #992200 !important; border-color: #992200 !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Day 7 -->
    <div class="daysheet-container" style="left: 85.2%;">
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 19.108280254777px; left: 9px; top: 327.7619047619px; width: 57px; ">
                <div class="bubble-body">
                    <div style="border-color: #992200 !important;" class="bubble-frame"></div>
                    <div style="background-color: #992200 !important; border-color: #992200 !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
        <div class="bubble-activator">
            <div class="bubble_selector" style="height: 458.59872611465px; top: 0px; width: 57px; left: 57px; ">
                <div class="bubble-body">
                    <div style="border-color: #992200 !important;" class="bubble-frame"></div>
                    <div style="background-color: #992200 !important; border-color: #992200 !important;" class="bubble-back"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<script>
    // Based on algorithm described here: http://stackoverflow.com/questions/11311410
    $( document ).ready( function( ) {
        var column_index = 0;
        $( '#timesheet-events .daysheet-container' ).each( function() {

            var block_width = $(this).width();
            var columns = [];
            var lastEventEnding = null;

            // Create an array of all events
            var events = $('.bubble_selector', this).map(function(index, o) {
                o = $(o);
                var top = o.offset().top;
                return {
                    'obj': o,
                    'top': top,
                    'bottom': top + o.height()
                };
            }).get();

            // Sort it by starting time, and then by ending time.
            events = events.sort(function(e1,e2) {
                if (e1.top < e2.top) return -1;
                if (e1.top > e2.top) return 1;
                if (e1.bottom < e2.bottom) return -1;
                if (e1.bottom > e2.bottom) return 1;
                return 0;
            });

            // Iterate over the sorted array
            $(events).each(function(index, e) {

                // Check if a new event group needs to be started
                if (lastEventEnding !== null && e.top >= lastEventEnding) {
                    // The latest event is later than any of the event in the
                    // current group. There is no overlap. Output the current
                    // event group and start a new event group.
                    PackEvents( columns, block_width );
                    columns = [];  // This starts new event group.
                    lastEventEnding = null;
                }

                // Try to place the event inside the existing columns
                var placed = false;
                for (var i = 0; i < columns.length; i++) {
                    var col = columns[ i ];
                    if (!collidesWith( col[col.length-1], e ) ) {
                        col.push(e);
                        placed = true;
                        break;
                    }
                }

                // It was not possible to place the event. Add a new column
                // for the current event group.
                if (!placed) {
                    columns.push([e]);
                }

                // Remember the latest event end time of the current group.
                // This is later used to determine if a new groups starts.
                if (lastEventEnding === null || e.bottom > lastEventEnding) {
                    lastEventEnding = e.bottom;
                }
            });

            if (columns.length > 0) {
                PackEvents( columns, block_width );
            }
        });
    });


    // Function does the layout for a group of events.
    function PackEvents( columns, block_width )
    {
        var n = columns.length;
        for (var i = 0; i < n; i++) {
            var col = columns[ i ];
            for (var j = 0; j < col.length; j++)
            {
                var bubble = col[j];
                var colSpan = ExpandEvent(bubble, i, columns);
                bubble.obj.css( 'left', (i / n)*100 + '%' );
                bubble.obj.css( 'width', block_width * colSpan / n - 1 );
            }
        }
    }

    // Check if two events collide.
    function collidesWith( a, b )
    {
        return a.bottom > b.top && a.top < b.bottom;
    }

    // Expand events at the far right to use up any remaining space.
    // Checks how many columns the event can expand into, without
    // colliding with other events. Step 5 in the algorithm.
    function ExpandEvent(ev, iColumn, columns)
    {
        var colSpan = 1;

        // To see the output without event expansion, uncomment
        // the line below. Watch column 3 in the output.
        //return colSpan;

        for (var i = iColumn + 1; i < columns.length; i++)
        {
            var col = columns[i];
            for (var j = 0; j < col.length; j++)
            {
                var ev1 = col[j];
                if (collidesWith(ev, ev1))
                {
                    return colSpan;
                }
            }
            colSpan++;
        }
        return colSpan;
    }
</script>