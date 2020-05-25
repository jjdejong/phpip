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
      let params = new URLSearchParams();
      let list = renewallist.querySelectorAll('input:checked');
      if (list.length === 0) {
        alert("No renewals selected for clearing!");
        return;
      }
      list.forEach((current) => {
        params.append('task_ids[]', current.id);
      });
      params.append('done_date', renewalcleardate.value);
      fetchREST('/matter/clear-tasks', 'POST', params)
      .then((response) => {
          if (response.errors === '') {
            refreshTasks(lastTasksFlag);
          } else {
            alert(response.errors.done_date);
          }
        }
      );
    }

    clearOpenTasks.onclick = (e) => {
      let params = new URLSearchParams();
      let list = tasklist.querySelectorAll('input:checked');
      if (list.length === 0) {
        alert("No tasks selected for clearing!");
        return;
      }
      list.forEach((current) => {
        params.append('task_ids[]', current.id);
      });
      params.append('done_date', taskcleardate.value);
      fetchREST('/matter/clear-tasks', 'POST', params)
      .then((response) => {
          if (response.errors === '') {
            refreshTasks(lastTasksFlag);
          } else {
            alert(response.errors.done_date);
          }
        }
      );
    }

</script>
