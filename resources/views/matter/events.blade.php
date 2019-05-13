<table class="table table-hover table-sm">
    <thead class="thead-light">
        <tr>
            <th>Event</th>
            <th>Date</th>
            <th>Number</th>
            <th>Notes</th>
            <th>
                Refers to
                <a data-toggle="collapse" href="tr.collapse" class="badge badge-info float-right" id="addEvent" title="Add event">
                    &plus;
                </a>
            </th>
        </tr>
        <tr class="collapse">
            <td colspan="5">
                <form id="addEventForm" class="form-inline">
                    <input type="hidden" name="matter_id" value="{{ $matter->id }}">
                    <input type="hidden" name="code" value="">
                    <div class="input-group">
                        <div class="ui-front">
                            <input type="text" class="form-control form-control-sm" name="name">
                            <input type="date" class="form-control form-control-sm" name="event_date">
                        </div>
                        <input type="text" class="form-control form-control-sm" name="detail">
                        <input type="text" class="form-control form-control-sm" name="notes">
                        <div class="ui-front">
                            <input type="text" class="form-control form-control-sm" name="alt_matter_id">
                        </div>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary btn-sm" id="addEventSubmit">&check;</button>
                            <button type="reset" class="btn btn-outline-primary btn-sm">&times;</button>
                        </div>
                    </div>
                </form>
            </td>
        </tr>
    </thead>
    <tbody id="eventList">
        @foreach ( $events as $event )
        <tr data-id="{{ $event->id }}">
            <td>{{ $event->info->name }}</td>
            <td><input type="date" class="form-control noformat" name="event_date" value="{{ $event->event_date }}"></td>
            <td><input type="text" class="form-control noformat" size="16" name="detail" value="{{ $event->detail }}"></td>
            <td><input type="text" class="form-control noformat" name="notes" value="{{ $event->notes }}"></td>
            <td class="ui-front"><input type="text" class="form-control noformat" size="10" name="alt_matter_id" value="{{ $event->altMatter ? $event->altMatter->uid : '' }}"></td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
    $("#addEventForm").find('input[name="name"]').focus().autocomplete({
        minLength: 2,
        source: "/event-name/autocomplete/0",
        select: (event, ui) => {
            $("#addEventForm").find('input[name="code"]').val(ui.item.code);
        },
        change: (event, ui) => {
            if (!ui.item)
                this.value = "";
        }
    });
    
    $("#addEventForm").find('input[name="alt_matter_id"]').autocomplete({
        minLength: 2,
        source: "/matter/autocomplete",
        change: (event, ui) => {
            if (!ui.item)
                this.value = "";
        }
    });

    document.getElementById('addEventSubmit').onclick = () => {
        var request = $("#addEventForm").find("input").filter(function () {
            return $(this).val().length > 0;
        }).serialize(); // Filter out empty values
        $.post('/event', request)
            .done( () => {
                $('#listModal').find(".modal-body").load("/matter/" + matter_id + "/events");
            }).fail( (errors) => {
                $.each(errors.responseJSON.errors, (key, item) => {
                    $("#addEventForm").find('input[name=' + key + ']').attr("placeholder", item).addClass('is-invalid');
            });
        });
    };
</script>
