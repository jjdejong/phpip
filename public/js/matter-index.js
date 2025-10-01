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

filterButtons.onchange = e => {
  // Alpine manages visual state, we just handle URL params and refresh
  if (e.target.name === 'Ctnr') {
    if (e.target.checked) {
      url.searchParams.set('Ctnr', '1');
    } else {
      url.searchParams.delete('Ctnr');
    }
    refreshMatterList();
  } else if (e.target.name === 'responsible') {
    if (e.target.checked) {
      const label = document.querySelector('label[for="btnshowmine"]');
      url.searchParams.set('responsible', label.dataset.responsible);
    } else {
      url.searchParams.delete('responsible');
    }
    refreshMatterList();
  } else if (e.target.name === 'include_dead') {
    if (e.target.checked) {
      url.searchParams.set('include_dead', '1');
    } else {
      url.searchParams.delete('include_dead');
    }
    refreshMatterList();
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
