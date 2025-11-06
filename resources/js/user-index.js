/**
 * User Index Page Module
 *
 * Provides functionality for the user list/index page including:
 * - Filtering by login name and display name
 * - URL state management with browser history
 */

import { reloadPart, debounce } from "./main.js";

/**
 * Initializes the user index page functionality.
 * Sets up filtering controls for the user list.
 *
 * @returns {void}
 */
export function initUserIndex() {
  const url = new URL(window.location.href);

  /**
   * Refreshes the user list with current filter parameters.
   * Updates URL state and reloads the user list partial.
   *
   * @returns {void}
   */
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
