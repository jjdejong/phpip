/**
 * Home Dashboard Module
 *
 * Provides functionality for the home/dashboard page including:
 * - Task list display and filtering (all tasks, my tasks, client tasks)
 * - Renewal list display
 * - Task completion/clearing functionality
 * - User dashboard view
 * - Pagination support
 */

import { fetchInto, fetchREST, reloadPart } from "./main.js";

/**
 * Stores the last selected task filter flag.
 * @type {string|number}
 */
let lastTasksFlag = 0;

/**
 * Refreshes both task and renewal lists based on the selected filter.
 *
 * @param {string|number} flag - Filter flag: 0=all tasks, 1=my tasks, 2=client tasks, or client ID
 * @returns {void}
 */
function refreshTasks(flag) {
  const urlParams = new URLSearchParams(window.location.search);
  const user_dashboard_val = urlParams.get("user_dashboard");
  lastTasksFlag = flag;
  if (flag === "2") {
    flag = clientId.value;
  }
  let url = "/task?what_tasks=" + flag;
  if (user_dashboard_val != null) {
    url += "&user_dashboard=" + user_dashboard_val;
  }
  fetchInto(url, tasklist);
  url += "&isrenewals=1";
  fetchInto(url, renewallist);

  // Refresh both panels with a single call
  const homeUrl = "/home?what_tasks=" + flag;
  reloadPart(homeUrl, "leftPanels");
}

/**
 * Initializes the home dashboard page functionality.
 * Sets up task filtering, completion handlers, and autocomplete integration.
 *
 * @returns {void}
 */
export function initHome() {
  // The filter and clearing controls are only rendered for staff roles, so
  // each of them may be missing (client users get the task lists alone)
  const allTasks = document.getElementById("alltasks");
  if (allTasks) {
    // Set default checked state for "Everyone" radio button
    allTasks.checked = true;
  }
  refreshTasks(0);

  /**
   * Event handler for client autocomplete selection.
   * Switches to client tasks view when a client is selected.
   */
  document
    .querySelector('[data-actarget="client_id"]')
    ?.addEventListener("acCompleted", () => {
      clientTasks.checked = true;
      refreshTasks("2");
    });

  filter.onchange = (e) => {
    if (e.target.name === "what_tasks") {
      refreshTasks(e.target.value);
    }
  };

  filter.onclick = (e) => {
    if (e.target.matches(".page-link")) {
      e.preventDefault();
      fetchInto(e.target.href, e.target.closest(".card-body"));
    }
  };

  const clearRenewals = document.getElementById("clearRenewals");
  const clearOpenTasks = document.getElementById("clearOpenTasks");

  if (clearRenewals) clearRenewals.onclick = () => {
    let params = new URLSearchParams();
    let list = renewallist.querySelectorAll("input:checked");
    if (list.length === 0) {
      alert("No renewals selected for clearing!");
      return;
    }
    list.forEach((current) => {
      params.append("task_ids[]", current.id);
    });
    params.append("done_date", renewalcleardate.value);
    fetchREST("/matter/clear-tasks", "POST", params).then((response) => {
      if (response.errors === "") {
        refreshTasks(lastTasksFlag);
      } else {
        alert(response.errors.done_date);
      }
    });
  };

  if (clearOpenTasks) clearOpenTasks.onclick = () => {
    let params = new URLSearchParams();
    let list = tasklist.querySelectorAll("input:checked");
    if (list.length === 0) {
      alert("No tasks selected for clearing!");
      return;
    }
    list.forEach((current) => {
      params.append("task_ids[]", current.id);
    });
    params.append("done_date", taskcleardate.value);
    fetchREST("/matter/clear-tasks", "POST", params).then((response) => {
      if (response.errors === "") {
        refreshTasks(lastTasksFlag);
      } else {
        alert(response.errors.done_date);
      }
    });
  };
}
