<script>
  var relatedUrl = ""; // Identifies what to display in the Ajax-filled modal. Updated according to the href attribute used for triggering the modal
  var resource = ""; // Identifies the REST resource for CRUD operations

// Actor processing

  // Initialize popovers with custom template
  var popoverTemplate = '<div class="popover border-info" role="tooltip"><div class="arrow"></div><h3 class="popover-header bg-info text-white"></h3><div class="popover-body"></div></div>';

  $('body').popover({
    selector: '[rel="popover"]',
    template: popoverTemplate,
    html: true,
    sanitize: false
  });

  $('body').on("shown.bs.popover", '[rel="popover"]', function() {
    // First destroy existing popover when a new popover is opened
    $('.popover').siblings('.popover').first().popover('dispose');

    var addActorForm = document.forms['addActorForm'];

    $('#actor_name').autocomplete({
      minLength: 2,
      source: "/actor/autocomplete",
      select: function(event, ui) {
        if (ui.item.value === 'create') { // Creates actor on the fly
          $.post('/actor', {
              name: this.value.toUpperCase(),
              default_role: addActorForm.role.value
            })
            .done(function(response) {
              addActorForm.actor_id.value = response.id;
              addActorForm.elements.actor_name.value = response.name;
            });
        } else {
          addActorForm.actor_id.value = ui.item.id;
          // Fills in actor's company information, if available
          addActorForm.company_id.value = ui.item.company_id;
        }
      },
      open: function(event, ui) { // Change color of last suggestion (which is the "create actor" option)
        $("ul").find(".ui-menu-item:last").addClass("text-success");
      },
      change: function(event, ui) {
        if (!ui.item)
          this.value = "";
      }
    });

    $('input[name="role"]').autocomplete({
      minLength: 0,
      source: "/role/autocomplete",
      select: function(event, ui) {
        addActorForm.shared.value = ui.item.shareable;
        if (ui.item.shareable) {
          addActorForm.elements.actorShared.checked = true;
        } else {
          addActorForm.elements.actorNotShared.checked = true;
        }
      },
      change: function(event, ui) {
        // Removes the entered value if it does not correspond to a suggestion
        if (!ui.item)
          this.value = "";
      }
    }).focus(function() {
      // Triggers autocomplete search with 0 characters upon focus
      $(this).autocomplete("search", "");
    });

    $("body").on("change", '.popover input[name="matter_id"]', function() {
      $('input[name="shared"]').val(function(index, value) {
        if (value === 1) {
          return 0;
        } else {
          return 1;
        }
      });
    });

    document.getElementById('addActorSubmit').onclick = () => {
      //var currentForm = this.parentNode;
      var request = $('#addActorForm').find("input").filter(function() {
        return $(this).val().length > 0;
      }).serialize(); // Filter out empty values
      $.post('/actor-pivot', request)
        .fail(function(errors) {
          $.each(errors.responseJSON.errors, function(key, item) {
            addActorForm[key].placeholder = item;
            addActorForm[key].className += ' is-invalid';
          });
          $(".popover-body").find(".alert").html(errors.responseJSON.message).removeClass("d-none");
        }).done(function() {
          $('.popover').popover('hide');
          $("#actorPanel").load("/matter/{{ $matter->id }}" + " #actorPanel > div");
        });
    };

    // Close popover by clicking the cancel button
    document.getElementById('popoverCancel').onclick = () => {
      $('.popover').popover('dispose');
    };
  });


// Modals

  // Ajax fill the opened modal and set global parameters
  $("#listModal, #createMatterModal, #summaryModal").on("show.bs.modal", function(event) {
    relatedUrl = event.relatedTarget.href;
    resource = event.relatedTarget.dataset.resource;
    this.querySelector('.modal-title').innerHTML = event.relatedTarget.title;
    /*var doFetch = async (url, selector) => {
      res = await fetch(url);
      this.querySelector(selector).innerHTML = await res.text();
    }
    doFetch(relatedUrl, '.modal-body');*/
    fetch(relatedUrl)
      .then(response => response.text())
      .then(html => {
        this.querySelector('.modal-body').innerHTML = html;
      });
  });

  // Ajax refresh various panels when a modal is closed
  $(".modal").on("hide.bs.modal", function(event) {
    if (resource === '/actor-pivot/') {
      //$("#actorPanel").load("/matter/{{ $matter->id }} #actorPanel > div");
      fetch("/matter/{{ $matter->id }}")
        .then(response => response.text())
        .then(html => {
          let doc = new DOMParser().parseFromString(html, "text/html");
          actorPanel.innerHTML = doc.getElementById('actorPanel').innerHTML;
        });
    } else {
      $("#multiPanel").load("/matter/{{ $matter->id }} #multiPanel > div");
    }
  });

// Notes edition

  multiPanel.addEventListener('change', e => {
    if (e.target && e.target.matches("#notes")) {
      $.ajax({
        type: 'PUT',
        url: "/matter/{{ $matter->id }}",
        data: {
          notes: $("#notes").val()
        }
      }).done(e.target.classList.remove("bg-warning"));
    }
  });


// Titles processing

  // Show the title creation form when the title panel is empty
  if (!titlePanel.querySelector('.row')) {
    $("#addTitleForm").collapse("show");
  }

  $("#titlePanel").on("keydown", ".titleItem", function(e) {
    if (e.which === 13) {
      e.preventDefault();
      var method = "PUT";
      var title = $(this).text().trim();
      if (!title)
        method = "DELETE";
      $.ajax({
        type: method,
        url: '/classifier/' + $(this).attr("id"),
        data: {
          value: title
        }
      }).done(function() {
        $('#titlePanel').load("/matter/{{ $matter->id }} #titlePanel > div");
      });
    } else
      $(this).addClass("border border-info");
  });

  $("#titlePanel").on("shown.bs.collapse", "#addTitleForm", function() {
    $(this).find('input[name="type"]').autocomplete({
      minLength: 0,
      source: "/classifier-type/autocomplete/1",
      select: function(event, ui) {
        $("#addTitleForm").find('input[name="type_code"]').val(ui.item.code);
      },
      change: function(event, ui) {
        if (!ui.item)
          this.value = "";
      }
    }).focus(function() {
      $(this).autocomplete("search", "");
    });
  });

  $("#titlePanel").on("click", "#addTitleSubmit", function() {
    var request = $("#addTitleForm").find("input").filter(function() {
      return $(this).val().length > 0;
    }).serialize(); // Filter out empty values
    $.post('/classifier', request)
      .done(function() {
        $('#titlePanel').load("/matter/{{ $matter->id }} #titlePanel > div");
      }).fail(function(errors) {
        $.each(errors.responseJSON.errors, function(key, item) {
          $("#addTitleForm").find('input[name=' + key + ']').attr("placeholder", item).addClass('is-invalid');
        });
      });
  });

// Generic listModal functions

  // Mark a modified input field
  document.body.addEventListener("input", e => {
    if (e.target && e.target.matches("input.noformat, textarea")) {
      e.target.classList.add("bg-warning");
    }
  });

  // Generic in-place edition of input fields
  listModal.addEventListener("change", e => {
    if (e.target && e.target.matches("input.noformat")) {
      $.ajax({
        url: resource + e.target.parentNode.parentNode.dataset.id,
        type: 'PUT',
        data: $(e.target).serialize()
      }).done(() => {
        $("#listModal").find(".modal-body").load(relatedUrl);
        $("#listModal").find(".alert").removeClass("alert-danger").html("");
      }).fail((errors) => {
        $.each(errors.responseJSON.errors, (key, item) => {
          $("#listModal").find(".modal-footer .alert").html(item).addClass("alert-danger");
        });
      });
    }
  });

  $('#listModal').on("click", 'input[name="assigned_to"].noformat', function() {
    $(this).autocomplete({
      minLength: 2,
      source: "/user/autocomplete",
      change: function(event, ui) {
        if (!ui.item) {
          this.value = "";
        }
      },
      select: function(event, ui) {
        this.value = ui.item.value;
        this.blur(); // Removing focus causes the "change" event to trigger and submit the value via the default functionality attached to the "input.noformat" fields
      }
    });
  });

  $('#listModal').on("click", 'input[name="actor_id"].noformat, input[name="company_id"].noformat', function() {
    $(this).autocomplete({
      minLength: 2,
      source: "/actor/autocomplete",
      change: function(event, ui) {
        if (!ui.item) {
          this.value = "";
        }
      },
      select: function(event, ui) {
        this.value = ui.item.id;
        this.blur();
      }
    });
  });

  $('#listModal').on("click", 'input[type="checkbox"]', function() {
    var flag = 0;
    if ($(this).is(":checked"))
      flag = 1;
    var data = $(this).attr("name") + "=" + flag;
    $.ajax({
      url: resource + $(this).closest("tr").data("id"),
      type: 'PUT',
      data: data
    }).done(function() {
      $("#listModal").find(".modal-body").load(relatedUrl);
      $("#listModal").find(".alert").removeClass("alert-danger").html("");
    });
  });

  $('#listModal').on("click", 'input[name="alt_matter_id"].noformat', function() {
    $(this).autocomplete({
      minLength: 2,
      source: "/matter/autocomplete",
      change: function(event, ui) {
        if (!ui.item) {
          this.value = "";
        }
      },
      select: function(event, ui) {
        this.value = ui.item.id;
        this.blur();
      }
    });
  });

  // Specific to Advanced matter edition

  $('#listModal').on("click", 'input[name="origin"],input[name="country"]', function() {
    $(this).autocomplete({
      minLength: 1,
      source: "/country/autocomplete",
      change: function(event, ui) {
        if (!ui.item) {
          this.value = "";
        }
      },
      select: function(event, ui) {
        this.value = ui.item.id;
        $.ajax({
          url: resource + $(this).closest("card").data("id"),
          type: 'PUT',
          data: $(this).serialize()
        }).done(function() {
          $("#listModal").find(".modal-body").load(relatedUrl);
          $("#listModal").find(".alert").removeClass("alert-danger").html("");
        });
      }
    });
  });

  $('#listModal').on("click", 'input[name="parent_id"],input[name="container_id"]', function() {
    $(this).autocomplete({
      minLength: 1,
      source: "/matter/autocomplete",
      change: function(event, ui) {
        if (!ui.item) {
          this.value = "";
        }
      },
      select: function(event, ui) {
        this.value = ui.item.value;
        $.ajax({
          url: resource + $(this).closest("card").data("id"),
          type: 'PUT',
          data: $(this).serialize()
        }).done(function() {
          $("#listModal").find(".modal-body").load(relatedUrl);
          $("#listModal").find(".alert").removeClass("alert-danger").html("");
        });
      }
    });
  });

  $('#listModal').on("click", 'input[name="responsible"]', function() {
    $(this).autocomplete({
      minLength: 1,
      source: "/user/autocomplete",
      change: function(event, ui) {
        if (!ui.item) {
          this.value = "";
        }
      },
      select: function(event, ui) {
        this.value = ui.item.value;
        $.ajax({
          url: resource + $(this).closest("card").data("id"),
          type: 'PUT',
          data: $(this).serialize()
        }).done(function() {
          $("#listModal").find(".modal-body").load(relatedUrl);
          $("#listModal").find(".alert").removeClass("alert-danger").html("");
        });
      }
    });
  });

  $("#listModal").on("click", "#deleteMatter", function() {
    $.ajax({
      url: '/matter/' + $(this).closest("card").data("id"),
      type: 'DELETE'
    }).done(function() {
      location.href = "/matter";
    }).fail(function(errors) {
      alert(errors.responseJSON.message);
    });
    return false;
  });

// Specific processing in the actor/role list modal

  $("#listModal").on("click", "#removeActor", function() {
    $.ajax({
      url: '/actor-pivot/' + $(this).closest("tr").data("id"),
      type: 'DELETE'
    }).done(function() {
      $('#listModal').find(".modal-body").load(relatedUrl);
    });
    return false;
  });

// Specific processing of the event list modal

  $('#listModal').on("click", 'input[name="eventName"]', function() {
    $(this).focus().autocomplete({
      minLength: 2,
      source: "/event-name/autocomplete/0",
      select: (event, ui) => {
        addEventForm['code'].value = ui.item.code;
      },
      change: (event, ui) => {
        if (!ui.item)
          this.value = "";
      }
    });
  })

  $('#listModal').on("click", "#alt_matter_id", function() {
    $(this).autocomplete({
      minLength: 2,
      source: "/matter/autocomplete",
      select: (event, ui) => {
        addEventForm['alt_matter_id'][0].value = ui.item.id;
      },
      change: (event, ui) => {
        if (!ui.item)
          this.value = "";
      }
    });
  });

  $("#listModal").on("click", "#addEventSubmit", function() {
    var request = $("#addEventForm").find("input").filter(function() {
      return $(this).val().length > 0;
    }).serialize(); // Filter out empty values
    $.post('/event', request)
      .done(() => {
        $('#listModal').find(".modal-body").load("/matter/{{ $matter->id }}/events");
      })
      .fail((errors) => {
        $.each(errors.responseJSON.errors, (key, item) => {
          $("#addEventForm").find('input[name=' + key + ']').attr("placeholder", item).addClass('is-invalid');
        });
      });
  });

// Specific processing in the task list modal

  $("#listModal").on("click", "#addTaskToEvent", function() {
    this.parentNode.parentNode.parentNode.insertAdjacentHTML('beforeend', addTaskFormTemplate.innerHTML);
    addTaskForm['trigger_id'].value = this.dataset.event_id;
    $("#addTaskForm").find('input[name="name"]').focus().autocomplete({
      minLength: 2,
      source: "/event-name/autocomplete/1",
      select: function(event, ui) {
        $("#addTaskForm").find('input[name="code"]').val(ui.item.code);
      },
      change: function(event, ui) {
        if (!ui.item)
          this.value = "";
      }
    });
    $("#addTaskForm").find('input[name="assigned_to"]').autocomplete({
      minLength: 2,
      source: "/user/autocomplete",
      change: function(event, ui) {
        if (!ui.item)
          this.value = "";
      }
    });
  });

  $("#listModal").on("click", "#addTaskSubmit", function() {
    var request = $("#addTaskForm").find("input").filter(function() {
      return $(this).val().length > 0;
    }).serialize(); // Filter out empty values
    $.post('/task', request)
      .done(function() {
        $('#listModal').find(".modal-body").load("/matter/{{ $matter->id }}/tasks");
      }).fail(function(errors) {
        $.each(errors.responseJSON.errors, function(key, item) {
          $("#addTaskForm").find('input[name=' + key + ']').attr("placeholder", item).addClass('is-invalid');
        });
      });
  });

  $("#listModal").on("click", "#deleteTask", function() {
    $.ajax({
      url: '/task/' + $(this).closest("tr").data("id"),
      type: 'DELETE'
    }).done(function() {
      $('#listModal').find(".modal-body").load(relatedUrl);
    });
  });

  $("#listModal").on("click", "#deleteEvent", function() {
    if (confirm("Deleting the event will also delete the linked tasks. Continue anyway?")) {
      $.ajax({
        url: '/event/' + $(this).data('event_id'),
        type: 'DELETE'
      }).done(function() {
        $('#listModal').find(".modal-body").load(relatedUrl);
      });
    }
  });

// Classifiers modal processing

  $('#classifiersModal').on("change", "input.noformat", function(e) {
    $.ajax({
      url: '/classifier/' + $(this).closest("tr").data("classifier_id"),
      type: 'PUT',
      data: $(this).serialize()
    }).done(function() {
      $('.bg-warning').removeClass("bg-warning");
      $("#classifiersModal").find(".alert").removeClass("alert-danger").html("");
    }).fail(function(errors) {
      $.each(errors.responseJSON.errors, function(key, item) {
        $("#classifiersModal").find(".modal-footer .alert").html(item).addClass('alert-danger');
      });
    });
  });

  $('#classifiersModal').on("click", 'input[name="lnk_matter_id"].noformat', function() {
    $(this).autocomplete({
      minLength: 2,
      source: "/matter/autocomplete",
      change: function(event, ui) {
        if (!ui.item) {
          this.value = "";
        }
      },
      select: function(event, ui) {
        this.value = ui.item.id;
        $.ajax({
          url: '/classifier/' + $(this).closest("tr").data("classifier_id"),
          type: 'PUT',
          data: $(this).serialize()
        }).done(function() {
          $('#classifiersModal').load("/matter/{{ $matter->id }} #classifiersModal > div");
          $("#classifiersModal").find(".alert").removeClass("alert-danger").html("");
        });
      }
    });
  });

  $("#classifiersModal").on("click", 'input[name="type"]', function() {
    $(this).autocomplete({
      minLength: 0,
      source: "/classifier-type/autocomplete/0",
      select: function(event, ui) {
        addClassifierForm['type_code'].value = ui.item.code;
      },
      change: function(event, ui) {
        if (!ui.item)
          this.value = "";
      }
    }).focus(function() {
      // Forces search with no characters upon focus
      $(this).autocomplete("search", "");
    });
  });

  $("#classifiersModal").on("click",'#lnk_matter_id', function() {
    $(this).autocomplete({
      minLength: 2,
      source: "/matter/autocomplete",
      select: (event, ui) => {
        addClassifierForm['lnk_matter_id'][0].value = ui.item.id;
      },
      change: function(event, ui) {
        if (!ui.item)
          this.value = "";
      }
    });
  });

  $("#classifiersModal").on("click", "#addClassifierSubmit", function() {
    $.post('/classifier', $("#addClassifierForm").serialize())
      .done(function() {
        $('#classifiersModal').load("/matter/{{ $matter->id }} #classifiersModal > div");
      }).fail(function(errors) {
        $.each(errors.responseJSON.errors, function(key, item) {
          $("#addClassifierForm").find('input[name=' + key + ']').attr("placeholder", item).addClass('is-invalid');
        });
      });
  });

  $("#classifiersModal").on("click", "#deleteClassifier", function() {
    $.ajax({
      url: '/classifier/' + $(this).closest("tr").data("classifier_id"),
      type: 'DELETE'
    }).done(function() {
      $('#classifiersModal').load("/matter/{{ $matter->id }} #classifiersModal > div");
    });
    return false;
  });

//  Generate summary and copy

  $("#summaryModal").on("click", "#sumButton", function(event) {
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
  });
</script>
