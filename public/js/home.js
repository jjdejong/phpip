  var lastTasksFlag = 0;

  window.onload = refreshTasks(0);

  function refreshTasks(flag) {
    var urlParams = new URLSearchParams(window.location.search);
    var user_dashboard_val = urlParams.get('user_dashboard');
    lastTasksFlag = flag;
    if (flag === '2') {
      flag = clientId.value;
    }
    var url = '/task?what_tasks=' + flag;
    if (user_dashboard_val != null) {
      url += '&user_dashboard=' + user_dashboard_val;
    }
    fetchInto(url, tasklist);
    url += '&isrenewals=1';
    fetchInto(url, renewallist);
  }

  filter.onchange = (e) => {
    if (e.target.name === 'what_tasks') {
      refreshTasks(e.target.value);
    }
  }

  filter.onclick = (e) => {
    if (e.target.matches('.page-link')) {
      e.preventDefault();
      fetchInto(e.target.href, e.target.closest('.card-body'));
    }
  }

  clearRenewals.onclick = (e) => {
    let params = new URLSearchParams();
    let list = renewallist.querySelectorAll('input:checked');
    if (list.length === 0) {
      alert('{{ _i("No renewals selected for clearing!") }}');
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
      });
  }

  clearOpenTasks.onclick = (e) => {
    let params = new URLSearchParams();
    let list = tasklist.querySelectorAll('input:checked');
    if (list.length === 0) {
      alert('{{ _i("No tasks selected for clearing!") }}');
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
      });
  }
