<script>
var relatedUrl = ""; // Identifies what to display in the Ajax-filled modal. Updated according to the href attribute used for triggering the modal
var csrf_token = $('input[name="_token"]').val();

function refreshRuleList() {
    var url = '/rule?' + $("#filter").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
    $('#rule-list').load(url + ' #rule-list > tr', function() { // Refresh all the tr's in tbody#matter-list
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
	// Display the modal view for creation of rule
    $("#addModal").on("show.bs.modal", function(event) {
    	relatedUrl = $(event.relatedTarget).attr("href");
    	resource = $(event.relatedTarget).data("resource");

    	$(this).find(".modal-title").text( $(event.relatedTarget).attr("title") );
        $(this).find(".modal-body").load(relatedUrl);
    });

});

// Generic in-place edition of fields in a listModal
$('#infoModal').on("focus", 'input[name^="use"].noformat', function() {
    $(this).datepicker({
	dateFormat: 'yy-mm-dd',
	showButtonPanel: true,
	onSelect: function(date, instance) {
	    var data = $.param({ _token: csrf_token, _method: "PUT" }) + "&" + $(this).serialize();
	    $.post(resource + $(this).closest("table").data("id"), data)
	    .done(function () {
		$("#infoModal").find(".modal-body").load(relatedUrl);
		$("#infoModal").find(".alert").removeClass("alert-danger").html("");
	    });
	}
    });
});

$("#infoModal").on("keypress", "input.noformat", function (e) {
	if (e.which == 13) {
		e.preventDefault();
		var data = $.param({ _token: csrf_token, _method: "PUT" }) + "&" + $(this).serialize();
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

$('.filter-input').keyup(_.debounce(function(){
	if($(this).val().length != 0)
	    $(this).css("background-color", "bisque");
	else
	    $(this).css("background-color", "white");
	refreshRuleList();
    }, 500));
    
// Specific in place edition of rule
$('#infoModal').on("click",'input[type="radio"]', function() {
	var mydata = {};
	mydata[this.name] = this.value;
	mydata['_token'] = csrf_token;
	mydata['_method'] ="PUT";
	$.post(resource + $(this).closest("table").data("id"),  mydata )
	.done(function () {
		$("#infoModal").find(".modal-body").load(relatedUrl);
	})
});

$('#infoModal').on("click", 'input[name="for_country"],input[name="for_origin"]', function() {
	$(this).autocomplete({
		minLength: 2,
		source: "/country/autocomplete",
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		},
		select: function(event, ui) {
			this.value = ui.item.value;
			var data = $.param({ _token: csrf_token, _method: "PUT" }) + "&" + $(this).serialize();
			$.post(resource + $(this).closest("table").data("id"), data)
			.done(function () {
				$("#infoModal").find(".modal-body").load(relatedUrl);
				$("#infoModal").find(".alert").removeClass("alert-danger").html("");
			});
		}
	});
});

$('#infoModal').on("click", 'input[name="task"]', function() {
	$(this).autocomplete({
		minLength: 1,
		source: "/task-name/autocomplete/1",
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		},
		select: function(event, ui) {
			this.value = ui.item.value;
			var data = $.param({ _token: csrf_token, _method: "PUT" }) + "&" + $(this).serialize();
			$.post(resource + $(this).closest("table").data("id"), data)
			.done(function () {
				$("#infoModal").find(".modal-body").load(relatedUrl);
				$("#infoModal").find(".alert").removeClass("alert-danger").html("");
			});
		}
	});
});

$('#infoModal').on("click", 'input[name="for_category"]', function() {
        $(this).autocomplete({
                minLength: 2,
                source: "/category/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function(event, ui) {
                        this.value = ui.item.value;
                        var data = $.param({ _token: csrf_token, _method: "PUT" }) + "&" + $(this).serialize();
                        $.post(resource + $(this).closest("table").data("id"), data)
                        .done(function () {
                                $("#infoModal").find(".modal-body").load(relatedUrl);
                                $("#infoModal").find(".alert").removeClass("alert-danger").html("");
                        });
                }
        });
});


$('#infoModal').on("click", 'input[name="for_type"]', function() {
        $(this).autocomplete({
                minLength: 2,
                source: "/type/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function(event, ui) {
                        this.value = ui.item.value;
                        var data = $.param({ _token: csrf_token, _method: "PUT" }) + "&" + $(this).serialize();
                        $.post(resource + $(this).closest("table").data("id"), data)
                        .done(function () {
                                $("#infoModal").find(".modal-body").load(relatedUrl);
                                $("#infoModal").find(".alert").removeClass("alert-danger").html("");
                        });
                } 
        });
});

$('#infoModal').on("click", "input[name$='event'],input[name='abort_on']", function() {
        $(this).autocomplete({
                minLength: 1,
                source: "/event/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function(event, ui) {
                        this.value = ui.item.value;
                        var data = $.param({ _token: csrf_token, _method: "PUT" }) + "&" + $(this).serialize();
                        $.post(resource + $(this).closest("table").data("id"), data)
                        .done(function () {
                                $("#infoModal").find(".modal-body").load(relatedUrl);
                                $("#infoModal").find(".alert").removeClass("alert-danger").html("");
                        });
                } 
        });
});

$('#infoModal').on("click", 'input[name="responsible"].noformat', function() {
         $(this).autocomplete({
                minLength: 2,
                source: "/user/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function(event, ui) {
                        this.value = ui.item.value;
                        var data = $.param({ _token: csrf_token, _method: "PUT" }) + "&" + $(this).serialize();
                        $.post(resource + $(this).closest("table").data("id"), data)
                        .done(function () {
                                $("#infoModal").find(".modal-body").load(relatedUrl);
                                $("#infoModal").find(".alert").removeClass("alert-danger").html("");
                        });
                } 
        });
});

$('.delete-from-list').click(function() {
    var del_conf = confirm("Deleting rule from table.");
    if(del_conf == 1) {
	var data = $.param({ _token: csrf_token, _method: "DELETE" }) ;
	$.post('/rule/' + $(this).closest("tr").data("id"), data).done(function(){
		$('#listModal').find(".modal-body").load(relatedUrl);
		});
	refreshRuleList();
    }
    return false;
});


// Edition of fields in new rule modal window

$('#infoModal').on("focus", 'input[name^="use"].noformat', function() {
    $(this).datepicker({
	dateFormat: 'yy-mm-dd',
	showButtonPanel: true,
	onSelect: function(date, instance) {
	    var data = $.param({ _token: csrf_token, _method: "PUT" }) + "&" + $(this).serialize();
	    $.post(resource + $(this).closest("table").data("id"), data)
	    .done(function () {
		$("#infoModal").find(".modal-body").load(relatedUrl);
		$("#infoModal").find(".alert").removeClass("alert-danger").html("");
	    });
	}
    });
});

$("#infoModal").on("keypress", "input.noformat", function (e) {
	if (e.which == 13) {
		e.preventDefault();
		var data = $.param({ _token: csrf_token, _method: "PUT" }) + "&" + $(this).serialize();
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

$('.filter-input').keyup(_.debounce(function(){
	if($(this).val().length != 0)
	    $(this).css("background-color", "bisque");
	else
	    $(this).css("background-color", "white");
	refreshRuleList();
    }, 500));

$('#infoModal').on("click", 'input[name="for_country"],input[name="for_origin"]', function() {
	$(this).autocomplete({
		minLength: 2,
		source: "/country/autocomplete",
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		},
		select: function(event, ui) {
			this.value = ui.item.value;
			var data = $.param({ _token: csrf_token, _method: "PUT" }) + "&" + $(this).serialize();
			$.post(resource + $(this).closest("table").data("id"), data)
			.done(function () {
				$("#infoModal").find(".modal-body").load(relatedUrl);
				$("#infoModal").find(".alert").removeClass("alert-danger").html("");
			});
		}
	});
});

$('#infoModal').on("click", 'input[name="task"]', function() {
	$(this).autocomplete({
		minLength: 1,
		source: "/task-name/autocomplete/1",
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		},
		select: function(event, ui) {
			this.value = ui.item.value;
			var data = $.param({ _token: csrf_token, _method: "PUT" }) + "&" + $(this).serialize();
			$.post(resource + $(this).closest("table").data("id"), data)
			.done(function () {
				$("#infoModal").find(".modal-body").load(relatedUrl);
				$("#infoModal").find(".alert").removeClass("alert-danger").html("");
			});
		}
	});
});

$('#infoModal').on("click", 'input[name="for_category"]', function() {
        $(this).autocomplete({
                minLength: 2,
                source: "/category/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function(event, ui) {
                        this.value = ui.item.value;
                        var data = $.param({ _token: csrf_token, _method: "PUT" }) + "&" + $(this).serialize();
                        $.post(resource + $(this).closest("table").data("id"), data)
                        .done(function () {
                                $("#infoModal").find(".modal-body").load(relatedUrl);
                                $("#infoModal").find(".alert").removeClass("alert-danger").html("");
                        });
                }
        });
});


$('#infoModal').on("click", 'input[name="for_type"]', function() {
        $(this).autocomplete({
                minLength: 2,
                source: "/type/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function(event, ui) {
                        this.value = ui.item.value;
                        var data = $.param({ _token: csrf_token, _method: "PUT" }) + "&" + $(this).serialize();
                        $.post(resource + $(this).closest("table").data("id"), data)
                        .done(function () {
                                $("#infoModal").find(".modal-body").load(relatedUrl);
                                $("#infoModal").find(".alert").removeClass("alert-danger").html("");
                        });
                } 
        });
});

$('#infoModal').on("click", "input[name$='event'],input[name='abort_on']", function() {
        $(this).autocomplete({
                minLength: 1,
                source: "/event/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function(event, ui) {
                        this.value = ui.item.value;
                        var data = $.param({ _token: csrf_token, _method: "PUT" }) + "&" + $(this).serialize();
                        $.post(resource + $(this).closest("table").data("id"), data)
                        .done(function () {
                                $("#infoModal").find(".modal-body").load(relatedUrl);
                                $("#infoModal").find(".alert").removeClass("alert-danger").html("");
                        });
                } 
        });
});

// For creation rule modal view

$('#addModal').on("click", 'input[name="task_new"]', function() {
         $(this).autocomplete({
                minLength: 1,
                source: "/task-name/autocomplete/1",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function (event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.label);
                        $("input[name='task']").val( ui.item.value);
                }
        });
});

$('#addModal').on("click", 'input[name="for_country_new"]', function() {
         $(this).autocomplete({
                minLength: 1,
                source: "/country/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function (event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.label);
                        $("input[name='for_country']").val( ui.item.value);
                }
        });
});

$('#addModal').on("click", 'input[name="for_origin_new"]', function() {
         $(this).autocomplete({
                minLength: 1,
                source: "/country/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function (event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.label);
                        $("input[name='for_origin']").val( ui.item.value);
                }
        });
});

$('#addModal').on("click", 'input[name="for_category_new"]', function() {
         $(this).autocomplete({
                minLength: 1,
                source: "/category/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function (event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.label);
                        $("input[name='for_category']").val( ui.item.value);
                }
        });
});

$('#addModal').on("click", 'input[name="for_type_new"]', function() {
         $(this).autocomplete({
                minLength: 1,
                source: "/type/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function (event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.label);
                        $("input[name='for_type']").val( ui.item.value);
                }
        });
});

$('#addModal').on("click", 'input[name="trigger_event_new"]', function() {
         $(this).autocomplete({
                minLength: 1,
                source: "/task-name/autocomplete/0",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function (event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.label);
                        $("input[name='trigger_event']").val( ui.item.value);
                }
        });
});

$('#addModal').on("click", 'input[name="condition_event_new"]', function() {
         $(this).autocomplete({
                minLength: 1,
                source: "/task-name/autocomplete/0",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function (event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.label);
                        $("input[name='condition_event']").val( ui.item.value);
                }
        });
});
$('#addModal').on("click", 'input[name="responsible_new"]', function() {
         $(this).autocomplete({
                minLength: 2,
                source: "/user/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function (event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.label);
                        $("input[name='responsible']").val( ui.item.value);
                }
        });
});

$('#addModal').on("click", 'input[name="abort_on_new"]', function() {
         $(this).autocomplete({
                minLength: 1,
                source: "/event/autocomplete",
                change: function (event, ui) {
                        if (!ui.item) $(this).val("");
                },
                select: function (event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.label);
                        $("input[name='abort_on']").val( ui.item.value);
                }
        });
});

$(document).on("submit", "#createRuleForm", function(e) {
	e.preventDefault();
	var $form = $(this);
	var request = $("#createRuleForm").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
	var data = $.param({ _token: csrf_token, _method: "PUT" }) + "&" + request;
	console.log(request);
	$.post('/ruleadd', data,function(response) {
		if(response.success) {
			window.alert("Rule created.");
			$('#addModal').modal("hide");}
		else {
		associate_errors(response['errors'],$form);
		}
	});
});

function associate_errors(errors,$form) {
	console.log(errors);
	$form.find('.form-group').removeClass('has-errors').find('.help-text').text();
	for(index in errors) {
		value = errors[index][0];
		console.log(index, value);
		document.getElementById('error-box').innerHTML+=ivalue + '<BR />';
	};
}
</script>
