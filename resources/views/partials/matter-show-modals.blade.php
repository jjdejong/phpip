<div id="classifiersModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4>Classifier Detail</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <table class="table table-sm">
          <thead class="thead-light">
            <tr>
              <th class="border-top-0">Type/Value</th>
              <th class="border-top-0">URL</th>
              <th class="border-top-0">Link to matter</th>
              <th class="border-top-0">
                <a href="#addClassifierRow" data-toggle="collapse">
                  <span class="float-right" title="Add classifier">&oplus;</span>
                </a>
              </th>
            </tr>
          </thead>
          @foreach ($classifiers as $type => $classifier_group)
          <tbody>
            <tr>
              <td colspan="4" class="font-weight-bold">
                {{ $type }}
              </td>
            </tr>
          </tbody>
          <tbody class="sortable">
            @foreach($classifier_group as $classifier)
            <tr class="reveal-hidden" data-classifier_id="{{ $classifier->id }}">
              <td><input type="text" class="form-control noformat" name="value" value="{{ $classifier->value }}" /></td>
              <td><input type="text" class="form-control noformat" name="url" value="{{ $classifier->url }}" /></td>
              <td class="ui-front"><input type="text" class="form-control noformat" name="lnk_matter_id" placeholder="{{ $classifier->lnk_matter_id ? $classifier->linkedMatter->uid : '' }}"></td>
              <td>
                <input type="hidden" name="display_order" value="{{ $classifier->display_order }}" />
                <a href="#" class="hidden-action" id="deleteClassifier" data-id="{{ $classifier->id }}" title="Delete classifier">
                  <span class="text-danger">&CircleMinus;</span>
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
          @endforeach
          <tbody>
            <tr id="addClassifierRow" class="collapse">
              <td colspan="5">
                <form id="addClassifierForm" class="form-inline">
                  <input type="hidden" name="matter_id" value="{{ $matter->container_id ?? $matter->id }}" />
                  <input type="hidden" name="type_code" value="" />
                  <div class="input-group">
                    <div class="ui-front">
                      <input type="text" class="form-control form-control-sm" size="16" name="type" placeholder="Type" />
                    </div>
                    <input type="text" class="form-control form-control-sm" size="10" name="value" placeholder="Value" />
                    <input type="url" class="form-control form-control-sm" size="16" name="url" placeholder="URL" />
                    <div class="ui-front">
                      <input type="hidden" name="lnk_matter_id" value="">
                      <input type="text" class="form-control form-control-sm" size="16" id="lnk_matter_id" placeholder="Linked to">
                    </div>
                    <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-sm" id="addClassifierSubmit">&check;</button>
                      <button type="reset" class="btn btn-outline-primary btn-sm" onClick="$('#addClassifierRow').collapse('hide')">&times;</button>
                    </div>
                  </div>
                </form>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <span class="alert float-left"></span>
        <mark>Values are editable. Click on a value to change it and press <kbd>&crarr;</kbd> to save changes</mark>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
