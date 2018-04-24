<table class="table table-hover table-sm">
	<thead class="thead-light">
		<tr>
			<th class="border-top-0">Event</th>
			<th class="border-top-0">Date</th>
			<th class="border-top-0">Number</th>
			<th class="border-top-0">Notes</th>
			<th class="border-top-0">
				Refers to
				<a href="javascript:void(0);" class="badge badge-info float-right" id="addEvent" title="Add event">
					&plus;
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
				@csrf
				<input type="hidden" name="matter_id" value="{{ $matter->id }}"/>
				<input type="hidden" name="code" value=""/>
				<div class="input-group">
					<div class="ui-front">
						<input type="text" class="form-control form-control-sm" name="name" placeholder="Name"/>
						<input type="text" class="form-control form-control-sm" name="event_date" placeholder="Date"/>
					</div>
					<input type="text" class="form-control form-control-sm" name="detail" placeholder="Detail"/>
					<input type="text" class="form-control form-control-sm" name="notes" placeholder="Notes"/>
					<div class="ui-front">
						<input type="text" class="form-control form-control-sm" name="alt_matter_id" placeholder="Linked to"/>
					</div>
					<div class="input-group-append">
						<button type="button" class="btn btn-primary btn-sm" id="addEventSubmit">&check;</button>
						<button type="reset" class="btn btn-outline-primary btn-sm" onClick="$(this).parents('tr').html('')">&times;</button>
					</div>
				</div>
			</form>
		</td>
	</tr>
</template>
