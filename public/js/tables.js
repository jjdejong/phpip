var url = new URL(window.location.href);

function refreshList() {
  window.history.pushState('', 'phpIP', url)
  reloadPart(url, 'tableList');
}

filter.addEventListener('input', debounce( e => {
  if (e.target.value.length === 0) {
    url.searchParams.delete(e.target.name);
  } else {
    url.searchParams.set(e.target.name, e.target.value);
  }
  refreshList();
}, 300));

// Reload the list when closing the modal window
$("#ajaxModal").on("hidden.bs.modal", function (event) {
  refreshList();
});
