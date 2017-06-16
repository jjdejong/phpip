<div id="listModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
	    <!-- Modal content-->
	    <div class="modal-content">
		    <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Title placeholder</h4>
			</div>
			<div class="modal-body">
				Ajax placeholder
			</div>
			<div class="modal-footer">
				<span class="alert pull-left"></span>
				<mark>Values are editable. Click on a value to change it and press <kbd>&crarr;</kbd> to save changes</mark>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
	    </div>
	</div>
</div>

<div id="classifiersModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
	    <!-- Modal content-->
	    <div class="modal-content">
		    <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4>Classifier Detail</h4>
			</div>
			<div class="modal-body">
				<table class="table table-condensed">
					<thead>
						<tr>
							<th>Type/Value</th>
							<th>URL</th>
							<th>Link to matter</th>
							<th>
								<a href="#addClassifierForm" data-toggle="collapse">
									<span class="glyphicon glyphicon-plus-sign pull-right" title="Add classifier"></span>
								</a>
							</th>
						</tr>
					</thead>
					@foreach ($classifiers as $type => $classifier_group)
						<tbody>
						<tr>
							<td colspan="4" class="text-warning">
								{{ $type }}
							</td>
						</tr>
						</tbody>
						<tbody class="sortable">
						@foreach($classifier_group as $classifier)
							<tr class="reveal-hidden" data-classifier_id="{{ $classifier->id }}">
								<td><input type="text" class="form-control noformat" name="value" value="{{ $classifier->value }}"/></td>
								<td><input type="text" class="form-control noformat" name="url" value="{{ $classifier->url }}"/></td>
								<td class="ui-front"><input type="text" class="form-control noformat" name="lnk_matter_id" value="{{ $classifier->lnk_matter_id ? $classifier->linkedMatter->uid : '' }}"></td>
								<td>
									<input type="hidden" name="display_order" value="{{ $classifier->display_order }}"/>
									<a href="#" class="hidden-action" id="deleteClassifier" data-id="{{ $classifier->id }}" title="Delete classifier">
										<span class="glyphicon glyphicon-trash text-danger"></span>
									</a>
								</td>
							</tr>
						@endforeach
						</tbody>
					@endforeach
					<tbody>
						<tr id="addClassifierForm" class="collapse">
							<td colspan="5">
								<form class="form-inline">
									{{ csrf_field() }}
									<input type="hidden" name="matter_id" value="{{ $matter->container_id or $matter->id }}"/>
									<input type="hidden" name="type_code" value=""/>
									<div class="form-group form-group-sm ui-front">
										<input type="text" class="form-control" size="16" name="type" placeholder="Type"/>
									</div>
									<div class="form-group form-group-sm">
										<input type="text" class="form-control" size="10" name="value" placeholder="Value"/>
									</div>
									<div class="form-group form-group-sm">
										<input type="url" class="form-control" size="16" name="url" placeholder="URL"/>
									</div>
									<div class="input-group input-group-sm ui-front">
										<input type="text" class="form-control" size="16" name="lnk_matter_id" placeholder="Linked to"/>
										<div class="input-group-btn">
											<button type="button" class="btn btn-primary" id="addClassifierSubmit"><span class="glyphicon glyphicon-ok"></span></button>
											<button type="button" class="btn btn-default" onClick="$('#addClassifierForm').collapse('hide')"><span class="glyphicon glyphicon-remove"></span></button>
										</div>
									</div>
								</form>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<span class="alert pull-left"></span>
				<mark>Values are editable. Click on a value to change it and press <kbd>&crarr;</kbd> to save changes</mark>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
	    </div>
	</div>
</div>

