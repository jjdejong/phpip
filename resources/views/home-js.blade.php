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
		onSelect: function(date, instance) {
      $.ajax({
        url: resource + $(this).closest("tr").data("id"),
        type: 'PUT',
        data: $(this).serialize(),
      }).done(function () {
				$("#listModal").find(".modal-body").load(relatedUrl);
				$("#listModal").find(".alert").removeClass("alert-danger").html("");
			});
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

</script>
