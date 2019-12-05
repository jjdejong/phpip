<style type="text/css">
    .card-body {
        max-height: 300px;
        min-height: 80px;
        overflow: auto;
    }
</style>
<script>

    function refreshTasks(flag) {
        var url = '/task?my_tasks=' + flag;
        fetchInto(url, tasklist);
    }

    function refreshRenewals(flag) {
        var url = '/task?renewals=1&my_tasks=' + flag;
        fetchInto(url, renewallist);
    }

    mytasks.onchange = () => { refreshTasks(1); }
    alltasks.onchange = () => { refreshTasks(0); }
    allrenewals.onchange = () => {refreshRenewals(0);}
    myrenewals.onchange = () => {refreshRenewals(1);}

    $('#clear-ren-tasks').click(function () {
        var tids = new Array();
        $('.clear-ren-task').each(function () {
            if ($(this).is(':checked'))
                tids.push($(this).attr('id'));
        });
        if (tids.length === 0) {
            alert("No tasks selected for clearing!");
            return;
        }
        $.post('/matter/clear-tasks',
                {task_ids: tids, done_date: $('#renewalcleardate').val()},
                function (response) {
                    if (response.errors === '') {
                        refreshRenewals();
                    } else {
                        alert(response.errors.done_date);
                    }
                }
        );
    });

    $('#clear-open-tasks').click(function () {
        var tids = new Array();
        $('.clear-open-task').each(function () {
            if ($(this).is(':checked'))
                tids.push($(this).attr('id'));
        });
        if (tids.length === 0) {
            alert("No tasks selected for clearing!");
            return;
        }
        $.post('/matter/clear-tasks',
                {task_ids: tids, done_date: $('#taskcleardate').val()},
                function (response) {
                    if (response.errors === '') {
                        refreshTasks();
                    } else {
                        alert(response.errors.done_date);
                    }
                }
        );
    });

</script>
