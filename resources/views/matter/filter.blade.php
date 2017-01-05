<script type="text/javascript" >
$(document).ready(function(){
	$(".see-matter").click(function(event){
		var id_array = $(this).attr('data-mid').split('-');
		var rid = $(this).attr('data-rid');
		var url = '/matter/'+id_array[2]+'/rid/'+rid;
		window.open(url);
	});
	
	$(".see-tasks").click(function(event){
		var id_array = $(this).attr('data-mid').split('-');
		$('#tasklist-pop-up').load("/matter/tasklist/matter_id/"+id_array[2], function(){
			$(this).show();
		}).css('top', $(this).offset().top).draggable();
	});


	@if ($matters->display_style == 1)
		$('.display_actor, .display_status').hide();
		$('.display_status').show();
	@else
		$('.display_status, .display_actor').hide();
		$('.display_actor').show();
	@endif
});
</script>

	@foreach ($matters as $matter)
		@if ($matter->container_ID)
		<tr>
		@else
		<tr class="info"> 
		@endif
			<td class="see-matter" data-mid="edit-matter-{{ $matter->ID }}">{{ $matter->Ref }}</td>
			<td style="width: 12px;">{{ $matter->Cat }}</td>
			<td>{{ $matter->Status }}</td>
			<td class="display_actor">{{ $matter->Client }}</td>
			<td class="display_actor">{{ $matter->ClRef }}</td>
			<td class="display_actor">{{ $matter->Agent }}</td>
			<td class="display_actor" style="max-width: 20px; overflow-x: scroll;">{{ $matter->AgtRef }}</td>
			<td class="display_actor" style="font-size: 0.8em;">{{ $matter->Title }}</td>
			<td class="display_actor">{{ $matter->Inventor1 }}</td>
			<td class="display_status">{{ $matter->Status_date }}</td>
			<td class="display_status">{{ $matter->Filed }}</td>
			<td class="display_status">{{ $matter->FilNo }}</td>
			<td class="display_status">{{ $matter->Published }}</td>
			<td class="display_status">{{ $matter->PubNo }}</td>
			<td class="display_status">{{ $matter->Granted }}</td>
			<td class="display_status">{{ $matter->GrtNo }}</td>
		</tr>
	@endforeach
		<tr><td>&nbsp;</td></tr>
