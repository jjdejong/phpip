@extends('layouts.app')

@section('titre')
    Rules edition
@endsection

@section('style')

<style>

.reveal-hidden:hover .hidden-action {
	display: inline-block;
}
.noformat {
    border: none;
    background: white;
    color: inherit;
    padding: 0px;
    height: inherit;
    display: inline;
    box-shadow: none;
}
</style>

@stop


@section('content')
<div id="rules-tab">Rules</div>
<a href="ruleadd"  data-toggle="modal" data-target="#infoModal" data-remote="false" title="Rule data" data-resource="/ruleadd/">Add a new rule</a>
<div id="rules-box" class="place-card">
<table class="table table-striped table-hover table-condensed">
	<thead>
    <tr id="filter">
    	<th><input class="filter-input form-control input-sm" name="Task" placeholder="Task" value="{{ old('Task') }}"></td>
        <th>Detail</th>
        <th><input class="filter-input form-control input-sm" value="{{ old('Trigger') }}" name="Trigger"  placeholder="Trigger event"/></th>
        <th>Category</th>
    	<th><input name="Country" class="filter-input form-control input-sm" name="Country" placeholder="Country" value="{{ old('Country') }}"/></th>
        <th>Origin</th>
        <th>Type</th>
    	<th>Delete</th>
    </tr>
    </thead>
<div id="rule-table-list">
<div class="phpip-list" id="rules-list">
<tbody id="rule-list">
	
@foreach ($ruleslist as $rule)
    <tr class="rule-list-row" id="{{ $rule->rule_id }}">
    	
    	<th class="col-task"><a href="/ruleinfo/{{ $rule->rule_id }}" class="lien hidden-action pull-right" data-toggle="modal" data-target="#infoModal" data-remote="false" title="Rule data" data-resource="/ruleinfo/">
								{{ $rule->task_name }}</a></th>
        <th class="col-notes">{{ $rule->detail }}</th>
        <th class="col-trigger">{{ $rule->trigger_event_name }}</th>
        <th class="col-category">{{ $rule->category_name }}</th>
    	<th class="col-country">{{ $rule->country_name }}</th>
        <th class="col-origin">{{ $rule->origin_name }}</th>
        <th class="col-type">{{ $rule->for_type_name }}</th>
    	<th class="col-delete" >
    		<span class="delete-from-list ui-icon ui-icon-trash" id="{{ $rule->rule_id }}" style="float:right;" title="Delete rule"></span>
    	</div>
    </tr>
@endforeach
</tbody>
</table>
</div>
</div>
</div>
<!-- Modals -->
@include('partials.table-show-modals')

@endsection 

@section('script')

@include('tables.table-js')

@stop

