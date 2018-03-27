<table class="table table-hover table-sm">
	<thead>
		<tr>
			<th>Event</th>
			<th>Date</th>
			<th>Number</th>
			<th>Notes</th>
			<th>
				Refers to
				<a href="javascript:void(0);" id="addEvent" title="Add event">
					<span class="float-right">&oplus;</span>
				</a>
			</th>
		</tr>
	</thead>
	<tbody>
	@foreach ( $events as $event )
		<tr data-id="{{ $event->id }}">
			<td>
				{{ $event->info->name }}
			</td>
			<td>
				<input type="text" class="form-control noformat" size="10" name="event_date" value="{{ $event->event_date }}"/>
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
				<div class="form-group ui-front">
					<input type="text" class="form-control form-control-sm" name="name" placeholder="Name"/>
				</div>
				<div class="form-group ui-front">
					<input type="text" class="form-control form-control-sm" name="event_date" placeholder="Date"/>
				</div>
				<div class="form-group">
					<input type="text" class="form-control form-control-sm" name="detail" placeholder="Detail"/>
					<input type="text" class="form-control form-control-sm" name="notes" placeholder="Notes"/>
				</div>
				<div class="input-group input-group-sm ui-front">
					<input type="text" class="form-control form-control-sm" name="alt_matter_id" placeholder="Linked to"/>
					<div class="input-group-btn">
						<button type="button" class="btn btn-primary btn-sm" id="addEventSubmit">&check;</button>
						<button type="reset" class="btn btn-secondary btn-sm" onClick="$(this).parents('tr').html('')">&times;</button>
					</div>
				</div>
			</form>
		</td>
	</tr>
</template>
