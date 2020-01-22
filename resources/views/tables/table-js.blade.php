<script>
    var contentSrc = ""; // Identifies what to display in the Ajax-filled modal. Updated according to the href attribute used for triggering the modal
    var sourceUrl = "";  // Identifies what to reload when refreshing the list
    var url = new URL(window.location.href);

    function refreshList() {
      window.history.pushState('', 'phpIP', url)
      reloadPart(url, 'tableList');
    }

    filter.addEventListener('input', debounce( e => {
      if (e.target.value.length === 0) {
        url.searchParams.delete(e.target.name);
      } else {
        url.searchParams.set(e.target.name, e.target.value);
      }
      refreshList();
    }, 300));


    $(document).ready(function () {

      // Reload the rules list when closing the modal window
      $("#ajaxModal").on("hidden.bs.modal", function (event) {
        refreshList();
      });

      // Display the modal view for creation of record
      $("#addModal").on("show.bs.modal", function (event) {
        contentSrc = $(event.relatedTarget).attr("href");
        sourceUrl = $(event.relatedTarget).data("source");   // Used to refresh the list
        resource = $(event.relatedTarget).data("resource");
        $(this).find(".modal-title").text($(event.relatedTarget).attr("title"));
        $(this).find(".modal-body").load(contentSrc);
      });
    });

</script>
