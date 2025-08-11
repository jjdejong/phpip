var lastTasksFlag = 0;

window.onload = function() {
  // Set default checked state for "Everyone" radio button
  document.getElementById('alltasks').checked = true;
  updateRadioButtonVisualState();
  refreshTasks(0);
};

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

  // Refresh both panels with a single call
  var homeUrl = '/home?what_tasks=' + flag;
  reloadPart(homeUrl, 'leftPanels');
}

function updateRadioButtonVisualState() {
  // Remove active state from all radio button labels
  document.querySelectorAll('label.btn').forEach(label => {
    const radio = label.querySelector('input[name="what_tasks"]');
    if (radio) {
      label.classList.remove('active');
    }
  });
  
  // Add active state to the checked radio button's label
  const checkedRadio = document.querySelector('input[name="what_tasks"]:checked');
  if (checkedRadio) {
    const label = checkedRadio.closest('label.btn');
    if (label) {
      label.classList.add('active');
    }
  }
}

// When client is selected via autocomplete, check the Client radio button and refresh
document.querySelector('[data-actarget="client_id"]').addEventListener('acCompleted', () => {
  clientTasks.checked = true;
  updateRadioButtonVisualState();
  refreshTasks('2');
});

filter.onchange = (e) => {
  if (e.target.name === 'what_tasks') {
    updateRadioButtonVisualState();
    refreshTasks(e.target.value);
  }
}

filter.onclick = (e) => {
  if (e.target.matches('.page-link')) {
    e.preventDefault();
    fetchInto(e.target.href, e.target.closest('.card-body'));
  }
}

clearRenewals.onclick = () => {
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
    });
}

clearOpenTasks.onclick = () => {
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
    });
}
