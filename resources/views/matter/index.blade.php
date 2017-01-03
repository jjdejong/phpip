@extends('layouts.app')

@section('content')
<script type="text/javascript">

     var sortKey = "<?= $matters->sort_id ?>";
     var sortDir = "<?= $matters->sort_dir ?>";

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

$(document).ready(function(){
    <?php if($matters->display_style):?>
		$('.display_actor,.display_status').css('display','none');
		$('.display_status').css('display','inline-block');
    <?php else:?>
		$('.display_actor,.display_status').css('display','none');
		$('.display_actor').css('display','inline-block');
    <?php endif;?>


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


     $(".see-matter").click(function(event){
        var id_array = $(this).attr('data-mid').split('-');
        var url = '/matter/' + id_array[2];
        window.open(url);
     });
     
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
        var url = '/matter/' + getFilterUrl();
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


     $('.display_style').click(function(){
         if($(this).val() == 1){
             $('.display_actor,.display_status').css('display','none');
             $('.display_status').css('display','inline-block');
             //Clear the filters exclusive to actor view
             $('#filter-agent').val('');
             $('#filter-agtref').val('');
             $('#filter-inventor').val('');
             $('#filter-filed').val('');
         }else {
             $('.display_actor,.display_status').css('display','none');
             $('.display_actor').css('display','inline-block');
             //Clear the filters exclusive to status view
             $('#filter-status_date').val('');
             $('#filter-filno').val('');
             $('#filter-published').val('');
             $('#filter-pubno').val('');
             $('#filter-granted').val('');
             $('#filter-grtno').val('');
         }
		var url = '/matter/filter' + getFilterUrl();                 
         $.ajax({
             url: url,
             type: 'GET',
             data: {},
             success: function(data){
                 $('#matter-list').empty();
                 $('#matter-list').html(data);
                 window.history.pushState('', 'phpIP' , url);
             }
         });
     });


     $('#show-all, #show-containers, #show-responsible').click(function(){
		var url = '/matter/filter' + getFilterUrl();
         $.ajax({
             url: url,
             type: 'GET',
             data: { },
             success: function(data){
                 $('#matter-list').empty();
                 $('#matter-list').html(data);
                 window.history.pushState('', 'phpIP' , url);
             }
         });
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
		var url = '/matter/filter' + getFilterUrl();
         $.ajax({
             url: url,
             type: 'GET',
             data: { },
             success: function(data){
                 $('#matter-list').empty();
                 $('#matter-list').html(data);
                 window.history.pushState('', 'phpIP' , url);
             }
         });
     });
                  
     $('#clear-matter-filters').click(function(){
         $(location).attr('href', '/matter');
     });
                  
     $( "button, input:submit, input:button").button();
     $( "#container-all" ).buttonset();
     $( "#actor-status" ).buttonset();
     $( "#mine-all" ).buttonset();

});
</script>

	<table class="table table-striped table-hover table-condensed">
		<tr>
			<th>Ref</th>
			<th>Cat</th>
			<th>Status</th>
			<th>Client</th>
			<th>ClRef</th>
			<th>Agent</th>
			<th>AgtRef</th>
			<th>Title</th>
			<th>Inventor1</th>
			<th>Filed</th>
		</tr>
		<tr>
			<td><input class="form-control input-sm" placeholder="Ref"></td>
			<td><input class="form-control input-sm" placeholder="Cat"></td>
			<td><input class="form-control input-sm" placeholder="Status"></td>
			<td><input class="form-control input-sm" placeholder="Client"></td>
			<td><input class="form-control input-sm" placeholder="Cl. Ref"></td>
			<td><input class="form-control input-sm" placeholder="Agent"></td>
			<td><input class="form-control input-sm" placeholder="Agt. Ref"></td>
			<td><input class="form-control input-sm" placeholder="Title"></td>
			<td><input class="form-control input-sm" placeholder="Inventor"></td>
			<td><input class="form-control input-sm" placeholder="Filed"></td>
		</tr>
	@foreach ($matters as $matter)
		@if ($matter->container_ID)
		<tr>
		@else
		<tr class="info"> 
		@endif
			<td class="see-matter" data-mid="edit-matter-{{ $matter->ID }}">{{ $matter->Ref }}</td>
			<td>{{ $matter->Cat }}</td>
			<td>{{ $matter->Status }}</td>
			<td>{{ $matter->Client }}</td>
			<td>{{ $matter->ClRef }}</td>
			<td>{{ $matter->Agent }}</td>
			<td>{{ $matter->AgtRef }}</td>
			<td style="font-size: 0.8em;">{{ $matter->Title }}</td>
			<td>{{ $matter->Inventor1 }}</td>
			<td>{{ $matter->Filed }}</td>
		</tr>
	@endforeach
		<tr><td>&nbsp;</td></tr>
	</table>
@stop
	
@section('footer')

	<div style="position: fixed; bottom: 0;">{{ $matters->links() }}</div>

@stop