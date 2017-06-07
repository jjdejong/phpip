<script>
$(document).ready(function() {

	$('input[type="date"].noformat').datepicker({
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true,
		onSelect: function(date, instance) {
			var data = $.param({ _token: "{{ csrf_token() }}", _method: "PUT" }) + "&" + $(this).serialize();
			$.post('/event/'+ $(this).closest("tr").data("event_id"), data)
			.done(function () {
				$("#listModal").find(".modal-body").load("/matter/{{ $matter->id }}/events");
				$("#listModal").find(".alert").removeClass("alert-danger").html("");
			});
		}
	});
	
	$('input.noformat').keypress(function (e) {
		if (e.which == 13) {
			e.preventDefault();
			var data = $.param({ _token: "{{ csrf_token() }}", _method: "PUT" }) + "&" + $(this).serialize();
			$.post('/event/'+ $(this).closest("tr").data("event_id"), data)
			.done(function () {
				$("td.bg-warning").removeClass("bg-warning");
				$("#listModal").find(".alert").removeClass("alert-danger").html("");
			}).fail(function(errors) {
				$.each(errors.responseJSON, function (key, item) {
					$("#listModal").find(".modal-footer .alert").html(item).addClass("alert-danger");
				});
			});
		} else
			$(this).parent("td").addClass("bg-warning");   
	});
	
	$('input[name="alt_matter_id"].noformat').autocomplete({
		minLength: 2,
		source: "/matter/autocomplete",
		change: function (event, ui) {
			if (!ui.item) $(this).val("");
		},
		select: function(event, ui) {
			this.value = ui.item.value;
			var data = $.param({ _token: "{{ csrf_token() }}", _method: "PUT" }) + "&" + $(this).serialize();
			$.post('/event/'+ $(this).closest("tr").data("event_id"), data)
			.done(function () {
				$("#listModal").find(".modal-body").load("/matter/{{ $matter->id }}/events");
				$("#listModal").find(".alert").removeClass("alert-danger").html("");
			});
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
			<th>
				Refers to
				<a href="javascript:void(0);" id="addEvent" title="Add event">
					<span class="glyphicon glyphicon-plus-sign pull-right"></span>
				</a>
			</th> 
		</tr>
	</thead>
	<tbody>
	@foreach ( $events as $event )
		<tr data-event_id="{{ $event->id }}">
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
		</tr>
	@endforeach
	</tbody>
</table>

<template id="addEventFormTemplate">
	<tr>
		<td colspan="5">
			<form id="addEventForm" class="form-inline">
				{{ csrf_field() }}
				<input type="hidden" name="matter_id" value="{{ $matter->id }}"/>
				<input type="hidden" name="code" value=""/>
				<div class="form-group form-group-sm ui-front">
					<input type="text" class="form-control" size="16" name="name" placeholder="Name"/>
				</div>
				<div class="form-group form-group-sm ui-front">
					<input type="date" class="form-control" size="10" name="event_date" placeholder="Date"/>
				</div>
				<div class="form-group form-group-sm">
					<input type="text" class="form-control" size="16" name="detail" placeholder="Detail"/>
					<input type="text" class="form-control" name="notes" placeholder="Notes"/>
				</div>
				<div class="input-group input-group-sm ui-front">
					<input type="text" class="form-control" size="16" name="alt_matter_id" placeholder="Linked to"/>
					<div class="input-group-btn">
						<button type="button" class="btn btn-primary" id="addEventSubmit"><span class="glyphicon glyphicon-ok"></span></button>
						<button type="button" class="btn btn-default" onClick="$(this).parents('tr').html('')"><span class="glyphicon glyphicon-remove"></span></button>
					</div>
				</div>
			</form>
		</td>
	</tr>
</template>
