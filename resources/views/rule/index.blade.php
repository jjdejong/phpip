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
<a href="rule/create"  data-toggle="modal" data-target="#addModal" data-remote="false" title="Rule data" data-source="/rule?" data-resource="/rule/create/">Add a new rule</a>
<div id="rules-box">
<table class="table table-striped table-hover table-sm">
	<thead>
    <tr id="filter">
    	<th><input class="filter-input form-control form-control-sm" name="Task" placeholder="Task" value="{{ old('Task') }}"></th>
      <th>Detail</th>
      <th><input class="filter-input form-control form-control-sm" value="{{ old('Trigger') }}" name="Trigger"  placeholder="Trigger event"/></th>
      <th>Category</th>
    	<th><input name="Country" class="filter-input form-control form-control-sm" name="Country" placeholder="Country" value="{{ old('Country') }}"/></th>
      <th>Origin</th>
      <th>Type</th>
    	<th>Delete</th>
    </tr>
  </thead>
<div id="rule-table-list">
<div class="phpip-list" id="rules-list">
  <tbody id="rule-list">

@foreach ($ruleslist as $rule)
    <tr class="rule-list-row" data-id="{{ $rule->id }}">
    	<td class="col-task"><a href="/rule/{{ $rule->id }}" class="hidden-action" data-toggle="modal" data-target="#infoModal" data-remote="false" title="Rule data" data-source="/rule?" data-resource="/rule/">
								{{ $rule->taskInfo->name }}</a></td>
      <td class="col-notes">{{ $rule->detail }}</td>
      <td class="col-trigger">{{ empty($rule->trigger) ? '' : $rule->trigger->name }}</td>
      <td class="col-category">{{ empty($rule->category) ? '' : $rule->category->name }}</td>
    	<td class="col-country">{{ empty($rule->country) ? '' : $rule->country->name }}</td>
      <td class="col-origin">{{ empty($rule->origin) ? '' : $rule->origin->name }}</td>
      <td class="col-type">{{ empty($rule->type) ? '' : $rule->type->name }}</td>
    	<td class="col-delete" >
    		<span class="delete-from-list float-right text-danger" data-id="{{ $rule->id }}" title="Delete rule">&ominus;</span>
    	</td>
    </tr>
@endforeach
  </tbody>
</table>
</div>
</div>
</div>

<!-- Modals -->
@include('partials.table-show-modals')
@include('partials.table-add-modals')

@endsection

@section('script')

@include('tables.table-js')

@stop
