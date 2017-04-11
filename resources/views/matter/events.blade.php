<script>
$(document).ready(function() {

	$('input[type="date"]').datepicker({
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true,
		onSelect: function(date, instance) {
			$(this).focus();
			$(this).parent("td").addClass("bg-warning");
		}
	});
	
	$("#allEventsModal").find('input.noformat').keypress(function (e) {
		if (e.which == 13) {
			e.preventDefault();
			var data = $.param({ _token: "{{ csrf_token() }}", _method: "PUT" }) + "&" + $(this).serialize();
			$.post('/event/'+ $(this).closest("tr").data("event_id"), data)
			.done(function () {
				$("#allEventsModal").find(".modal-body").load("/matter/{{ $matter->id }}/events");
				$("#allEventsModal").find(".alert").removeClass("alert-danger").html("");
			}).fail(function(errors) {
				$.each(errors.responseJSON, function (key, item) {
					$("#allEventsModal").find(".modal-footer .alert").html(item).addClass("alert-danger");
				});
			});
		}
		$(this).parent("td").addClass("bg-warning");   
	});
	
	$('input[name="alt_matter_id"]').autocomplete({
		minLength: 2,
		source: "/matter/search",
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
			if ($(this).hasClass("noformat")) $(this).parent().addClass("alert alert-warning");
		}
	});
});
</script>

<table class="table table-hover table-condensed">
	<thead>
		<tr>
			<th>Event</th>
			<th>Date</th>
			<th>Number</th> 
			<th>Notes</th> 
			<th>Refers to</th> 
			<th style="width: 24px;">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	@foreach ( $events as $event )
		<tr class="reveal-hidden" data-event_id="{{ $event->id }}">
			<td>
				{{ $event->info->name }}
			</td> 
			<td>
				<input type="date" class="form-control noformat" size="10" name="event_date" value="{{ $event->event_date }}"/>
			</td>
			<td>
				<input type="text" class="form-control noformat" size="16" name="detail" value="{{ $event->detail }}"/>
			</td>
			<td>
				<input type="text" class="form-control noformat" name="notes" value="{{ $event->notes }}"/>
			</td>
			<td class="ui-front">
				<input type="text" class="form-control noformat" size="10" name="alt_matter_id" value="{{ $event->altMatter ? $event->altMatter->uid : '' }}"/>
			</td>
			<td>
				<a href="javascript:void(0);" class="hidden-action" id="deleteEvent" data-id="{{ $event->id }}" title="Delete event">
					<span class="glyphicon glyphicon-trash text-danger"></span>
				</a>
			</td>
		</tr>
	@endforeach
	</tbody>
</table>

<template id="addEventFormTemplate">
	<tr>
		<td colspan="6">
			<form id="addEventForm" class="form-inline">
				{{ csrf_field() }}
				<input type="hidden" name="code" value="" id="event_code" />
				<div class="form-group form-group-sm ui-front">
					<input type="text" class="form-control" name="name" placeholder="Name"/>
				</div>
				<div class="form-group form-group-sm ui-front">
					<input type="date" class="form-control" name="event_date" placeholder="Date"/>
				</div>
				<div class="form-group form-group-sm">
					<input type="text" class="form-control" name="detail" placeholder="Detail"/>
					<input type="text" class="form-control" name="notes" placeholder="Notes"/>
					<input type="text" class="form-control" name="alt_matter_id" placeholder="Linked to"/>
					<button type="button" class="btn btn-primary" id="addEventSubmit"><span class="glyphicon glyphicon-ok"></span></button>
					<button type="button" class="btn btn-primary" id="addEventCancel"><span class="glyphicon glyphicon-remove"></span></button>
				</div>
			</form>
		</td>
	</tr>
</template>
