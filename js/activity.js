$(function() {
    // actions (edit and delete) on the activity table
    $('td.action').click(function() {
        var action = $(this).attr('class').split(' ');
        var dataId = $(this).attr('row_id');
        var dataTimeSpent = $(this).attr('row_time');
        var dataDay = $(this).attr('row_day');

        if(action[1] == 'edit' || action[1] == 'delete') {
            $('.action-window p.error').css('display', 'none');
            $('.action-window b').html('');
            $('.action-window b').html(action[1]);
            $('.action-window > div').css('display', 'none');
            $('.action-window .'+action[1]).css('display', 'block');
            $('.action-window .btns').css('display', 'block');
            $('.action-window .'+action[1]+' input[name="data-id"]').attr('value', dataId);

            if(action[1] == 'edit') {
                $('.action-window .edit input[name="data-time"]').attr('value', dataTimeSpent);
                $('.action-window .edit input[name="data-day"]').attr('value', dataDay);

                $('.action-window .edit input[name="time"]').val(dataTimeSpent);
                $('.action-window .edit input[name="day"]').val(dataDay)
            } else if(action[1] == 'delete') {
                $('.action-window .delete p span').html(dataId);
            }
        }
    })

    // reset action window
    $('input[type="reset"]').click(function() {
        location.reload(true);
    })

    // show and hide columns
    showId = $('input[name="show_id"]').val();
    showTime = $('input[name="show_time"]').val();
    showDay = $('input[name="show_day"]').val();

    if(showId == 0) {
        $('th').eq(0).hide();
        for(i = 0; i < $('tr').length; i++) {
            $('tr').eq(i).find('td').eq(0).hide();
        }
    }
    if(showTime == 0) {
        $('th').eq(1).hide();
        for(i = 0; i < $('tr').length; i++) {
            $('tr').eq(i).find('td').eq(1).hide();
        }
    }
    if(showDay == 0) {
        $('th').eq(2).hide();
        for(i = 0; i < $('tr').length; i++) {
            $('tr').eq(i).find('td').eq(2).hide();
        }
    }

    // show and hide data
    var rotation = 0;
    var cookies = document.cookie.replace(/ /g, '').split(';');
    var hiddenCookie, hiddenCookieIndex;
    for(i = 0; i < cookies.length; i++) {
        if(cookies[i].split('=')[0] == 'hidden') {
            hiddenCookieIndex = i;
            hiddenCookie = cookies[i].split('=')[1];
            if(hiddenCookie == 'true') {
                $('tr').hide();
                $('tr').eq(0).show();
                rotation = 180;
                $('img.arrow').css('transform', 'rotate('+rotation+'deg)');
            } else {
                $('tr').show();
            }
        }
    }
    
    $('img.arrow').click(function() {
        rotation = (rotation == 0) ? 180 : 0;
        $(this).css('transform', 'rotate('+rotation+'deg)');
        if(hiddenCookie == 'true') {
            $('tr').show();
            document.cookie = "hidden=false";
        } else {
            $('tr').hide();
            $('tr').eq(0).show();
            document.cookie = "hidden=true";
        }
        cookies = document.cookie.replace(/ /g, '').split(';');
        hiddenCookie = cookies[hiddenCookieIndex].split('=')[1]
    });
})