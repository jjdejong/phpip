var url = new URL(window.location.href);



function refreshMatterList() {
  url.searchParams.delete('page');
  window.history.pushState('', 'phpIP', url);
  reloadPart(url, 'matterList');
}

filterFields.onclick = e => {
  if (e.target.matches('.sortable')) {
    for (elt of filterFields.getElementsByClassName('sortable')) {
      elt.classList.remove('active');
      elt.innerHTML = '&UpDownArrow;';
    }
    e.target.classList.add('active');
    url.searchParams.set('sortkey', e.target.dataset.sortkey);
    url.searchParams.set('sortdir', e.target.dataset.sortdir);
    if (e.target.dataset.sortdir === 'asc') {
      e.target.dataset.sortdir = 'desc';
      e.target.innerHTML = '&uarr;';
    } else {
      e.target.dataset.sortdir = 'asc';
      e.target.innerHTML = '&darr;';
    }
    refreshMatterList();
  }
}

filterButtons.onclick = e => {
  switch (e.target.id) {
    case 'showStatus':
      for (td of document.getElementsByClassName('tab1')) {
        td.classList.remove('d-none');
      }
      for (td of document.getElementsByClassName('tab0')) {
        td.classList.add('d-none');
      }
      url.searchParams.set('tab', '1');
      window.history.pushState('', 'phpIP', url);
      break;
    case 'showActors':
      for (td of document.getElementsByClassName('tab0')) {
        td.classList.remove('d-none');
      }
      for (td of document.getElementsByClassName('tab1')) {
        td.classList.add('d-none');
      }
      url.searchParams.set('tab', '0');
      window.history.pushState('', 'phpIP', url);
      break;
    case 'showContainers':
      if (url.searchParams.has('Ctnr')) {
        url.searchParams.delete('Ctnr');
      } else {
        url.searchParams.set('Ctnr', '1');
      }
      refreshMatterList();
      break;
    case 'showResponsible':
      if (url.searchParams.has('responsible')) {
        url.searchParams.delete('responsible');
      } else {
        url.searchParams.set('responsible', e.target.dataset.responsible);
      }
      refreshMatterList();
      break;
    case 'includeDead':
      if (url.searchParams.has('include_dead')) {
        url.searchParams.delete('include_dead');
      } else {
        url.searchParams.set('include_dead', '1');
      }
      refreshMatterList();
      break;
  }
}

exportList.onclick = e => {
  let exportUrl = '/matter/export' + url.search;
  e.preventDefault(); //stop the browser from following
  window.location.href = exportUrl;
};

filterFields.addEventListener('input', debounce(e => {
  if (e.target.value.length === 0) {
    url.searchParams.delete(e.target.name);
  } else {
    url.searchParams.set(e.target.name, e.target.value);
  }
  refreshMatterList();
}, 500));

clearFilters.onclick = () => {
  window.location.href = '/matter';
};
