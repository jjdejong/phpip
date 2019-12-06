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
        @if(Request::filled('user_dashboard'))
        url += '&user_dashboard={{ Request::get('user_dashboard') }}';
        @endif
        fetchInto(url, tasklist);
    }

    function refreshRenewals(flag) {
        var url = '/task?isrenewals=1&my_tasks=' + flag;
        @if(Request::filled('user_dashboard'))
        url += '&user_dashboard={{ Request::get('user_dashboard') }}';
        @endif
        fetchInto(url, renewallist);
    }

    @if(!Request::filled('user_dashboard'))
    mytasks.onchange = () => { refreshTasks(1); }
    alltasks.onchange = () => { refreshTasks(0); }
    allrenewals.onchange = () => { refreshRenewals(0); }
    myrenewals.onchange = () => { refreshRenewals(1); }
    @endif

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
            refreshRenewals(0);
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
            refreshTasks(0);
          } else {
            alert(response.errors.done_date);
          }
        }
      );
    }

</script>
