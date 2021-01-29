<script>

// Actor processing

  // Initialize popovers with custom template
  var popoverTemplate = '<div class="popover border-info" role="tooltip"><div class="arrow"></div><h3 class="popover-header bg-info text-white"></h3><div class="popover-body"></div></div>';

  $('body').popover({
    selector: '[data-toggle="popover"]',
    boundary: 'viewport',
    template: popoverTemplate,
    content: actorPopoverTemplate.content.firstElementChild,
    html: true,
    sanitize: false
  });

  // Process actor addition popovers
  $('body').on("shown.bs.popover", function(e) {
    // First destroy existing popover when a new popover is opened
    $('.popover').siblings('.popover').first().popover('dispose');

    if (e.target.hasAttribute('data-role_code')) {
      // Change form based on role information
      addActorForm['role'].value = e.target.dataset.role_code;
      roleName.setAttribute('placeholder', e.target.dataset.role_name);
      addActorForm['shared'].value = e.target.dataset.shareable;
      if (e.target.dataset.shareable === "1") {
        actorShared.checked = true;
      } else {
        actorNotShared.checked = true;
      }
      actorName.focus();
    } else {
      // Reset form to defaults
      addActorForm['role'].value = "";
      roleName.setAttribute('placeholder', 'Role');
      addActorForm['shared'].value = "1";
      actorShared.checked = true;
      roleName.focus();
    }

    $('#actorName').autocomplete({
      minLength: 2,
      source: "/actor/autocomplete/1",
      select: function(event, ui) {
        if (ui.item.key === 'create') { // Creates actor on the fly
          fetchREST('/actor', 'POST', new URLSearchParams('name=' + this.value.toUpperCase() + '&default_role=' + addActorForm.role.value))
          .then( response => {
            addActorForm.actor_id.value = response.id;
            actorName.classList.add('is-valid');
            actorName.value = response.name;
          });
        } else {
          addActorForm.actor_id.value = ui.item.key;
        }
      },
      change: function(event, ui) {
        if (!ui.item) {
          this.value = "";
        }
      }
    });

    $('input#roleName').autocomplete({
      minLength: 0,
      source: "/role/autocomplete",
      change: function(event, ui) {
        if (!ui.item) {
          // Removes the entered value if it does not correspond to a suggestion
          this.value = "";
        } else {
          addActorForm.role.value = ui.item.key;
          addActorForm.shared.value = ui.item.shareable;
          if (ui.item.shareable) {
            addActorForm.elements.actorShared.checked = true;
          } else {
            addActorForm.elements.actorNotShared.checked = true;
          }
        }
      }
    }).focus(function() {
      // Triggers autocomplete search with 0 characters upon focus
      $(this).autocomplete("search", "");
    });

    actorShared.onclick = () => {
      addActorForm['shared'].value = "1";
    }

    actorNotShared.onclick = () => {
      addActorForm['shared'].value = "0";
    }

    addActorSubmit.onclick = () => {
      formData = new FormData(addActorForm);
      params = new URLSearchParams(formData);
      fetchREST('/actor-pivot', 'POST', params)
      .then(data => {
        if (data.errors) {
          processSubmitErrors(data.errors, addActorForm);
        } else {
          addActorForm.reset();
          $('.popover').popover('dispose');
          reloadPart("/matter/{{ $matter->id }}", 'actorPanel');
        }
      });
    };

    // Close popover by clicking the cancel button
    popoverCancel.onclick = () => {
      addActorForm.reset();
      $('.popover').popover('dispose');
    };
  }); // End popover processing


// Titles processing

  // Show the title creation form when the title panel is empty
  if (!titlePanel.querySelector('dt')) {
    $("#addTitleCollapse").collapse("show");
  }

  titlePanel.onclick = e => {
    if (e.target.id == 'addTitleSubmit') {
      formData = new FormData(addTitleForm);
      params = new URLSearchParams(formData);
      fetchREST('/classifier', 'POST', params)
        .then( data => {
          if (data.errors) {
            processSubmitErrors(data.errors, addTitleForm);
            footerAlert.innerHTML = data.message;
            footerAlert.classList.add('alert-danger');
          } else {
            reloadPart(window.location.href, 'titlePanel');
          }
        });
    }
  }

  // Ajax refresh various panels when a modal is closed
  $("#ajaxModal").on("hide.bs.modal", function(e) {
    switch (contentSrc.split('/')[5]) {
      case 'roleActors':
        reloadPart(window.location.href, 'actorPanel');
        break;
      case 'events':
      case 'tasks':
      case 'renewals':
      case 'classifiers':
        reloadPart(window.location.href, 'multiPanel');
        break;
      case 'edit':
        reloadPart(window.location.href, 'refsPanel');
        break;
    }
    contentSrc = "";
  });

//  Generate summary and copy

  ajaxModal.onclick = e => {
    if (e.target.id == 'sumButton') {
      /* write to the clipboard now */
      //var text = document.getElementById("tocopy").textContent;
      var node = document.getElementById("tocopy")

      var selection = getSelection();
      selection.removeAllRanges();

      var range = document.createRange();
      range.selectNodeContents(node);
      selection.addRange(range);

      var success = document.execCommand('copy');
      selection.removeAllRanges();
      return success;
    }
  }

  // File drop zone management

  dropZone.ondragover = function () {
    this.classList.remove('bg-info');
    this.classList.add('bg-primary');
    return false;
  };
  dropZone.ondragleave = function () {
    this.classList.remove('bg-primary');
    this.classList.add('bg-info');
    return false;
  };
  dropZone.ondrop = function (event) {
    event.preventDefault();
    this.classList.add('bg-info');
    this.classList.remove('bg-primary');
    var file = event.dataTransfer.files[0];
    var formData = new FormData();
    formData.append('file', file);
    fetch(this.dataset.url, {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        method: 'POST',
        body: formData
    })
    .then(response => {
      if (!response.ok) {
        if (response.status == 422) {
          alert('Only DOCX files can be processed for the moment');
        }
        throw new Error('Response status ' + response.status);
      }
      return response.blob();
    })
    .then(blob => {
      // Simulate click on a temporary link to perform download
      var tempLink = document.createElement('a');
      tempLink.style.display = 'none';
      tempLink.href = URL.createObjectURL(blob);
      tempLink.download = "{{ $matter->uid }}-" + file.name;
      document.body.appendChild(tempLink);
      tempLink.click();
      document.body.removeChild(tempLink);
    })
    .catch(error => {
      console.error(error);
    });
    return false;
  };
</script>
