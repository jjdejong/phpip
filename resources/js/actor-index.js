/**
 * Actor Index Page Module
 *
 * Provides functionality for the actor list/index page including:
 * - Filtering by name, site, and other fields
 * - Person/company type filtering
 * - URL state management with browser history
 */

import { reloadPart, debounce } from "./main.js";

/**
 * Initializes the actor index page functionality.
 * Sets up filtering controls for the actor list.
 *
 * @returns {void}
 */
export function initActorIndex() {
  const url = new URL(window.location.href);

  /**
   * Refreshes the actor list with current filter parameters.
   * Updates URL state and reloads the actor list partial.
   *
   * @returns {void}
   */
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
