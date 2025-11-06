/**
 * Tables Module
 *
 * Provides common functionality for various index/list pages including:
 * - Text input filtering with debouncing
 * - Clear filter buttons
 * - URL state management with browser history
 * - Auto-refresh after modal operations
 * - Used by: rule index, event name index, role index, type index, etc.
 */

import { reloadPart, debounce } from "./main.js";

/**
 * Initializes common table filtering functionality.
 * Sets up input filters, clear buttons, and refresh triggers.
 *
 * @returns {void}
 */
export function initTables() {
  const url = new URL(window.location.href);

  // Initialize clear buttons based on current URL parameters
  const inputs = document.querySelectorAll("#filter input");
  inputs.forEach((input) => {
    const paramValue = url.searchParams.get(input.name);
    if (paramValue) {
      input.value = paramValue;
      const clearBtn = input.parentElement.querySelector(".clear-filter");
      if (clearBtn) clearBtn.style.display = "block";
    }
  });

  /**
   * Refreshes the table list with current filter parameters.
   * Updates URL state and reloads the table list partial.
   *
   * @returns {void}
   */
  function refreshList() {
    window.history.pushState("", "phpIP", url);
    reloadPart(url, "tableList");
  }

  const filterEl = document.getElementById("filter");
  if (filterEl) {
    filterEl.addEventListener(
      "input",
      debounce((e) => {
        if (e.target.value.length === 0) {
          url.searchParams.delete(e.target.name);
          // Hide clear button when input is empty
          const clearBtn =
            e.target.parentElement.querySelector(".clear-filter");
          if (clearBtn) clearBtn.style.display = "none";
        } else {
          url.searchParams.set(e.target.name, e.target.value);
          // Show clear button when input has value
          const clearBtn =
            e.target.parentElement.querySelector(".clear-filter");
          if (clearBtn) clearBtn.style.display = "block";
        }
        // Reset to page 1 when filtering
        url.searchParams.delete("page");
        refreshList();
      }, 300),
    );
  }

  // Handle clear button clicks
  document.addEventListener("click", (e) => {
    if (e.target.closest(".clear-filter")) {
      const clearBtn = e.target.closest(".clear-filter");
      const targetName = clearBtn.dataset.target;
      const input = document.querySelector(`input[name="${targetName}"]`);

      if (input) {
        input.value = "";
        clearBtn.style.display = "none";
        url.searchParams.delete(targetName);
        url.searchParams.delete("page");
        refreshList();
      }
    }
  });

  // Reload the list when closing the creation modal
  ajaxModal.addEventListener("hidden.bs.modal", function (event) {
    refreshList();
  });

  // Refresh the list when an input field of ajaxPanel is changed
  ajaxPanel.addEventListener("xhrsent", function (event) {
    refreshList();
  });
}
