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
		<tr data-id="{{ $event->id }}">
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
