<style type="text/css">
    .card-body {
        max-height: 250px;
        overflow: auto;
    }
    .card-body .row:hover {
        background-color: #eeeeee;
    }
</style>
<script>

    function refreshTasks() {
        var url = '/home?' + $("#filter").find("input").filter(function () {
            return $(this).val().length > 0;
        }).serialize(); // Filter out empty values
        $('#tasklist').load(url + ' #tasklist > div', function () { // Refresh all the card-body's in #tasklist
            window.history.pushState('', 'phpIP', url);
        });
    }

    function refreshRenewals() {
        var url = '/home?' + $("#filter").find("input").filter(function () {
            return $(this).val().length > 0;
        }).serialize(); // Filter out empty values
        $('#renewallist').load(url + ' #renewallist > div', function () { // Refresh all the card-body in #renewallist
            window.history.pushState('', 'phpIP', url);
        });
    }

    $('#mytasks').on('change', function () {
        refreshTasks();
    });

    $('#alltasks').on('change', function () {
        refreshTasks();
    });

    $('#allrenewals').on('change', function () {
        refreshRenewals();
    });

    $('#myrenewals').on('change', function () {
        refreshRenewals();
    });

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
