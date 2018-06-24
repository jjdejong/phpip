<style type="text/css">
    .card-body {
        max-height: 150px;
        overflow: auto;
        }
    .card-body .row:hover {
        background-color: #dddddd;
        }
</style>
<script>

$('#tasklistdate').on("focus", 'input[name$="date"]', function() {
    $(this).datepicker({
	dateFormat: 'yy-mm-dd',
	showButtonPanel: true,
	onSelect: function(date) {
                        $(this).val(date);
	}
    });
});

$('#mytasks').on('change', function() {
    var url = '/home?' + $("#filter").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
    $('#tasklist').load(url + ' #tasklist > div', function() { // Refresh all the card-body's in #tasklist
	window.history.pushState('', 'phpIP' , url);
	});
});

$('#alltasks').on('change', function() {
    var url = '/home?' + $("#filter").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
    $('#tasklist').load(url + ' #tasklist > div', function() { // Refresh all the card-body in #tasklist
	window.history.pushState('', 'phpIP' , url);
	});
});

$('#allrenewals').on('change', function() {
    var url = '/home?' + $("#filter").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
    $('#renewallist').load(url + ' #renewallist > div', function() { // Refresh all the card-body in #renewallist
	window.history.pushState('', 'phpIP' , url);
	});
});


$('#myrenewals').on('change', function() {
    var url = '/home?' + $("#filter").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
    $('#renewallist').load(url + ' #renewallist > div', function() { // Refresh all the card-body in #renewallist
	window.history.pushState('', 'phpIP' , url);
	});
});

	// Ajax fill the opened modal and set global parameters
    $("#homeModal").on("show.bs.modal", function(event) {
    	relatedUrl = $(event.relatedTarget).attr("href");
    	resource = $(event.relatedTarget).data("resource");

    	$(this).find(".modal-title").text( $(event.relatedTarget).attr("title") );
        $(this).find(".modal-body").load(relatedUrl);
    });

</script>
