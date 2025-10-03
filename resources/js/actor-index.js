import { reloadPart, debounce } from "./main.js";

export function initActorIndex() {
  const url = new URL(window.location.href);

  function refreshActorList() {
    window.history.pushState("", "phpIP", url);
    reloadPart(url, "actorList");
  }

  const personSelect = document.getElementById("person");
  if (personSelect) {
    personSelect.onchange = (e) => {
      if (e.target.value.length === 0) {
        url.searchParams.delete(e.target.name);
      } else {
        url.searchParams.set(e.target.name, e.target.value);
      }
      refreshActorList();
    };
  }

  const filterFieldsEl = document.getElementById("filterFields");
  if (filterFieldsEl) {
    filterFieldsEl.addEventListener(
      "input",
      debounce((e) => {
        if (e.target.value.length === 0) {
          url.searchParams.delete(e.target.name);
        } else {
          url.searchParams.set(e.target.name, e.target.value);
        }
        refreshActorList();
      }, 300),
    );
  }
}
