<style type="text/css">
.status-row {
	display: block;
}

.tasklist-col-detail {
	display: inline-block;
	width: 220px;
	vertical-align: top;
}

.tasklist-col-due_date, .tasklist-col-done_date,
	.tasklist-col-assigned_to {
	display: inline-block;
	width: 80px;
	vertical-align: top;
}

.tasklist-col-done {
	display: inline-block;
	width: 38px;
	vertical-align: top;
	text-align: center;
}

.tasklist-col-cost, .tasklist-col-fee {
	display: inline-block;
	width: 50px;
	vertical-align: top;
}

.tasklist-col-currency {
	display: inline-block;
	width: 36px;
	vertical-align: top;
}

.tasklist-col-time_spent {
	display: inline-block;
	width: 60px;
	vertical-align: top;
}

.tasklist-col-notes {
	display: inline-block;
	width: 160px;
	vertical-align: top;
	overflow: hidden;
}

.tasklist-col {
	display: inline-block;
	width: 150px;
	vertical-align: top;
}

#MatterAllTasks:hover {
	cursor: pointer;
}

#MatterAllTasksBlock {
	margin-left: 30px;
	margin-top: 10px;
	width: 941px;
}

.notes:hover {
	cursor: help;
}

#add-task-popup {
	width: 500px;
	height: auto;
	top: 220px;
	display: none;
	position: fixed;
}

#add-task-wrap {
	background: whitesmoke;
	padding: 5px;
	border: 1px inset #ccc;
}

#add-task-wrap label {
	width: 75px;
	display: inline-block;
	vertical-align: middle;
	text-align: right;
}

#add-task-wrap input {
	margin-bottom: 5px;
}

.delete-task {
	display: none;
	cursor: pointer;
}

.add-task-toevent {
	cursor: pointer;
	width: 50px;
}

.event-actions {
	display: none;
}

.event-head {
	margin-top: 12px;
	height: 18px;
	border-bottom: 1px #DDD solid;
	font-weight: bold;
}

.event-head:hover .event-actions {
	display: inline-block;
	vertical-align: top;
}

.task-row:hover .delete-task {
	display: inline-block;
	vertical-align: top;
}

.task-row:hover {
	background: #EEF;
}
</style>

<script type="text/javascript">
$(document).ready(function(){
    $.editable.addInputType("datepicker", {
        element:  function(settings, original) {
            var input = $("<input type=\"text\" name=\"value\" style==\"width:10px\"/>");
            settings.onblur = function(e) {
            };
            $(this).append(input);
            return(input);
        },
        plugin:  function(settings, original) {
            var form = this;
            $("input", this).filter(":text").datepicker({
                dateFormat: 'dd/mm/yy',
                onSelect: function(dateText) { $(this).hide(); $(form).trigger("submit"); }
            });
        }
    });

    $.editable.addInputType('autocomplete', {
		element : $.editable.types.text.element,
 		plugin : function(settings, original){
			$('input', this).autocomplete(settings.autocomplete);
		}
    });

    $('.edit-assigned-to').editable('/matter/update-task', {
		type: 'autocomplete',
        autocomplete: {
			minLength : 1,
			source : "/actor/get-all-logins",
			select: function( event, ui ) {
				this.value = ui.item.value;
				$(event.target.form).submit();
			}
        },
        submitdata: function(value,settings){
			return { 
				task_id : $(this).parent().attr('id'), 
				field : 'assigned_to' 
			};
		},
		placeholder: '...',
		indicator : '<span class="icon-busy" />',
		tooltip: 'Click to edit...',
		select: true
    });

    $('#task_assigned_to').autocomplete({
        minLength: 1,
        source: "/actor/get-all-logins",
        select: function( event, ui ) {
            this.value = ui.item.value;
        }
    });

    $("#MatterAllTasks").click(function(event){
        $('#tasklist-pop-up').hide();
        location.reload(); // try to avoid because it reloads the whole page
    });

	$('.editable').editable('/matter/update-task', {
		submitdata: function(value, settings){
			var fclass = $(this).attr('class').split(" ");
			var field_name = (fclass[0]).split('-');
			return { 
				task_id : $(this).parent().attr('id'), 
				field : field_name[2]
			}
		},
		placeholder: '...',
		select : true,
		indicator : '<span class="icon-busy" />',
		// This is to diplay only the "detail" portion of the task in the edit field when clicking on a task name for edition                 
		data: function(value, settings){
			var fclass = $(this).attr('class').split(" ");
			var field_name = (fclass[0]).split('-');
			var name_detail = value.split(": ");
			if (field_name[2] == 'detail')
				return name_detail[1];
			else
				return value;
		}
	});
    
    $('.editable-due-date').editable('/matter/update-task', {
			submitdata: function(value,settings){
				var rule_id = $(this).attr('data-rule_id');
				if(rule_id) {
					var update_due = confirm("Changing the due date will unlink the task from date calculation rule");
					if(update_due)
						return { 
							task_id: $(this).attr('id'), 
							field: 'due_date', 
							rule_id: rule_id 
						}
				} else 
					return { 
						task_id: $(this).attr('id'), 
						field: 'due_date'
					}
            },
            type: 'datepicker',
            select: true,
            indicator: '<span class="icon-busy" />',
            placeholder: '...',
            tooltip: 'Click to edit...'
    });
    
    $('.editable-done-date').editable('/matter/update-task', {
		submitdata: function(value,settings){
			return { 
				task_id: $(this).attr('id'), 
				field: 'done_date'
			};
		},
		type: 'datepicker',
		select: true,
		indicator: '<span class="icon-busy" />',
		placeholder: '...',
		tooltip: 'Click to edit...'
    });

    $('.tasklist-col-done > input[type=checkbox]').click(function(){
       var tid = $(this).val();
       var done_val = 0;
       if($(this).is(":checked")){ 
          done_val = 1;
       }else{
          done_val = 0;
          var conf_done = confirm("Do you wish to set the task to undone?");
          if(!conf_done){
              $(this).attr("checked", true);
              return;
          }
       }
        $.post('/matter/update-task', { task_id: tid, field: 'done', value: done_val }, function(data){
        	var event_id = <?=(isset($this->event_id)?$this->event_id:0)?>;
        	if ( event_id )
        		$('#tasklist-pop-up').load("/matter/tasklist/event_id/<?=$this->event_id?>", {renewal: <?=$this->renewal ? 1 : 0?>});
        	else 
        		$('#tasklist-pop-up').load("/matter/tasklist/matter_id/<?=$this->matter_id?>");
        });
    });


    $('.add-task-toevent').click(function(){
        $('#trigger_id').val( $(this).attr('id') );
        $('#popup-label').val( $(this).attr('title') );
        $('#facade').show();
        $('#add-task-popup').show();
        $('#task_name').focus();
    });

    $('#add-task-cancel').click(function(){
        $('#add-task-popup').hide();
        $('#facade').hide();
    });

    $( "#task_name" ).autocomplete({
            minLength: 1,
            source: "/matter/get-all-tasks",
            focus: function( event, ui ) {
                    return false;
            },
            select: function( event, ui ) {
                    $( "#task_name" ).val( ui.item.value );
                    $( "#task_id" ).val( ui.item.id );
                    var valid=true;
                    if ( !ui.item ) {
                      var matcher = $.ui.autocomplete.escapeRegex( $(this).val() );
                      valid = false;
                      ui.item.each(function(value) {
                        if ( value.match( matcher ) ) {
                          valid = true;
                          return true;
                        }
                      });
                    }
                   if ( !valid ) {
                     // remove invalid value, as it didn't match anything
                      $( this ).val( "" );
                      $(this).data( "autocomplete" ).term = "";
                      return false;
                   }
                    return true;
        }
    });

    $('#task_duedate').datepicker({
        dateFormat: 'dd/mm/yy',
    });

    $('#add-task-submit').click(function(){
       if($('#task_id').val() == ''){
           alert('Select task name!');
           return;
       }
       if($('#task_duedate').val() == ''){
           alert('Due date required!');
           return;
       }
		$.post('/matter/add-event-task', { 
       		trigger_ID: $('#trigger_id').val(),
			code: $('#task_id').val(),
			due_date: $('#task_duedate').val(),
			assigned_to: $('#task_assignedto').val(),
			detail: $('#task_detail').val(),
			time_spent: $('#task_time').val(),
			notes: $('#task_notes').val(),
			cost: $('#task_cost').val(),
			fee: $('#task_fee').val(),
			currency: $('#task_currency').val()
		},
		function(data){
			if(!data.match(/SQLSTATE/)){
				$('#tasklist-pop-up').load("/matter/tasklist/matter_id/<?=$this->matter_id?>");
				$('#add-task-popup').css('display', 'none');
				$('#facade').hide();
			} else {
				alert(data);
			}
		});
    });

    $('.delete-task').click(function(){
        if( confirm("Do you want to delete task?") ){
			$.post('/matter/delete-task', { tid: $(this).parent().attr('id') }, function(data){
				if(!isNaN(parseInt(data))){
					$('#tasklist-pop-up').load("/matter/tasklist/matter_id/<?=$this->matter_id?>");
				}else{
					alert(data);
				}
			});
		} 
    });

    $('.delete-event').click(function(){
        var conf_event_del = confirm("Deleting the event will also delete the linked tasks");
        if(conf_event_del){
			$.post('/matter/delete-event', 
				{ eid: $(this).attr('id') },
				function(data){
					if(!isNaN(parseInt(data))){
						$('#tasklist-pop-up').load("/matter/tasklist/matter_id/<?=$this->matter_id?>");
					} else {
						alert(data);
					}
				}
			); 
		}
	});
    $("button, input:button").button();
});

</script>

<div id="MatterAllTasksBlock" class="listrow">
	<div id="MatterAllTasks" class="status-row">
		<span class="tasklist-col-detail" style="margin-left: 5px;">
			<?=$this->renewal ? "All renewals" : "All tasks" ?>
		</span>
		<span class="tasklist-col-due_date">Due date</span>
		<span class="tasklist-col-done">Done</span> 
		<span class="tasklist-col-done_date">Done date</span> 
		<span class="tasklist-col-cost">Cost</span> 
		<span class="tasklist-col-fee">Fee</span>
		<span class="tasklist-col-currency">Cur.</span>
<?php if (!$this->renewal) {?> 
		<span class="tasklist-col-time_spent">Time</span>
<?php } ?>
		<span class="tasklist-col-assigned_to">Assigned To</span> 
		<span class="tasklist-col-notes">Notes</span> 
		<span class="ui-icon ui-icon-arrowreturnthick-1-s" style="float: right;"></span>
	</div>
	<div style="max-height: 350px; min-height: 100px; height: auto; width: 100%; background: whitesmoke; overflow: auto">
<?php
$event_id = '';
foreach ( $this->matter_event_tasks as $event_task ) :
	// Display the trigger event in the first iteration
	if ($event_id != $event_task ['event_ID']):			
		$event_id = $event_task ['event_ID'];
?>
		<div class="event-head">
			<span>
				<?=$event_task['event_name'] . ": " . $event_task['event_date'] ?>
			</span>
			<span class="event-actions add-task-toevent" id="<?=$event_id?>" title="Add task to <?=$event_task['event_name']?>">
				<span class="ui-icon ui-icon-plusthick"></span>
			</span>
			<span class="event-actions delete-event" style="cursor: pointer;" id="<?=$event_id?>" title="Delete event">
				<span class="ui-icon ui-icon-trash"></span>
			</span>
		</div>
<?php
	endif;
	if ($event_id == $event_task ['trigger_ID']): ?>
		<div class="status-row task-row" id="<?=$event_task['ID']?>">
			<span class="tasklist-col-detail editable" title="Click to edit detail">
				<?=$event_task['task_name']?>
				<?=$event_task ['detail'] != "" ? ": " . $event_task ['detail'] : ''?>
			</span> 
			<span id="<?=$event_task['ID']?>" class="tasklist-col-due_date editable-due-date" data-rule_id="<?=$event_task['rule_used']?>">
				<?=$event_task['due_date']?>
			</span>
			<span class="tasklist-col-done">
				<input type="checkbox" name="doneflag-<?=$event_task['ID']?>" value="<?=$event_task['ID']?>"
				<?=$event_task['done'] ? 'checked' : '' ?>>
			</span> 
			<span id="<?=$event_task['ID']?>" class="tasklist-col-done_date editable-done-date">
				<?=$event_task['done_date']?>
			</span>
			<span class="tasklist-col-cost editable" title="Click to edit cost">
				<?=$event_task['cost']?>
			</span>
			<span class="tasklist-col-fee editable" title="Click to edit fee">
				<?=$event_task['fee']?>
			</span>
			<span class="tasklist-col-currency editable" title="Click to edit">
				<?=$event_task['currency']?>
			</span>
			<?php if (!$this->renewal): ?>
			<span class="tasklist-col-time_spent editable" title="Click to edit (HH:MM:SS)">
				<?=$event_task['time_spent']?>
			</span>
			<?php endif; ?>
			<span class="tasklist-col-assigned_to edit-assigned-to" title="Click to edit">
				<?=$event_task['assigned_to']?>
			</span>
			<span class="tasklist-col-notes editable" title="Click to edit notes">
				<?=$event_task['task_notes']?>
			</span>
			<span class="delete-task" title="Delete task">
				<span class="ui-icon ui-icon-trash" style="float: right;"></span>
			</span>
		</div>
<?php 
	endif;
endforeach; ?>
	</div>
</div>
<div id="add-task-popup" class="listrow">
	<input type="text" id="popup-label" value=""
		style="font-weight: bold; background-color: transparent; border: 0px; margin-bottom: 2px" />
	<div id="add-task-wrap">
		<input type="hidden" name="trigger_id" value="" id="trigger_id" />
		<label for="task_name"><b>Task Name</b></label>
		<input type="text" name="task_name" id="task_name" value="" style="width: 120px;" />
		<input type="hidden" name="task_id" id="task_id" value="" />
		<label for="task_duedate"><b>Due date</b></label>
		<input type="text" name="task_duedate" id="task_duedate" value="" style="width: 120px;" />
		<br>
		<label for="task_detail">Detail </label>
		<input type="text" name="task_detail" id="task_detail" value="" style="width: 120px;" />
		<label for="task_cost">Cost </label>
		<input type="text" name="task_cost" id="task_cost" value="" style="width: 120px;" />
		<br>
		<label for="task_fee">Fee </label>
		<input type="text" name="task_fee" id="task_fee" value="" style="width: 120px;" />
		<label for="task_currency">Currency </label>
		<input type="text" name="task_currency" id="task_currency" value="" style="width: 120px;" />
		<br>
		<label for="task_time">Time spent</label>
		<input type="text" name="task_time" id="task_time" value="" style="width: 120px;" />
		<label for="task_assigned_to">Assigned to </label>
		<input type="text" name="task_assigned_to" id="task_assigned_to" value="" style="width: 120px;" />
		<br> 
		<label for="task_notes">Notes</label>
		<textarea name="task_notes" id="task_notes" rows=2 cols=45></textarea>
		<br>
		<button name="add_task_cancel" id="add-task-cancel" style="float: right;">Cancel</button>
		<button name="add_task_submit" id="add-task-submit" style="float: right;">Add task</button>
	</div>
</div>
