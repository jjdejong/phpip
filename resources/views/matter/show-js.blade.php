<script>

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

    $('#actorName').autocomplete({
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
          addActorForm.actor_id.value = ui.item.key;
        }
      },
      change: function(event, ui) {
        if (!ui.item)
          this.value = "";
      }
    });

    $('input#roleName').autocomplete({
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
        if (!ui.item) {
          this.value = "";
        } else {
          addActorForm.role.value = ui.item.key;
        }
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

    addActorSubmit.onclick = () => {
      formData = new FormData(addActorForm);
      params = new URLSearchParams(formData);
      fetchREST('/actor-pivot', 'POST', params)
      .then(data => {
        if (data.errors) {
          processSubmitErrors(data.errors, addActorForm);
        } else {
          $('.popover').popover('hide');
          $("#actorPanel").load("/matter/{{ $matter->id }} #actorPanel > div");
        }
      });
    };

    // Close popover by clicking the cancel button
    popoverCancel.onclick = () => {
      $('.popover').popover('dispose');
    };
  });


// Notes edition

  multiPanel.addEventListener('change', e => {
    if (e.target && e.target.matches("#notes")) {
      let params = new URLSearchParams();
      params.append('notes', e.target.value);
      fetchREST("/matter/{{ $matter->id }}", "PUT", params)
      .then(data => {
        if (data.errors){
          console.log(data.errors);
        } else {
          e.target.classList.remove('bg-warning');
        }
      });
    }
  });


// Titles processing

  // Show the title creation form when the title panel is empty
  if (!titlePanel.querySelector('.row')) {
    $("#addTitleCollapse").collapse("show");
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

  $("#titlePanel").on("shown.bs.collapse", "#addTitleCollapse", function() {
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

// Generic ajaxModal functions

  // Ajax refresh various panels when a modal is closed
  $("#ajaxModal").on("hide.bs.modal", function(event) {
    if (resource === '/actor-pivot/') {
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

//  Generate summary and copy

  $("#ajaxModal").on("click", "#sumButton", function(event) {
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
