import { reloadPart, debounce } from "./main.js";

export function initRenewalIndex() {
  const url = new URL(window.location.href);

  // Get all required element references
  const filterFieldsEl = document.getElementById("filterFields");
  const graceEl = document.getElementById("grace");
  const selectAllEl = document.getElementById("selectAll");
  const tabsGroupEl = document.getElementById("tabsGroup");
  const clearFiltersEl = document.getElementById("clearFilters");
  const doneRenewalsEl = document.getElementById("doneRenewals");
  const callRenewalsEl = document.getElementById("callRenewals");
  const renewalsSentEl = document.getElementById("renewalsSent");
  const renewalsExportEl = document.getElementById("renewalsExport");
  const renewalsInvoicedEl = document.getElementById("renewalsInvoiced");
  const invoicesPaidEl = document.getElementById("invoicesPaid");
  const instructedRenewalsEl = document.getElementById("instructedRenewals");
  const lastReminderRenewalsEl = document.getElementById(
    "lastReminderRenewals",
  );
  const reminderRenewalsEl = document.getElementById("reminderRenewals");
  const abandonRenewalsEl = document.getElementById("abandonRenewals");
  const lapsedRenewalsEl = document.getElementById("lapsedRenewals");
  const lapsingRenewalsEl = document.getElementById("lapsingRenewals");
  const sendLapsedRenewalsEl = document.getElementById("sendLapsedRenewals");
  const xmlRenewalsEl = document.getElementById("xmlRenewals");

  function refreshList() {
    window.history.pushState("", "phpIP", url);
    reloadPart(url, "renewalList");
  }

  if (filterFieldsEl) {
    filterFieldsEl.addEventListener(
      "input",
      debounce((e) => {
        if (e.target.matches(".form-control")) {
          if (e.target.value.length === 0) {
            url.searchParams.delete(e.target.name);
          } else {
            url.searchParams.set(e.target.name, e.target.value);
          }
          url.searchParams.delete("page");
          refreshList();
        }
      }, 500),
    );
  }

  if (graceEl) {
    graceEl.onchange = (e) => {
      if (!e.target.checked) {
        url.searchParams.delete("grace_period");
      } else {
        url.searchParams.set("grace_period", "1");
      }
      refreshList();
    };
  }

  if (selectAllEl) {
    selectAllEl.onchange = (e) => {
      const newValue = e.target.checked;
      const boxes = document.getElementsByClassName("clear-ren-task");
      for (const box of boxes) {
        box.checked = newValue;
      }
    };
  }

  // Load list according to corresponding tab
  if (tabsGroupEl) {
    tabsGroupEl.addEventListener("click", function (e) {
      url.searchParams.delete("step");
      url.searchParams.delete("invoice_step");
      url.searchParams.delete("page");
      if (e.target.hasAttribute("data-step")) {
        url.searchParams.set("step", e.target.dataset.step);
      }
      if (e.target.hasAttribute("data-invoice_step")) {
        url.searchParams.set("invoice_step", e.target.dataset.invoice_step);
      }
      window.history.pushState("", "phpIP", url);
      reloadPart(url, "renewalList");
    });
  }

  if (clearFiltersEl) {
    clearFiltersEl.onclick = () => {
      for (const key of url.searchParams.keys()) {
        if (key != "step" && key != "invoice_step") {
          url.searchParams.delete(key);
        }
      }
      window.location.href = url.href;
    };
  }

  if (doneRenewalsEl) {
    doneRenewalsEl.addEventListener("click", function (b) {
      const msgAction = "resetting";
      actionRenewals(b.target, msgAction, "/renewal/done");
    });
  }

  if (callRenewalsEl) {
    callRenewalsEl.addEventListener("click", function (b) {
      const msgAction = "call";
      actionRenewals(b.target, msgAction, "/renewal/call/1");
    });
  }

  if (renewalsSentEl) {
    renewalsSentEl.addEventListener("click", function (b) {
      const msgAction = "call";
      actionRenewals(b.target, msgAction, "/renewal/call/0");
    });
  }

  if (renewalsExportEl) {
    renewalsExportEl.onclick = (e) => {
      const exportUrl = "/renewal/export";
      e.preventDefault();
      window.location.href = exportUrl;
    };
  }

  if (renewalsInvoicedEl) {
    renewalsInvoicedEl.addEventListener("click", function (b) {
      const msgAction = "invoiced";
      actionRenewals(b.target, msgAction, "/renewal/invoice/0");
    });
  }

  if (invoicesPaidEl) {
    invoicesPaidEl.onclick = (b) => {
      const msgAction = "paid";
      actionRenewals(b.target, msgAction, "/renewal/paid");
    };
  }

  if (instructedRenewalsEl) {
    instructedRenewalsEl.addEventListener("click", function (b) {
      const msgAction = "for payment";
      actionRenewals(b.target, msgAction, "/renewal/topay");
    });
  }

  if (lastReminderRenewalsEl) {
    lastReminderRenewalsEl.addEventListener("click", function (b) {
      const msgAction = "last call";
      actionRenewals(b.target, msgAction, "/renewal/lastcall");
    });
  }

  if (reminderRenewalsEl) {
    reminderRenewalsEl.addEventListener("click", function (b) {
      const msgAction = "reminder";
      actionRenewals(b.target, msgAction, "/renewal/reminder");
    });
  }

  if (abandonRenewalsEl) {
    abandonRenewalsEl.addEventListener("click", function (b) {
      const msgAction = "abandon renewals";
      actionRenewals(b.target, msgAction, "/renewal/abandon");
    });
  }

  if (lapsedRenewalsEl) {
    lapsedRenewalsEl.addEventListener("click", function (b) {
      const msgAction = "lapsed renewals";
      actionRenewals(b.target, msgAction, "/renewal/lapsing");
    });
  }

  if (lapsingRenewalsEl) {
    lapsingRenewalsEl.addEventListener("click", function (b) {
      const msgAction = "lapsed renewals";
      actionRenewals(b.target, msgAction, "/renewal/lapsing");
    });
  }

  if (sendLapsedRenewalsEl) {
    sendLapsedRenewalsEl.addEventListener("click", function (b) {
      const msgAction = "lapse communications sent";
      actionRenewals(b.target, msgAction, "/renewal/closing");
    });
  }

  // Conditional handlers based on config (moved from blade template)
  const invoiceRenewalsEl = document.getElementById("invoiceRenewals");
  if (
    window.appConfig?.renewal?.invoice?.backend === "dolibarr" &&
    invoiceRenewalsEl
  ) {
    invoiceRenewalsEl.addEventListener("click", function (b) {
      const msgAction = "invoicing";
      actionRenewals(b.target, msgAction, "/renewal/invoice/1");
    });
  }

  if (window.appConfig?.renewal?.general?.receipt_tabs) {
    const receiptRenewalsEl = document.getElementById("receiptRenewals");
    if (receiptRenewalsEl) {
      receiptRenewalsEl.addEventListener("click", function (b) {
        const msgAction = "registering receipt";
        actionRenewals(b.target, msgAction, "/renewal/receipt");
      });
    }

    const sendReceiptsRenewalsEl = document.getElementById(
      "sendReceiptsRenewals",
    );
    if (sendReceiptsRenewalsEl) {
      sendReceiptsRenewalsEl.addEventListener("click", function (b) {
        const msgAction = "closing renewals";
        actionRenewals(b.target, msgAction, "/renewal/closing");
      });
    }
  }

  async function actionRenewals(button, msgAction, action_url) {
    // Active spinner
    button.insertAdjacentHTML(
      "afterbegin",
      '<i class="spinner-border spinner-border-sm" role="status" />',
    );
    const tids = getSelected();
    let string;
    if (tids.length === 0) {
      const end = document.getElementById("Untildate").value;
      if (!end) {
        alert("No renewals selected for " + msgAction);
        // withdraw spinner and restore button
        button.removeChild(
          document.getElementsByClassName("spinner-border")[0],
        );
        return;
      }
      const begin = document.getElementById("Fromdate").value;
      string = JSON.stringify({
        begin: begin,
        end: end,
      });
    } else {
      string = JSON.stringify({
        task_ids: tids,
      });
    }
    const context_url = new URL(window.location.href);
    await submitUpdate(string, action_url).catch((err) => alert(err));
    window.history.pushState("", "phpIP", context_url);
    reloadPart(context_url, "renewalList");
    // withdraw spinner
    button.removeChild(document.getElementsByClassName("spinner-border")[0]);
  }

  function submitUpdate(string, url) {
    return new Promise(function (resolve, reject) {
      const xhr = new XMLHttpRequest();
      xhr.open("POST", url, true);
      xhr.setRequestHeader("Content-type", "application/json; charset=utf-8");
      xhr.setRequestHeader(
        "X-CSRF-TOKEN",
        document.head.querySelector("[name=csrf-token]").content,
      );
      xhr.send(string);
      xhr.onload = function () {
        if (this.status === 200) {
          resolve(JSON.parse(this.responseText).success);
        }
        if (this.status === 419) {
          reject("Token expired. Refresh the page");
        }
        if (this.status === 404) {
          reject(
            "No email template found - check that your templates match your client's language",
          );
        } else {
          reject("Something went wrong");
        }
      };
    });
  }

  if (xmlRenewalsEl) {
    xmlRenewalsEl.addEventListener("click", function () {
      const tids = getSelected();
      if (tids.length === 0) {
        alert("No renewals selected for order");
        return;
      }
      const string = JSON.stringify({
        task_ids: tids,
        clear: false,
      });
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "/renewal/order", true);
      xhr.setRequestHeader("Content-Type", "application/json; charset=utf-8");
      xhr.setRequestHeader(
        "X-CSRF-TOKEN",
        document.head.querySelector("[name=csrf-token]").content,
      );
      xhr.send(string);
      xhr.onload = function (e) {
        if (this.status == 200) {
          // Find file name
          const filename = xhr
            .getResponseHeader("Content-Disposition")
            .split("filename=")[1];

          // The actual download by creating a link and clicking it programmatically
          const f = new File([xhr.response], filename, {
            type: xhr.getResponseHeader("Content-Disposition"),
          });
          const link = document.createElement("a");
          link.href = window.URL.createObjectURL(f);
          link.download = filename;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        } else if (this.status == 501) {
          alert(JSON.parse(this.responseText).error);
        }
      };
    });
  }

  function getSelected() {
    const tids = new Array();
    const boxes = document.getElementsByClassName("clear-ren-task");
    for (const box of boxes) {
      if (box.checked) {
        tids.push(box.getAttribute("id"));
      }
    }
    return tids;
  }
}
