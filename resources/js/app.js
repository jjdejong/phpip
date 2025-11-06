/**
 * Application Entry Point
 *
 * This module serves as the main entry point for the phpIP application.
 * It handles:
 * - Bootstrap and styling imports
 * - Alpine.js initialization and component registration
 * - Page-specific module initialization based on DOM elements
 * - Global function exposure for use in Alpine.js and inline handlers
 *
 * Automatically detects which page is loaded and initializes appropriate modules.
 */

import "./bootstrap";
import "../sass/app.scss";

// Import Alpine.js
import Alpine from "alpinejs";
window.Alpine = Alpine;

// Import main functionality
import {
  initMain,
  fetchInto,
  fetchREST,
  reloadPart,
  debounce,
  submitModalForm,
  processSubmitErrors,
} from "./main.js";
import { initHome } from "./home.js";
import { initMatterShow, registerImageUpload } from "./matter-show.js";
import { initTables } from "./tables.js";
import { initMatterIndex } from "./matter-index.js";
import { initRenewalIndex } from "./renewal-index.js";
import { initActorIndex } from "./actor-index.js";
import { initUserIndex } from "./user-index.js";

// Register Alpine components before starting Alpine
registerImageUpload();

Alpine.start();

// Expose utility functions globally for Alpine.js and inline handlers
window.fetchInto = fetchInto;
window.fetchREST = fetchREST;
window.reloadPart = reloadPart;
window.debounce = debounce;
window.submitModalForm = submitModalForm;
window.processSubmitErrors = processSubmitErrors;
window.contentSrc = "";

/**
 * Initializes the application when DOM is ready.
 * Detects current page based on element IDs and initializes appropriate modules.
 *
 * @listens DOMContentLoaded
 */
document.addEventListener("DOMContentLoaded", () => {
  initMain();

  // Only initialize home if we're on the home page
  if (document.getElementById("alltasks")) {
    initHome();
  }

  // Only initialize matter-show if we're on the matter show page
  if (document.getElementById("actorPanel")) {
    initMatterShow();
  }

  // Initialize tables.js for various index pages
  if (document.getElementById("tableList")) {
    initTables();
  }

  // Initialize matter-index if we're on the matter index page
  if (document.getElementById("matterList")) {
    initMatterIndex();
  }

  // Initialize renewal-index if we're on the renewals page
  if (document.getElementById("renewalList")) {
    initRenewalIndex();
  }

  // Initialize actor-index if we're on the actor index page
  if (document.getElementById("actorList")) {
    initActorIndex();
  }

  // Initialize user-index if we're on the user index page
  if (document.getElementById("userList")) {
    initUserIndex();
  }
});
