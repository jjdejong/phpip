<script>

    function refreshActorList() {
        var url = '/actor?' + $("#filter").find("input").filter(function () {
            return $(this).val().length > 0
        }).serialize(); // Filter out empty values
        $('#actor-list').load(url + ' #actor-list > tr', function () { // Refresh all the tr's in tbody#actor-list
            window.history.pushState('', 'phpIP', url);
        })
    }

    $(document).ready(function () {

        // Reload the actors list when closing the modal window
        $("#ajaxModal").on("hide.bs.modal", function (event) {
            refreshActorList();
        });

        // Display actor depencies in dropdown
        $("#ajaxModal").on("show.bs.dropdown", "#usedInDropdown", function (event) {
            $(this).find(".dropdown-menu").load(event.relatedTarget.href);
        });

    });

// Address and notes edition
    /*$("#ajaxModal").on("keyup", "textarea.noformat", function () {
        var field = $(this).data('field');
        $(field).removeClass('hidden-action');
        $(this).addClass('changed');
    });

    $("#ajaxModal").on("click", "button.area", function () {
        var field = $(this).data('field');
        var areaId = '#' + field;
        var dataString = field + "=" + $(areaId).val();
        if ($(areaId).hasClass('changed')) {
            $.ajax({
                type: 'PUT',
                url: "/actor/" + $(this).closest("table").data("id"),
                data: dataString,
            });
            this.classList.add('hidden-action');
            $(areaId).removeClass('border', 'border-info');
        }
        return false;
    });*/

//$('.filter-input:input').onclick( function() {
    $('#physical').on('change', function () {
        refreshActorList();
    });

    $('#legal').on('change', function () {
        refreshActorList();
    });

    $('#both').on('change', function () {
        refreshActorList();
    });

    $('.filter-input').keyup(debounce(function () {
        if ($(this).val().length != 0)
            $(this).css("background-color", "bisque");
        else
            $(this).css("background-color", "white");
        refreshActorList();
    }, 500));

// Specific in place edition of actor
    $('#ajaxModal').on("click", 'input[type="radio"]', function () {
        var mydata = {};
        mydata[this.name] = this.value;
        mydata['_method'] = "PUT";
        $.post(resource + $(this).closest("table").data("id"), mydata)
            .done(function () {
                $("#ajaxModal").find(".modal-body").load(relatedUrl);
            })
    });

    $('#actor-list').on("click", '.delete-from-list', function () {
        var del_conf = confirm("Deleting actor from table?");
        if (del_conf == 1) {
            var data = $.param({_method: "DELETE"});
            $.post('/actor/' + $(this).closest("tr").data("id"), data).done(function () {
                refreshActorList();
            })
            .fail(data => alert(data.responseJSON.message));
        }
        return false;
    });

    $('#ajaxModal').on("click", '.delete-actor', function () {
        var del_conf = confirm("Deleting actor from table?");
        if (del_conf == 1) {
            var data = $.param({_method: "DELETE"});
            $.post('/actor/' + $(this).data("id"), data).done(function () {
                $('#ajaxModal').find(".modal-body").load(relatedUrl);
            })
            .fail(data => alert(data.responseJSON.message));;
        }
        return false;
    });

// For creation rule modal view

// THESE ALL NEED TO BE CHANGED TO USE ajaxModal

    $('#addModal').on("click", 'input[name="nationality_new"]', function () {
        $(this).autocomplete({
            minLength: 1,
            source: "/country/autocomplete",
            change: function (event, ui) {
                if (!ui.item)
                    $(this).val("");
            },
            select: function (event, ui) {
                event.preventDefault();
                $(this).val(ui.item.value);
                $('input[name="nationality"]').val(ui.item.id);
            }
        });
    });

    $('#addModal').on("click", 'input[name="country_new"]', function () {
        $(this).autocomplete({
            minLength: 1,
            source: "/country/autocomplete",
            change: function (event, ui) {
                if (!ui.item)
                    $(this).val("");
            },
            select: function (event, ui) {
                event.preventDefault();
                $(this).val(ui.item.value);
                $('input[name="country"]').val(ui.item.id);
            }
        });
    });

    $('#addModal').on("click", 'input[name="country_mailing_new"]', function () {
        $(this).autocomplete({
            minLength: 1,
            source: "/country/autocomplete",
            change: function (event, ui) {
                if (!ui.item)
                    $(this).val("");
            },
            select: function (event, ui) {
                event.preventDefault();
                $(this).val(ui.item.value);
                $('input[name="country_mailing"]').val(ui.item.id);
            }
        });
    });

    $('#addModal').on("click", 'input[name="country_billing_new"]', function () {
        $(this).autocomplete({
            minLength: 1,
            source: "/country/autocomplete",
            change: function (event, ui) {
                if (!ui.item)
                    $(this).val("");
            },
            select: function (event, ui) {
                event.preventDefault();
                $(this).val(ui.item.value);
                $('input[name="country_billing"]').val(ui.item.id);
            }
        });
    });

    $('#addModal').on("click", 'input[name="drole_new"]', function () {
        $(this).autocomplete({
            minLength: 1,
            source: "/role/autocomplete",
            change: function (event, ui) {
                if (!ui.item)
                    $(this).val("");
            },
            select: function (event, ui) {
                event.preventDefault();
                $(this).val(ui.item.label);
                $('input[name="default_role"]').val(ui.item.value);
            }
        });
    });
    $('#addModal').on("click", 'input[name="parent_new"]', function () {
        $(this).autocomplete({
            minLength: 1,
            source: "/actor/autocomplete",
            change: function (event, ui) {
                if (!ui.item)
                    $(this).val("");
            },
            select: function (event, ui) {
                event.preventDefault();
                $(this).val(ui.item.label);
                $('input[name="parent_id"]').val(ui.item.id);
            }
        });
    });

    $('#addModal').on("click", 'input[name="company_new"]', function () {
        $(this).autocomplete({
            minLength: 1,
            source: "/actor/autocomplete",
            change: function (event, ui) {
                if (!ui.item)
                    $(this).val("");
            },
            select: function (event, ui) {
                event.preventDefault();
                $(this).val(ui.item.label);
                $('input[name="company_id"]').val(ui.item.id);
            }
        });
    });

    $('#addModal').on("click", 'input[name="site_new"]', function () {
        $(this).autocomplete({
            minLength: 1,
            source: "/actor/autocomplete",
            change: function (event, ui) {
                if (!ui.item)
                    $(this).val("");
            },
            select: function (event, ui) {
                event.preventDefault();
                $(this).val(ui.item.label);
                $('input[name="site_id"]').val(ui.item.id);
            }
        });
    });

    $(document).on("submit", "#createActorForm", function (e) {
        e.preventDefault();
        var $form = $(this);
        var request = $("#createActorForm").find("input").filter(function () {
            return $(this).val().length > 0
        }).serialize(); // Filter out empty values
        request = request + "&" + $("#createActorForm").find("textarea").filter(function () {
            return $(this).val().length > 0
        }).serialize();
        var data = request;
        console.log(request);
        $.post('/actor', data, function (response) {
            if (response.success) {
                window.alert("Actor created.");
                $('#addModal').modal("hide");
                refreshActorList();
            } else {
                associate_errors(response['errors'], $form);
            }
        });
    });

    function associate_errors(errors, $form) {
        $form.find('.form-control').removeClass('is-invalid').attr("placeholder", "");
        for (index in errors) {
            value = errors[index][0];
            $form.find('input[name=' + index + '_new]').attr("placeholder", value).attr("title", value).addClass('is-invalid');
            $form.find('input[name=' + index + ']').attr("placeholder", value).attr("title", value).addClass('is-invalid');
        };
    }
</script>
