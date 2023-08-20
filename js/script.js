$(function() {
    // actions (edit and delete) on the activities table
    $('td.action').click(function() {
        var action = $(this).attr('class').split(' ');
        var dataId = $(this).attr('row_id');

        if(action[1] == 'edit' || action[1] == 'delete') {
            $('.action-window p.error').css('display', 'none')
            $('.action-window b').html('');
            $('.action-window b').html(action[1]);
            $('.action-window > div').css('display', 'none');
            $('.action-window .'+action[1]).css('display', 'block');
            $('.action-window .btns').css('display', 'block');
            $('.action-window .'+action[1]+' input[type="hidden"]').attr('value', dataId);

            if(action[1] == 'edit') {
                $('.action-window .edit input[type="text"]').val($(this).prev().html());
            } else if(action[1] == 'delete') {
                $('.action-window .delete p span').html(dataId);
            }
        }
    })

    // reset action window
    $('input[type="reset"]').click(function() {
        location.reload(true);
    })
})