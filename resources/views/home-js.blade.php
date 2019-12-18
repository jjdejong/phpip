<style type="text/css">
    .card-body {
        max-height: 300px;
        min-height: 80px;
        overflow: auto;
    }
</style>
<script>

  var lastTasksFlag = 0;

    function refreshTasks(flag) {
        lastTasksFlag = flag;
        if (flag === '2') {
          flag = clientId.value;
        }
        var url = '/task?what_tasks=' + flag;
        @if(Request::filled('user_dashboard'))
        url += '&user_dashboard={{ Request::get('user_dashboard') }}';
        @endif
        fetchInto(url, tasklist);
        url += '&isrenewals=1';
        fetchInto(url, renewallist);
    }

    filter.onchange = (e) => {
      if (e.target.name === 'what_tasks') {
        refreshTasks(e.target.value);
      }
    }

    clearRenewals.onclick = (e) => {
      var tids = new Array();
      renewallist.querySelectorAll('input:checked').forEach( (current) => {
        tids.push(current.id);
      });
      if (tids.length === 0) {
        alert("No tasks selected for clearing!");
        return;
      }
      $.post('/matter/clear-tasks',
        { task_ids: tids, done_date: renewalcleardate.value },
        function (response) {
          if (response.errors === '') {
            refreshTasks(lastTasksFlag);
          } else {
            alert(response.errors.done_date);
          }
        }
      );
    }

    clearOpenTasks.onclick = (e) => {
      var tids = new Array();
      tasklist.querySelectorAll('input:checked').forEach( (current) => {
        tids.push(current.id);
      });
      if (tids.length === 0) {
        alert("No tasks selected for clearing!");
        return;
      }
      $.post('/matter/clear-tasks',
        { task_ids: tids, done_date: taskcleardate.value },
        function (response) {
          if (response.errors === '') {
            refreshTasks(lastTasksFlag);
          } else {
            alert(response.errors.done_date);
          }
        }
      );
    }

</script>
