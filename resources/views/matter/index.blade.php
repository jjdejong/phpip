@extends('layouts.app')

@section('script')
<script type="text/javascript">

     var sortKey = "{{ $matters->sort_id }}";
     var sortDir = "{{ $matters->sort_dir }}";

function getFilterUrl()
{
	var ref = $('#filter-ref').val().replace(/\//,"~~");
	var cat = $('#filter-cat').val();
	var stat = $('#filter-status').val();
	var stat_date = $('#filter-status_date').val();
	var client = $('#filter-client').val();
	var clref = $('#filter-clref').val().replace(/\//,"~~");
	var agent = $('#filter-agent').val();
	var agtref = $('#filter-agtref').val().replace(/\//,"~~");
	var title = $('#filter-title').val().replace(/\//,"~~");
	var inventor = $('#filter-inventor').val();
	var filed = $('#filter-filed').val();
	var filno = $('#filter-filno').val().replace(/\//,"~~");
	var published = $('#filter-published').val();
	var pubno = $('#filter-pubno').val().replace(/\//,"~~");
	var granted = $('#filter-granted').val();
	var grtno = $('#filter-grtno').val().replace(/\//,"~~");
	var display_style = $('input:radio[name=displaystyle]:checked').val();
	var responsible = $('input[name=responsible-filter]:checked').val();
	var url = '?';
	if($('input[name=container-filter]:checked').val() == 1)
		url = url + 'Ctnr=1&';
	if(ref != '')
		url = url + 'Ref=' + ref + '&';
	if(cat != '')
		url = url + 'Cat=' + cat + '&';
	if(stat_date != '')
		url = url + 'Status_date=' + stat_date + '&';
	if(stat != '')
		url = url + 'Status=' + stat + '&';
	if(client != '')
		url = url + 'Client=' + client + '&';
	if(clref != '')
		url = url + 'ClRef=' + clref + '&';
	if(agent != '')
		url = url + 'Agent=' + agent + '&';
	if(agtref != '')
		url = url + 'AgtRef=' + agtref + '&';
	if(title != '')
		url = url + 'Title=%25' + title + '&';
	if(inventor != '')
		url = url + 'Inventor1=' + inventor + '&';
	if(filed != '')
		url = url + 'Filed=' + filed + '&';
	if(filno != '')
		url = url + 'FilNo=' + filno + '&';
	if(published != '')
		url = url + 'Published=' + published + '&';
	if(pubno != '')
		url = url + 'PubNo=' + pubno + '&';
	if(granted != '')
		url = url + 'Granted=' + granted + '&';
	if(grtno != '')
		url = url + 'GrtNo=' + grtno + '&';       
	if(responsible == 1)
		url = url + 'responsible=' + '<?=$matters->responsible?>&';
	url = url + 'sort=' + sortKey + '&dir=' + sortDir + '&';
	<?php if($matters->category_display):?>
		url = url + 'display=<?=$matters->category_display?>&';
	<?php endif;?>
		url = url + 'display_style=' + display_style;
	return url;
}

function matterListJS () {
	@if ($matters->display_style == 1)
		$('.display_actor, .display_status').hide();
		$('.display_status').show();
	@else
		$('.display_status, .display_actor').hide();
		$('.display_actor').show();
	@endif

	$(".see-matter").click(function(event){
		var id_array = $(this).attr('data-mid').split('-');
		var url = '/matter/' + id_array[2];
		window.open(url);
	});
};

$(document).ready(function(){

	matterListJS ()
	
     if(sortDir == 'asc'){
         $('#'+sortKey+'-asc').attr('id', sortKey+"-desc");
         $('#'+sortKey+'-sort').css('background-position', '0px 0px');
     }else{
         $('#'+sortKey+'-sort').css('background-position', '-10px 0px');
     }
     $('.sorting-img').css('display', 'none');
     $('#'+sortKey+'-sort').css('display', 'inline-block');

     if(sortKey == ""){
         $('#caseref-sort').css('display', 'inline-block');
     }
     
     $(".see-tasks").click(function(event){
     	var id_array = $(this).attr('data-mid').split('-');
        $('#tasklist-pop-up').load("/matter/tasklist/matter_id/"+id_array[2], function(){
           	$(this).show();
        }).css('top', $(this).offset().top).draggable();
     });

     $('.sorting-dirs').click(function(){
        var sort_id = $(this).attr('id').split('-');
        sortKey = sort_id[0];
        sortDir = sort_id[1];
        var url = '/matter' + getFilterUrl();
        var objThis = $(this);
        var objSort = $('#'+sort_id[0]+'-sort');
        $.ajax({
             url: url,
             type: 'GET',
             data: { },
             success: function(data){
                 $('#matter-list').empty();
                 $('#matter-list').html(data);
                 if(sort_id[1] == 'asc'){
                      objThis.attr('id', sort_id[0]+"-desc");
                      objSort.css('background-position', "0px 0px");
                 }else{
                      objThis.attr('id', sort_id[0]+"-asc");
                      objSort.css('background-position', "-10px 0px");
                 }
                 $('.sorting-img').css('display', 'none');
                 objSort.css('display', 'inline-block');
                 window.history.pushState('', 'phpIP' , url);
             }
         });
     });


     $('#show-actor, #show-status').change(function(){
         if($('#actor-status input:radio:checked').val() == "1"){
             $('.display_actor').hide();
             $('.display_status').show();
             //Clear the filters exclusive to actor view
             $('#filter-agent').val('');
             $('#filter-agtref').val('');
             $('#filter-inventor').val('');
             $('#filter-title').val('');
         }else {
             $('.display_status').hide();
             $('.display_actor').show();
             //Clear the filters exclusive to status view
             $('#filter-status_date').val('');
             $('#filter-filno').val('');
             $('#filter-published').val('');
             $('#filter-pubno').val('');
             $('#filter-granted').val('');
             $('#filter-grtno').val('');
         }
         /*var url = '/matter' + getFilterUrl();                 
         $.ajax({
             url: url,
             type: 'GET',
             data: {},
             success: function(data){
                 $('#matter-list').empty();
                 $('#matter-list').html(data);
                 //window.history.pushState('', 'phpIP' , url);
             }
         });*/
     });


	$('#show-all, #show-containers, #show-responsible').change(function(){
		var url = '/matter' + getFilterUrl();
		/*$.ajax({
			url: url,
			type: 'GET',
			data: { },
			success: function(data){
				$('#matter-list').empty();
				$('#matter-list').html(data);
				//window.history.pushState('', 'phpIP' , url);
			}
		});*/
		//toggleMatterPanel();
		$('#matter-list').load(url + ' #matter-list > tr', function() {
			matterListJS ();
		});
		window.history.pushState('', 'phpIP' , url); 
	});
     
    $('#export').click(function(e){
		var url = '/matter/export' + getFilterUrl();
		e.preventDefault();  //stop the browser from following
    	window.location.href = url;
     });

     $('.filter-input').keyup(function(){
         if($(this).val().length != 0 && $(this).val().length < 3 && ($(this).attr("id") == "filter-ref" || $(this).attr("id") == "filter-title")){
             return false;
         }
		if($(this).val().length != 0)
			$(this).css("background-color", "bisque");
		else
			$(this).css("background-color", "white");
		var url = '/matter' + getFilterUrl();
         /*$.ajax({
             url: url,
             type: 'GET',
             data: { },
             success: function(data){
                 $('#matter-list').empty();
                 $('#matter-list').html(data).show();
                 //window.history.pushState('', 'phpIP' , url);
             }
         });*/
		$('#matter-list').load(url + ' #matter-list').show();
     });

	/*if($('.filter-input').val().length != 0)
		$('.filter-input').css("background-color", "bisque");
	else
		$('.filter-input').css("background-color", "white");*/
                  
     $('#clear-matter-filters').click(function(){
    	 window.location.href = '/matter';
     });
                  
     /*$( "button, input:submit, input:button").button();
     $( "#container-all" ).buttonset();
     $( "#actor-status" ).buttonset();
     $( "#mine-all" ).buttonset();*/

});
</script>
@stop

@section('content')

<style>
.see-matter {
	cursor: pointer;
}
</style>
<form class="btn-toolbar" role="toolbar">
	<div class="btn-group btn-group-sm" data-toggle="buttons" id="container-all">
		<label for="show-all" class="btn btn-primary {{ $matters->filters['Ctnr'] or null ? '' : 'active' }}">
			<input type="radio" id="show-all" name="container-filter" value="0">Show All 
		</label>
		<label for="show-containers" class="btn btn-primary {{ $matters->filters['Ctnr'] or null ? 'active' : '' }}"> 
			<input type="radio" id="show-containers" name="container-filter" value="1">Show Containers
		</label>
	</div>
	<div class="btn-group btn-group-sm" data-toggle="buttons" id="actor-status">
		<label for="show-actor" class="btn btn-primary  {{ $matters->display_style == 1 ? '' : 'active' }}">
			<input type="radio" id="show-actor" name="displaystyle" value="0">
			Actor View
		</label>
		<label for="show-status" class="btn btn-primary {{ $matters->display_style == 1 ? 'active' : '' }}"> 
			<input type="radio" id="show-status" name="displaystyle" value="1">
			Status View
		</label>
	</div>
	
	<div class="btn-group btn-group-sm" id="mine-all" data-toggle="buttons">
		<label for="show-responsible" class="btn btn-sm btn-primary {{ $matters->responsible ? 'active' : '' }}">
			<input class="responsible-filter" type="checkbox" id="show-responsible" name="responsible-filter" value="1"> 
			Show Mine
		</label>
	</div>
</form>
	
	<div class="btn-group btn-group-sm pull-right">
		<button id="export" name="export" class="btn btn-sm btn-default">
			<span class="glyphicon glyphicon-download-alt"></span> Export
		</button>
		<button id="clear-matter-filters" name="clear-filters" class="btn btn-sm btn-default">
			<span class="glyphicon glyphicon-refresh"></span> Clear filters
		</button>
	</div>

	<table class="table table-striped table-hover table-condensed">
	<thead>
		<tr>
			<th>Ref</th>
			<th>Cat</th>
			<th>Status</th>
			<th class="display_actor">Client</th>
			<th class="display_actor">ClRef</th>
			<th class="display_actor">Agent</th>
			<th class="display_actor">AgtRef</th>
			<th class="display_actor">Title</th>
			<th class="display_actor">Inventor</th>
			<th class="display_status">Date</th>
			<th class="display_status">Filed</th>
			<th class="display_status">Number</th>
			<th class="display_status">Published</th>
			<th class="display_status">Number</th>
			<th class="display_status">Granted</th>
			<th class="display_status">Number</th>
		</tr>
		<tr>
			<td><input id="filter-ref" class="filter-input form-control input-sm" name="Ref" value="{{ old ( 'Ref' ) }}" placeholder="Ref"></td>
			<td><input id="filter-cat" class="filter-input form-control input-sm" name="Cat" placeholder="Cat"></td>
			<td><input id="filter-status" class="filter-input form-control input-sm" name="Status" placeholder="Status"></td>
			<td class="display_actor"><input id="filter-client" class="filter-input form-control input-sm" name="Client" placeholder="Client"></td>
			<td class="display_actor"><input id="filter-clref" class="filter-input form-control input-sm" name="ClRef" placeholder="Cl. Ref"></td>
			<td class="display_actor"><input id="filter-agent" class="filter-input form-control input-sm" name="Agent" placeholder="Agent"></td>
			<td class="display_actor"><input id="filter-agtref" class="filter-input form-control input-sm" name="AgtRef" placeholder="Agt. Ref"></td>
			<td class="display_actor"><input id="filter-title" class="filter-input form-control input-sm" name="Title" placeholder="Title"></td>
			<td class="display_actor"><input id="filter-inventor" class="filter-input form-control input-sm" name="Inventor1" placeholder="Inventor"></td>
			<td class="display_status"><input id="filter-status_date" class="filter-input form-control input-sm" name="Status_date" placeholder="Date"></td>
			<td class="display_status"><input id="filter-filed" class="filter-input form-control input-sm" name="Filed" placeholder="Filed"></td>
			<td class="display_status"><input id="filter-filno" class="filter-input form-control input-sm" name="FilNo" placeholder="Number"></td>
			<td class="display_status"><input id="filter-published" class="filter-input form-control input-sm" name="Published" placeholder="Published"></td>
			<td class="display_status"><input id="filter-pubno" class="filter-input form-control input-sm" name="PubNo" placeholder="Number"></td>
			<td class="display_status"><input id="filter-granted" class="filter-input form-control input-sm" name="Granted" placeholder="Granted"></td>
			<td class="display_status"><input id="filter-grtno" class="filter-input form-control input-sm" name="GrtNo" placeholder="Number"></td>
		</tr>
	</thead>
	<tbody id="matter-list">
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
		<tr style="position: fixed; bottom: -20px;"><td class="pagination-sm">{{ $matters->links() }}</td></tr>
	</tbody>

	</table>
	
@stop