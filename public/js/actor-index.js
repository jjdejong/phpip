var url = new URL(window.location.href);

function refreshActorList() {
  window.history.pushState('', 'phpIP', url)
  reloadPart(url, 'actorList');
}

person.onchange = (e) => {
  if (e.target.value.length === 0) {
    url.searchParams.delete(e.target.name);
  } else {
    url.searchParams.set(e.target.name, e.target.value);
  }
  refreshActorList();
}

filterFields.addEventListener('input', debounce( e => {
  if (e.target.value.length === 0) {
    url.searchParams.delete(e.target.name);
  } else {
    url.searchParams.set(e.target.name, e.target.value);
  }
  refreshActorList();
}, 300));