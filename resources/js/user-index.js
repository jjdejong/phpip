import { reloadPart, debounce } from "./main.js";

export function initUserIndex() {
  const url = new URL(window.location.href);

  function refreshUserList() {
    window.history.pushState("", "phpIP", url);
    reloadPart(url, "userList");
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
        refreshUserList();
      }, 300),
    );
  }
}
