<script>
var relatedUrl = ""; // Identifies what to display in the Ajax-filled modal. Updated according to the href attribute used for triggering the modal

function refreshActorList() {
    var url = '/actor?' + $("#filter").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
    $('#actor-list').load(url + ' #actor-list > tr', function() { // Refresh all the tr's in tbody#actor-list
	window.history.pushState('', 'phpIP' , url);
    })
}

$(document).ready(function() {

	// Ajax fill the opened modal and set global parameters
    $("#infoModal").on("show.bs.modal", function(event) {
    	relatedUrl = $(event.relatedTarget).attr("href");
    	resource = $(event.relatedTarget).data("resource");
    	$(this).find(".modal-title").text( $(event.relatedTarget).attr("title") );
        $(this).find(".modal-body").load(relatedUrl);
    });
    // Reload the actors list when closing the modal window
    $("#infoModal").on("hide.bs.modal", function(event) {
    	refreshActorList();
    });

	// Display the modal view for creation of rule
    $("#addModal").on("show.bs.modal", function(event) {
    	relatedUrl = $(event.relatedTarget).attr("href");
    	resource = $(event.relatedTarget).data("resource");
    	$(this).find(".modal-title").text( $(event.relatedTarget).attr("title") );
        $(this).find(".modal-body").load(relatedUrl);
    });
    // Reload the actors list when closing the modal window
    $("#infoModal").on("hidden.bs.modal", function(event) {
    	refreshActorList();
    });

    $("#usedModal").on("hidden.bs.modal", function(event) {
        $('#infoModal').modal('hide');
    	refreshActorList();
    });

	// Display the modal view for used in
    $("#usedModal").on("show.bs.modal", function(event) {
    	relatedUrl = $(event.relatedTarget).attr("href");

    	$(this).find(".modal-title").text( $(event.relatedTarget).attr("title") );
        $(this).find(".modal-body").load(relatedUrl);
    });

});

// Generic in-place edition of fields in a infoModal

$("#infoModal").on("keypress", "input.editable", function (e) {
	if (e.which == 13) {
		e.preventDefault();
		var data = $.param({ _method: "PUT" }) + "&" + $(this).serialize();
		$.post(resource + $(this).closest("table").data("id"), data)
		.done(function () {
			$("#infoModal").find(".modal-body").load(relatedUrl);
			$("#infoModal").find(".alert").removeClass("alert-danger").html("");
		}).fail(function(errors) {
			$.each(errors.responseJSON, function (key, item) {
				$("#infoModal").find(".modal-footer .alert").html(item).addClass("alert-danger");
			});
		});
	} else
		$(this).parent("td").addClass("bg-warning");
});

// Address and notes edition
$("#infoModal").on("keyup", "textarea.editable", function () {
    var field = $(this).data('field');
	$(field).removeClass('hidden-action');
	$(this).addClass('changed');
});

$("#infoModal").on("click", "button.area", function () {
    var field = $(this).data('field');
    var areaId = '#'+field
    var dataString = field + "=" + $(areaId).val();
	if ($(areaId).hasClass('changed')) {
		$.ajax({
			type: 'PUT',
			url: "/actor/" +  $(this).closest("table").data("id"),
			data: dataString,
		});
		$(this).addClass('hidden-action');
		$(areaId).removeClass('changed');
	}
	return false;
});

//$('.filter-input:input').onclick( function() {
$('#physical').on('change', function() {
	refreshActorList();
	}
);

$('#legal').on('change', function() {
	refreshActorList();
	}
);

$('#both').on('change', function() {
	refreshActorList();
	}
);

$('.filter-input').keyup(debounce(function(){
	if($(this).val().length != 0)
	    $(this).css("background-color", "bisque");
	else
	    $(this).css("background-color", "white");
	refreshActorList();
    }, 500));

// Specific in place edition of actor
$('#infoModal').on("click",'input[type="radio"]', function() {
	var mydata = {};
	mydata[this.name] = this.value;
	mydata['_method'] ="PUT";
	$.post(resource + $(this).closest("table").data("id"),  mydata )
	.done(function () {
		$("#infoModal").find(".modal-body").load(relatedUrl);
	})
});

$('#infoModal').on("click", 'input[name^="country"],input[name="nationality"]', function() {
	$(this).autocomplete({
		minLength: 1,
		source: "/country/autocomplete",
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		},
		select: function(event, ui) {
			this.value = ui.item.id;
			var data = $.param({ _method: "PUT" }) + "&" + $(this).serialize();
			$.post(resource + $(this).closest("table").data("id"), data)
			.done(function () {
				$("#infoModal").find(".modal-body").load(relatedUrl);
				$("#infoModal").find(".alert").removeClass("alert-danger").html("");
			});
		}
	});
});

$('#infoModal').on("click", 'input[name="company_id"],input[name="parent_id"],input[name="site_id"]', function() {
	$(this).autocomplete({
		minLength: 2,
		source: "/actor/autocomplete",
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		},
		select: function(event, ui) {
			this.value = ui.item.id;
			var data = $.param({ _method: "PUT" }) + "&" + $(this).serialize();
			$.post(resource + $(this).closest("table").data("id"), data)
			.done(function () {
				$("#infoModal").find(".modal-body").load(relatedUrl);
				$("#infoModal").find(".alert").removeClass("alert-danger").html("");
			});
		}
	});
});

$('#infoModal').on("click", 'input[name="default_role"]', function() {
	$(this).autocomplete({
		minLength: 1,
		source: "/role/autocomplete",
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		},
		select: function(event, ui) {
			this.value = ui.item.value;
			var data = $.param({ _method: "PUT" }) + "&" + $(this).serialize();
			$.post(resource + $(this).closest("table").data("id"), data)
			.done(function () {
				$("#infoModal").find(".modal-body").load(relatedUrl);
				$("#infoModal").find(".alert").removeClass("alert-danger").html("");
			});
		}
	});
});

$('#actor-list').on("click",'.delete-from-list',function() {
    var del_conf = confirm("Deleting actor from table?");
    if(del_conf == 1) {
	var data = $.param({ _method: "DELETE" }) ;
	$.post('/actor/' + $(this).closest("tr").data("id"), data).done(function(){
		refreshActorList();
		});
    }
    return false;
});

$('#infoModal').on("click",'.delete-actor',function() {
    var del_conf = confirm("Deleting actor from table?");
    if(del_conf == 1) {
	var data = $.param({ _method: "DELETE" }) ;
	$.post('/actor/' + $(this).data("id"), data).done(function(){
		$('#listModal').find(".modal-body").load(relatedUrl);
		});
    }
    return false;
});

// For creation rule modal view

$('#addModal').on("click", 'input[name="nationality_new"]', function() {
         $(this).autocomplete({
                minLength: 1,
                source: "/country/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function (event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.value);
                        $('input[name="nationality"]').val(ui.item.id);
                }
        });
});

$('#addModal').on("click", 'input[name="country_new"]', function() {
         $(this).autocomplete({
                minLength: 1,
                source: "/country/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function (event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.value);
                        $('input[name="country"]').val(ui.item.id);
                }
        });
});

$('#addModal').on("click", 'input[name="country_mailing_new"]', function() {
         $(this).autocomplete({
                minLength: 1,
                source: "/country/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function (event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.value);
                        $('input[name="country_mailing"]').val(ui.item.id);
                }
        });
});

$('#addModal').on("click", 'input[name="country_billing_new"]', function() {
         $(this).autocomplete({
                minLength: 1,
                source: "/country/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function (event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.value);
                        $('input[name="country_billing"]').val(ui.item.id);
                }
        });
});

$('#addModal').on("click", 'input[name="drole_new"]', function() {
         $(this).autocomplete({
                minLength: 1,
                source: "/role/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function (event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.label);
                        $('input[name="default_role"]').val(ui.item.value);
                }
        });
});
$('#addModal').on("click", 'input[name="parent_new"]', function() {
         $(this).autocomplete({
                minLength: 1,
                source: "/actor/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function (event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.label);
                        $('input[name="parent_id"]').val(ui.item.id);
                }
        });
});

$('#addModal').on("click", 'input[name="company_new"]', function() {
         $(this).autocomplete({
                minLength: 1,
                source: "/actor/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function (event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.label);
                        $('input[name="company_id"]').val(ui.item.id);
                }
        });
});

$('#addModal').on("click", 'input[name="site_new"]', function() {
         $(this).autocomplete({
                minLength: 1,
                source: "/actor/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function (event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.label);
                        $('input[name="site_id"]').val(ui.item.id);
                }
        });
});

$(document).on("submit", "#createActorForm", function(e) {
	e.preventDefault();
	var $form = $(this);
	var request = $("#createActorForm").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
	request = request +"&" + $("#createActorForm").find("textarea").filter(function(){return $(this).val().length > 0}).serialize();
	var data = request;
	console.log(request);
	$.post('/actor', data,function(response) {
		if(response.success) {
			window.alert("Actor created.");
			$('#addModal').modal("hide");
			refreshActorList();}
		else {
		associate_errors(response['errors'],$form);
		}
	});
});

function associate_errors(errors,$form) {
	$form.find('.form-control').removeClass('is-invalid').attr("placeholder", "");
	for(index in errors) {
		value = errors[index][0];
        $form.find('input[name=' + index + '_new]').attr("placeholder", value).attr("title", value).addClass('is-invalid');
        $form.find('input[name=' + index + ']').attr("placeholder", value).attr("title", value).addClass('is-invalid');
	};
}
</script>
