<script>
var relatedUrl = ""; // Identifies what to display in the Ajax-filled modal. Updated according to the href attribute used for triggering the modal
var resource = ""; // Identifies the REST resource for CRUD operations
var matter_id = $('#matter_id').text();

$(document).ready(function() {

    // Actor processing

    // Initialize popovers with custom template
    var popoverTemplate = '<div class="popover border-info" role="tooltip"><div class="arrow"></div><h3 class="popover-header bg-info text-white"></h3><div class="popover-body"></div></div>';

    $('body').popover({
      selector: '[rel="popover"]',
      template: popoverTemplate
    });

    // Close popovers by clicking the cancel button
    $('body').on('click', "#popoverCancel", function (e) {
      $(this).parents('.popover').popover('hide');
    });

    $('body').on("shown.bs.popover", '[rel="popover"]', function() {
      $(".popover:last").find('input[name="actor_id"]').autocomplete({
    		minLength: 2,
    		source: "/actor/autocomplete",
        select: function( event, ui ) {
          $(this).parents('form').find('input[name="company_id"]').val(ui.item.company_id);
    		},
    		change: function (event, ui) {
    			if (!ui.item) $(this).val("");
    		}
      });

      $(".popover:last").find('input[name="role"]').autocomplete({
    		minLength: 0,
    		source: "/role/autocomplete",
        select: function( event, ui ) {
          $(this).parents('form').find('input[name="shared"]').val(ui.item.shareable);
          if (ui.item.shareable) {
    			  $(this).parents('form').find("#actorShared").prop('checked', true);
          } else {
            $(this).parents('form').find("#actorNotShared").prop('checked', true);
          }
    		},
    		change: function (event, ui) {
          // Removes the entered value if it does not correspond to a suggestion
    			if (!ui.item) $(this).val("");
    		}
      }).focus(function () {
        // Triggers autocomplete search with 0 characters upon focus
        $(this).autocomplete("search", "");
      });

      $("body").on("change", '.popover input[name="matter_id"]', function() {
        $(this).parents('form').find('input[name="shared"]').val(function( index, value ) {
          if (value == 1) {
            return 0;
          } else {
            return 1;
          }
        });
      });

      $(".popover:last").find("#addActorSubmit").click( function() {
        var currentForm = $(this).parents('form');
      	var request = currentForm.find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
      	$.post('/actor-pivot', request)
      	.fail(function(errors) {
      		$.each(errors.responseJSON.errors, function (key, item) {
      			currentForm.find('input[name=' + key + ']').attr("placeholder", item).addClass('is-invalid');
      		});
          currentForm.parents(".popover-body").find(".alert").html(errors.responseJSON.message).removeClass("d-none");
    	  })
        .done(function() {
          currentForm.parents('.popover').popover('hide');
          $("#actorPanel").load("/matter/" + matter_id + " #actorPanel > div");
        });
      });
    });

	// Ajax fill the opened modal and set global parameters
    $("#listModal, #createMatterModal").on("show.bs.modal", function(event) {
    	relatedUrl = $(event.relatedTarget).attr("href");
    	resource = $(event.relatedTarget).data("resource");
    	$(this).find(".modal-title").text( $(event.relatedTarget).attr("title") );
      $(this).find(".modal-body").load(relatedUrl);
    });

	// Ajax refresh various panels when a modal is closed
    $("#listModal, #classifiersModal").on("hide.bs.modal", function(event) {
      if ( resource == '/actor-pivot/') {
        $("#actorPanel").load("/matter/" + matter_id + " #actorPanel > div");
      } else {
        $("#multiPanel").load("/matter/" + matter_id + " #multiPanel > div");
      }
    });

  // Notes edition
  $("#notes").keyup(function() {
		$("#updateNotes").removeClass('hidden-action');
		$(this).addClass('changed');
	});

  $("#updateNotes").click(function() {
		if ( $("#notes").hasClass('changed') ) {
      $.ajax({
        type: 'PUT',
        url: "/matter/" + matter_id,
        data: { notes: $("#notes").val() }
      });
			$("#updateNotes").addClass('hidden-action');
			$(this).removeClass('changed');
		}
		return false;
	});
});


// Titles processing

// Show the title creation form when the title panel is empty
if (!$("#titlePanel").text().trim()) {
  $("#addTitleForm").collapse("show");
}

$("#titlePanel").on("keypress", ".titleItem", function (e) {
	if (e.which == 13) {
		e.preventDefault();
		var method = "PUT";
		var title = $(this).text().trim();
		if (!title)
			method = "DELETE";
    $.ajax({
      type: method,
      url: '/classifier/' + $(this).attr("id"),
      data: { value: title }
    }).done(function() {
			$('#titlePanel').load("/matter/" + matter_id + " #titlePanel > div");
		});
	} else
		$(this).addClass("bg-warning");
});

$("#titlePanel").on("shown.bs.collapse", "#addTitleForm", function() {
   	$(this).find('input[name="type"]').autocomplete({
  		minLength: 0,
  		source: "/classifier-type/autocomplete/1",
  		select: function( event, ui ) {
  			$("#addTitleForm").find('input[name="type_code"]').val( ui.item.code );
  		},
  		change: function (event, ui) {
  			if (!ui.item) $(this).val("");
  		}
  	}).focus(function () {
        $(this).autocomplete("search", "");
    });
});

$("#titlePanel").on("click", "#addTitleSubmit", function() {
	var request = $("#addTitleForm").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
	$.post('/classifier', request)
	.done(function() {
		$('#titlePanel').load("/matter/" + matter_id + " #titlePanel > div");
	}).fail(function(errors) {
		$.each(errors.responseJSON.errors, function (key, item) {
			$("#addTitleForm").find('input[name=' + key + ']').attr("placeholder", item).addClass('is-invalid');
		});
	});
});

// Generic in-place edition of fields in a listModal

$("#listModal").on("keypress", "input.noformat", function (e) {
	if (e.which == 13) {
		e.preventDefault();
    $.ajax({
      url: resource + $(this).closest("tr").data("id"),
      type: 'PUT',
      data: $(this).serialize(),
    }).done(function () {
			$("#listModal").find(".modal-body").load(relatedUrl);
			$("#listModal").find(".alert").removeClass("alert-danger").html("");
		}).fail(function(errors) {
			$.each(errors.responseJSON.errors, function (key, item) {
				$("#listModal").find(".modal-footer .alert").html(item).addClass("alert-danger");
			});
		});
	} else
		$(this).parent("td").addClass("bg-warning");
});

$('#listModal').on("focus", 'input[name$="date"].noformat', function() {
	$(this).datepicker({
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true,
		onSelect: function(date, instance) {
      $.ajax({
        url: resource + $(this).closest("tr").data("id"),
        type: 'PUT',
        data: $(this).serialize(),
      }).done(function () {
				$("#listModal").find(".modal-body").load(relatedUrl);
				$("#listModal").find(".alert").removeClass("alert-danger").html("");
			});
		}
	});
});

$('#listModal').on("click", 'input[name="assigned_to"].noformat', function() {
	$(this).autocomplete({
		minLength: 2,
		source: "/user/autocomplete",
		change: function (event, ui) {
      if (!ui.item) {
        $(this).val("");
        $(this).parent().removeClass("bg-warning");
      }
		},
		select: function(event, ui) {
			this.value = ui.item.value;
			$.ajax({
        url: resource + $(this).closest("tr").data("id"),
        type: 'PUT',
        data: $(this).serialize(),
      }).done(function () {
				$("#listModal").find(".modal-body").load(relatedUrl);
				$("#listModal").find(".alert").removeClass("alert-danger").html("");
			});
		}
	});
});

$('#listModal').on("click", 'input[name="actor_id"].noformat, input[name="company_id"].noformat', function() {
	$(this).autocomplete({
		minLength: 2,
		source: "/actor/autocomplete",
		change: function (event, ui) {
			if (!ui.item) {
        $(this).val("");
        $(this).parent().removeClass("bg-warning");
      }
		},
		select: function(event, ui) {
			this.value = ui.item.value;
			$.ajax({
        url: resource + $(this).closest("tr").data("id"),
        type: 'PUT',
        data: $(this).serialize(),
      }).done(function () {
				$("#listModal").find(".modal-body").load(relatedUrl);
				$("#listModal").find(".alert").removeClass("alert-danger").html("");
			});
		}
	});
});

$('#listModal').on("click",'input[type="checkbox"]', function() {
	var flag = 0;
	if ( $(this).is(":checked") ) flag = 1;
  var data = $(this).attr("name") + "=" + flag;
	$.ajax({
    url: resource + $(this).closest("tr").data("id"),
    type: 'PUT',
    data: data,
  }).done(function () {
		$("#listModal").find(".modal-body").load(relatedUrl);
		$("#listModal").find(".alert").removeClass("alert-danger").html("");
	})
});

$('#listModal').on("click", 'input[name="alt_matter_id"].noformat', function() {
	$(this).autocomplete({
		minLength: 2,
		source: "/matter/autocomplete",
		change: function (event, ui) {
      if (!ui.item) {
        $(this).val("");
        $(this).parent().removeClass("bg-warning");
      }
		},
		select: function(event, ui) {
			this.value = ui.item.value;
      $.ajax({
        url: resource + $(this).closest("tr").data("id"),
        type: 'PUT',
        data: $(this).serialize(),
      }).done(function () {
				$("#listModal").find(".modal-body").load(relatedUrl);
				$("#listModal").find(".alert").removeClass("alert-danger").html("");
			});
		}
	});
});


// Specific processing in the actor/role list modal

$("#listModal").on("click", "#removeActor", function() {
  $.ajax({
    url: '/actor-pivot/' + $(this).closest("tr").data("id"),
    type: 'DELETE',
  }).done(function() {
    $('#listModal').find(".modal-body").load(relatedUrl);
  });
  return false;
});

// Specific processing in the task list modal

$("#listModal").on("click", "#addTaskToEvent", function() {
	$(this).parents("tbody").append( $("#addTaskFormTemplate").html() );
 	$("#addTaskForm").find('input[name="trigger_id"]').val( $(this).data("event_id") );
 	$("#addTaskForm").find('input[name="name"]').focus().autocomplete({
		minLength: 2,
		source: "/event-name/autocomplete/1",
		select: function( event, ui ) {
			$("#addTaskForm").find('input[name="code"]').val( ui.item.code );
		},
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		}
  });
 	$("#addTaskForm").find('input[name="assigned_to"]').autocomplete({
		minLength: 2,
		source: "/user/autocomplete",
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		}
  });
 	$("#addTaskForm").find('input[name$="date"]').datepicker({
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true,
  });
});

$("#listModal").on("click", "#addTaskSubmit", function() {
	var request = $("#addTaskForm").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
	$.post('/task', request)
	.done(function() {
		$('#listModal').find(".modal-body").load("/matter/" + matter_id + "/tasks");
	}).fail(function(errors) {
		$.each(errors.responseJSON.errors, function (key, item) {
      $("#addTaskForm").find('input[name=' + key + ']').attr("placeholder", item).addClass('is-invalid');
		});
	});
});

$("#listModal").on("click", "#deleteTask", function() {
	$.ajax({
    url: '/task/' + $(this).closest("tr").data("id"),
    type: 'DELETE',
  }).done(function() {
		$('#listModal').find(".modal-body").load(relatedUrl);
	});
});

$("#listModal").on("click","#deleteEvent", function() {
	if ( confirm("Deleting the event will also delete the linked tasks. Continue anyway?") ) {
    $.ajax({
      url: '/event/' + $(this).data('event_id'),
      type: 'DELETE',
    }).done(function() {
  		$('#listModal').find(".modal-body").load(relatedUrl);
  	});
	}
});

// Specific processing in the event list modal

$("#listModal").on("click", "#addEvent", function() {
	$("#listModal").find("tbody").append( $("#addEventFormTemplate").html() );
   	$("#addEventForm").find('input[name="name"]').focus().autocomplete({
		minLength: 2,
		source: "/event-name/autocomplete/0",
		select: function( event, ui ) {
			$("#addEventForm").find('input[name="code"]').val( ui.item.code );
		},
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		}
	});
   	$("#addEventForm").find('input[name="alt_matter_id"]').autocomplete({
		minLength: 2,
		source: "/matter/autocomplete",
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		}
	});
   	$("#addEventForm").find('input[name$="date"]').datepicker({
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true,
	});
});

$("#listModal").on("click", "#addEventSubmit", function() {
	var request = $("#addEventForm").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
	$.post('/event', request)
	.done(function() {
		$('#listModal').find(".modal-body").load("/matter/" + matter_id + "/events");
	}).fail(function(errors) {
		$.each(errors.responseJSON.errors, function (key, item) {
			$("#addEventForm").find('input[name=' + key + ']').attr("placeholder", item).addClass('is-invalid');
		});
	});
});

// Classifiers modal processing

$('#classifiersModal').on("keypress", "input.noformat", function (e) {
	if (e.which == 13) {
		e.preventDefault();
    $.ajax({
      url: '/classifier/'+ $(this).closest("tr").data("classifier_id"),
      type: 'PUT',
      data: $(this).serialize(),
    }).done(function () {
			$("td.bg-warning").removeClass("bg-warning");
			$("#classifiersModal").find(".alert").removeClass("alert-danger").html("");
		}).fail(function(errors) {
			$.each(errors.responseJSON.errors, function (key, item) {
				$("#classifiersModal").find(".modal-footer .alert").html(item).addClass('alert-danger');
			});
		});
	} else
		$(this).parent("td").addClass("bg-warning");
});

$('#classifiersModal').on("click", 'input[name="lnk_matter_id"].noformat', function() {
	$(this).autocomplete({
		minLength: 2,
		source: "/matter/autocomplete",
		change: function (event, ui) {
      if (!ui.item) {
        $(this).val("");
        $(this).parent().removeClass("bg-warning");
      }
		},
		select: function(event, ui) {
			this.value = ui.item.value;
      $.ajax({
        url: '/classifier/'+ $(this).closest("tr").data("classifier_id"),
        type: 'PUT',
        data: $(this).serialize(),
      }).done(function () {
				$('#classifiersModal').load("/matter/" + matter_id + " #classifiersModal > div");
				$("#classifiersModal").find(".alert").removeClass("alert-danger").html("");
			});
		}
	});
});

$("#classifiersModal").on("shown.bs.collapse", "#addClassifierForm", function() {
 	$("#addClassifierForm").find('input[name="type"]').autocomplete({
		minLength: 0,
		source: "/classifier-type/autocomplete/0",
		select: function( event, ui ) {
			$("#addClassifierForm").find('input[name="type_code"]').val( ui.item.code );
		},
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		}
	}).focus(function () {
  // Forces search with no characters upon focus
    $(this).autocomplete("search", "");
  });
 	$("#addClassifierForm").find('input[name="lnk_matter_id"]').autocomplete({
		minLength: 2,
		source: "/matter/autocomplete",
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		}
  });
});

$("#classifiersModal").on("click", "#addClassifierSubmit", function() {
	var request = $("#addClassifierForm").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
	$.post('/classifier', request)
	.done(function() {
		$('#classifiersModal').load("/matter/" + matter_id + " #classifiersModal > div");
	}).fail(function(errors) {
		$.each(errors.responseJSON.errors, function (key, item) {
			$("#addClassifierForm").find('input[name=' + key + ']').attr("placeholder", item).addClass('is-invalid');
		});
	});
});

$("#classifiersModal").on("click", "#deleteClassifier", function() {
	$.ajax({
    url: '/classifier/' + $(this).closest("tr").data("classifier_id"),
    type: 'DELETE',
  }).done(function() {
		$('#classifiersModal').load("/matter/" + matter_id + " #classifiersModal > div");
	});
	return false;
});
</script>
