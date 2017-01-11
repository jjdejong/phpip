@extends('layouts.app')

@section('script')
<script type="text/javascript">

     var sortKey = "{{ $matters->sort_id }}";
     var sortDir = "{{ $matters->sort_dir }}";

// This needs to be run the first time and every time the matter list is updated via Ajax
function contentUpdated() {
	// Select the data to display (actor-related or status-related)
	if ( $('#actor-status label.active input').val() == 1 ) {
		$('.display_actor').hide();
		$('.display_status').show();
	} else {
		$('.display_status').hide();
		$('.display_actor').show();
	}
	console.log($('#actor-status label.active input').val());
};

$(document).ready(function(){

	contentUpdated();
	
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
        $('#tasklist-pop-up').load('/matter/' + id_array[2] + '/task', function(){
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

  // Toggle the data to display
	$("label[for='show-status']").click(function(){
		$('.display_actor').hide();
		$('.display_status').show();
	});

	$("label[for='show-actor']").click(function(){
		$('.display_status').hide();
		$('.display_actor').show();
	});

	$('#show-all, #show-containers, #show-responsible').change(function(){
		var url = '/matter?' + $(".btn-toolbar, #filter").find("input").filter(function(){return $(this).val().length > 0}).serialize();
		$('tbody#matter-list').load(url + ' tbody#matter-list > tr', function() { // Refresh all the tr's in tbody
			contentUpdated();
			window.history.pushState('', 'phpIP' , url);
		}); 
	});
     
    $('#export').click(function(e){
		var url = '/matter/export?' + $(".btn-toolbar, #filter").find("input").filter(function(){return $(this).val().length > 0}).serialize();
		e.preventDefault();  //stop the browser from following
    	window.location.href = url;
     });

     $('.filter-input').keyup(function(){
         if($(this).val().length != 0 && $(this).val().length < 3 && ($(this).attr("name") == "Ref" || $(this).attr("name") == "Title")){
             return false;
         }
		if($(this).val().length != 0)
			$(this).css("background-color", "bisque");
		else
			$(this).css("background-color", "white");
		var url = '/matter?' + $(".btn-toolbar, #filter").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
		$('#matter-list').load(url + ' #matter-list > tr', function() { // Inject content under tbody
			contentUpdated();
			window.history.pushState('', 'phpIP' , url);
		});
     });
});
</script>
@stop

@section('content')

<div class="panel panel-default"><div class="panel-body">
<form class="btn-toolbar" role="toolbar">
	<div class="btn-group btn-group-sm" data-toggle="buttons" id="container-all">
		<label for="show-all" class="btn btn-primary active">
			<input type="radio" id="show-all" name="Ctnr" value="0">Show All 
		</label>
		<label for="show-containers" class="btn btn-primary"> 
			<input type="radio" id="show-containers" name="Ctnr" value="1">Show Containers
		</label>
	</div>
	<div class="btn-group btn-group-sm" data-toggle="buttons" id="actor-status">
		<label for="show-actor" class="btn btn-primary active">
			<input type="radio" id="show-actor" value="0">
			Actor View
		</label>
		<label for="show-status" class="btn btn-primary"> 
			<input type="radio" id="show-status" value="1">
			Status View
		</label>
	</div>
	
	<div class="btn-group btn-group-sm" id="mine-all" data-toggle="buttons">
		<label for="show-responsible" class="btn btn-sm btn-primary {{ $matters->responsible ? 'active' : '' }}">
			<input class="responsible-filter" type="checkbox" id="show-responsible" name="responsible" value="{{ Auth::user ()->login }}"> 
			Show Mine
		</label>
	</div>
	<input type="hidden" name="sort" value="{{ $matters->sort_id }}">
	<input type="hidden" name="dir" value="{{ $matters->sort_dir }}">
	
	<div class="btn-group btn-group-sm pull-right" style="display: inline-block;">
		<button id="export" name="export" class="btn btn-sm btn-default">
			<span class="glyphicon glyphicon-download-alt"></span> Export
		</button>
		<button id="clear-filters" name="clear-filters" class="btn btn-sm btn-default" onclick="$('#matter-list').load('/matter #matter-list > tr', function() {
				window.history.pushState('', 'phpIP' , '/matter');
			});">
			<span class="glyphicon glyphicon-refresh"></span> Clear filters
		</button>
	</div>
</form>
</div></div>

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
		<tr id="filter">
			<td><input id="filter-ref" class="filter-input form-control input-sm" name="Ref" placeholder="Ref" value="{{ old('Ref') }}"></td>
			<td><input id="filter-cat" class="filter-input form-control input-sm" name="Cat" placeholder="Cat" value="{{ old('Cat') }}"></td>
			<td><input id="filter-status" class="filter-input form-control input-sm" name="Status" placeholder="Status" value="{{ old('Status') }}"></td>
			<td class="display_actor"><input class="filter-input form-control input-sm" name="Client" placeholder="Client" value="{{ old('Client') }}"></td>
			<td class="display_actor"><input class="filter-input form-control input-sm" name="ClRef" placeholder="Cl. Ref" value="{{ old('ClRef') }}"></td>
			<td class="display_actor"><input class="filter-input form-control input-sm" name="Agent" placeholder="Agent" value="{{ old('Agent') }}"></td>
			<td class="display_actor"><input class="filter-input form-control input-sm" name="AgtRef" placeholder="Agt. Ref" value="{{ old('AgtRef') }}"></td>
			<td class="display_actor"><input class="filter-input form-control input-sm" name="Title" placeholder="Title" value="{{ old('Title') }}"></td>
			<td class="display_actor"><input class="filter-input form-control input-sm" name="Inventor1" placeholder="Inventor" value="{{ old('Inventor1') }}"></td>
			<td class="display_status"><input class="filter-input form-control input-sm" name="Status_date" placeholder="Date" value="{{ old('Status_date') }}"></td>
			<td class="display_status"><input class="filter-input form-control input-sm" name="Filed" placeholder="Filed" value="{{ old('Filed') }}"></td>
			<td class="display_status"><input class="filter-input form-control input-sm" name="FilNo" placeholder="Number" value="{{ old('FilNo') }}"></td>
			<td class="display_status"><input class="filter-input form-control input-sm" name="Published" placeholder="Published" value="{{ old('Published') }}"></td>
			<td class="display_status"><input class="filter-input form-control input-sm" name="PubNo" placeholder="Number" value="{{ old('PubNo') }}"></td>
			<td class="display_status"><input class="filter-input form-control input-sm" name="Granted" placeholder="Granted" value="{{ old('Granted') }}"></td>
			<td class="display_status"><input class="filter-input form-control input-sm" name="GrtNo" placeholder="Number" value="{{ old('GrtNo') }}"></td>
		</tr>
	</thead>
	<tbody id="matter-list">
	@foreach ($matters as $matter)
	<?php // Format the publication number for searching on Espacenet
		$published = 0;
		if ( $matter->PubNo || $matter->GrtNo) {
			$published = 1;
			if ( $matter->origin == 'EP' )
				$CC = 'EP';
			else
				$CC = $matter->country;
			$removethese = [ "/^$matter->country/", '/ /', '/,/', '/-/', '/\//' ]; 
			$pubno = preg_replace ( $removethese, '', $matter->PubNo );
			if ( $CC == 'US' ) {
				if ( $matter->GrtNo )
					$pubno = preg_replace ( $removethese, '', $matter->GrtNo );
				else
					$pubno = substr ( $pubno, 0, 4 ) . substr ( $pubno, - 6 );
			}
		}
	?>
		@if ( $matter->container_ID )
		<tr>
		@else
		<tr class="info"> 
		@endif
			<td {!! $matter->dead ? 'style="text-decoration: line-through"' : '' !!}><a href="/matter/{{ $matter->ID }}" target="_blank">{{ $matter->Ref }}</a></td>
			<td style="width: 12px;">{{ $matter->Cat }}</td>
			<td>
			@if ( $published )
				<a href="http://worldwide.espacenet.com/publicationDetails/biblio?DB=EPODOC&CC={{ $CC }}&NR={{ $pubno }}" target="_blank">{{ $matter->Status }}</a>
			@else
				{{ $matter->Status }}
			@endif
			</td>
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
		<tr style="position: fixed; bottom: -20px;">
			<td>
				<ul class="pagination pagination-sm">
					<li onclick="$('#matter-list').load('{!! $matters->previousPageUrl() !!}' + ' #matter-list > tr', function() {
						contentUpdated();
						window.history.pushState('', 'phpIP' , '{!! $matters->previousPageUrl() !!}');
					});"><a href=# rel="prev"><span class="glyphicon glyphicon-chevron-left"></span></a></li>
					<li onclick="$('#matter-list').load('{!! $matters->nextPageUrl() !!}' + ' #matter-list > tr', function() {
						contentUpdated();
						window.history.pushState('', 'phpIP' , '{!! $matters->nextPageUrl() !!}');
					});"><a href=# rel="next"><span class="glyphicon glyphicon-chevron-right"></span></a></li>
				</ul>
			</td>
		</tr>
	</tbody>

	</table>
	
@stop