import { fetchREST, reloadPart, processSubmitErrors } from "./main.js";

// Get mutable reference to contentSrc
let getContentSrc = () => {
  return window.contentSrc || "";
};
let setContentSrc = (value) => {
  window.contentSrc = value;
};

// Register Alpine.js component for image upload
export function registerImageUpload() {
  if (window.Alpine) {
    window.Alpine.data("imageUpload", (initialData) => ({
      expanded: initialData.hasImage || false,
      imageUrl: initialData.imageUrl || "",
      classifierId: initialData.classifierId || null,
      matterId: initialData.matterId,
      showControls: false,

      async uploadImage(file) {
        if (!file || !file.type.startsWith("image/")) return;

        const formData = new FormData();
        formData.append("matter_id", this.matterId);
        formData.append("type_code", "IMG");
        formData.append("image", file);

        try {
          const response = await fetch("/classifier", {
            method: "POST",
            headers: {
              "X-Requested-With": "XMLHttpRequest",
              "X-CSRF-TOKEN":
                document.head.querySelector("[name=csrf-token]").content,
            },
            body: formData,
          });

          if (response.ok) {
            const data = await response.text();
            this.classifierId = parseInt(data);
            this.imageUrl = "/classifier/" + data + "/img";
            this.showControls = false;
          }
        } catch (error) {
          console.error("Upload failed:", error);
        }
      },

      async deleteImage() {
        if (
          !this.classifierId ||
          !confirm(
            window.appConfig?.translations?.deleteImageConfirm ||
              "Delete this image?",
          )
        )
          return;

        try {
          const response = await fetchREST(
            "/classifier/" + this.classifierId,
            "DELETE",
          );
          if (response) {
            this.imageUrl = "";
            this.classifierId = null;
            this.expanded = false;
            this.showControls = false;
          }
        } catch (error) {
          console.error("Delete failed:", error);
        }
      },

      handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        const file = e.dataTransfer.files[0];
        if (file) this.uploadImage(file);
      },
    }));
  }
}

export function initMatterShow() {
  // Actor processing

  // Initialize the popovers
  let popover = null;
  let popoverList = new bootstrap.Popover(document.body, {
    selector: '[data-bs-toggle="popover"]',
    boundary: "viewport",
    content: actorPopoverTemplate.content.firstElementChild,
    container: "body",
    html: true,
    sanitize: false,
  });

  // Global listener references and cleanup function
  let currentActorHandler = null;
  let currentRoleHandler = null;
  function cleanupListeners() {
    if (currentActorHandler) {
      actorName.removeEventListener("acCompleted", currentActorHandler);
      currentActorHandler = null;
    }
    if (currentRoleHandler) {
      roleName.removeEventListener("acCompleted", currentRoleHandler);
      currentRoleHandler = null;
    }
  }

  // Process actor addition popovers
  app.addEventListener("shown.bs.popover", (e) => {
    // First destroy existing popover when a new popover is opened
    if (popover) {
      addActorForm.reset();
      popover.hide();
      cleanupListeners();
    }
    popover = bootstrap.Popover.getInstance(e.target);

    if (e.target.hasAttribute("data-role_code")) {
      // Change form based on role information
      addActorForm["role"].value = e.target.dataset.role_code;
      roleName.setAttribute("placeholder", e.target.dataset.role_name);
      addActorForm["shared"].value = e.target.dataset.shareable;
      if (e.target.dataset.shareable === "1") {
        actorShared.checked = true;
      } else {
        actorNotShared.checked = true;
      }
      actorName.focus();
    } else {
      // Reset form to defaults
      addActorForm["role"].value = "";
      roleName.setAttribute("placeholder", "Role");
      addActorForm["shared"].value = "1";
      actorShared.checked = true;
      roleName.focus();
    }

    // Attach listener for actorName's "acCompleted"
    currentActorHandler = (event) => {
      const selectedItem = event.detail;
      if (selectedItem.key === "create") {
        // Creates actor on the fly
        fetchREST(
          "/actor",
          "POST",
          new URLSearchParams(
            "name=" +
              selectedItem.value.toUpperCase() +
              "&default_role=" +
              addActorForm.role.value,
          ),
        ).then((response) => {
          addActorForm.actor_id.value = response.id;
          actorName.classList.add("is-valid");
          actorName.value = response.name;
        });
      } else {
        addActorForm.actor_id.value = selectedItem.key;
      }
    };
    actorName.addEventListener("acCompleted", currentActorHandler);

    // Attach listener for roleName's "acCompleted"
    currentRoleHandler = (event) => {
      const selectedItem = event.detail;
      addActorForm.shared.value = selectedItem.shareable;
      if (selectedItem.shareable) {
        actorShared.checked = true;
      } else {
        actorNotShared.checked = true;
      }
    };
    roleName.addEventListener("acCompleted", currentRoleHandler);

    actorShared.onclick = () => {
      addActorForm["shared"].value = "1";
    };

    actorNotShared.onclick = () => {
      addActorForm["shared"].value = "0";
    };

    addActorSubmit.onclick = () => {
      const formData = new FormData(addActorForm);
      const params = new URLSearchParams(formData);
      fetchREST("/actor-pivot", "POST", params).then((data) => {
        if (data.errors) {
          processSubmitErrors(data.errors, addActorForm);
        } else {
          addActorForm.reset();
          popover.hide();
          cleanupListeners();
          popover = null; // Allow immediate re-opening
          reloadPart(window.location.href, "actorPanel");
        }
      });
    };

    // Close popover by clicking the cancel button
    popoverCancel.onclick = () => {
      addActorForm.reset();
      popover.hide();
      cleanupListeners();
      popover = null; // Allow immediate re-opening
    };
  }); // End popover processing

  // Titles processing

  // Show the title creation form when the title panel is empty
  if (!titlePanel.querySelector("dt")) {
    titlePanel.querySelector('[href="#addTitleCollapse"]').click();
  }

  titlePanel.onclick = (e) => {
    if (e.target.id == "addTitleSubmit") {
      const formData = new FormData(addTitleForm);
      const params = new URLSearchParams(formData);
      fetchREST("/classifier", "POST", params).then((data) => {
        if (data.errors) {
          processSubmitErrors(data.errors, addTitleForm);
          footerAlert.innerHTML = data.message;
          footerAlert.classList.add("alert-danger");
        } else {
          reloadPart(window.location.href, "titlePanel");
        }
      });
    }
  };

  // Ajax refresh various panels when a modal is closed
  ajaxModal.addEventListener("hide.bs.modal", (e) => {
    const contentSrc = getContentSrc();
    switch (contentSrc.split("/")[5]) {
      case "roleActors":
        reloadPart(window.location.href, "actorPanel");
        break;
      case "events":
      case "tasks":
      case "renewals":
      case "classifiers":
        reloadPart(window.location.href, "multiPanel");
        break;
      case "edit":
        reloadPart(window.location.href, "refsPanel");
        break;
    }
    setContentSrc("");
  });

  //  Generate summary and copy

  ajaxModal.onclick = (e) => {
    switch (e.target.id) {
      case "sumButton":
        /* write to the clipboard now */
        //var text = document.getElementById("tocopy").textContent;
        var node = document.getElementById("tocopy");

        var selection = getSelection();
        selection.removeAllRanges();

        var range = document.createRange();
        range.selectNodeContents(node);
        selection.addRange(range);

        var success = document.execCommand("copy");
        selection.removeAllRanges();
        return success;

      case "addTaskReset":
        e.target.closest("tr").innerHTML = "";
        break;

      // case 'addClassifierReset':
      //   // Doesn't work, probably not necessary
      //   bootstrap.Collapse.getOrCreateInstance(addClassifierRow).hide();
      //   break;
    }
  };

  // File drop zone management

  dropZone.ondragover = function () {
    this.classList.remove("bg-info");
    this.classList.add("bg-primary");
    return false;
  };
  dropZone.ondragleave = function () {
    this.classList.remove("bg-primary");
    this.classList.add("bg-info");
    return false;
  };
  dropZone.ondrop = function (event) {
    event.preventDefault();
    this.classList.add("bg-info");
    this.classList.remove("bg-primary");
    var file = event.dataTransfer.files[0];
    var formData = new FormData();
    formData.append("file", file);
    fetch(this.dataset.url, {
      headers: {
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN":
          document.head.querySelector("[name=csrf-token]").content,
      },
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          if (response.status == 422) {
            alert("Only DOCX files can be processed for the moment");
          }
          throw new Error("Response status " + response.status);
        }
        return response.blob();
      })
      .then((blob) => {
        // Simulate click on a temporary link to perform download
        var tempLink = document.createElement("a");
        tempLink.style.display = "none";
        tempLink.href = URL.createObjectURL(blob);
        tempLink.download = uid.outerText + "-" + file.name;
        document.body.appendChild(tempLink);
        tempLink.click();
        document.body.removeChild(tempLink);
      })
      .catch((error) => {
        console.error(error);
      });
    return false;
  };
}
