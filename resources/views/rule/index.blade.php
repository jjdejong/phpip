@extends('layouts.app')

@section('content')
<legend class="alert alert-dark d-flex justify-content-between py-2 mb-1">
  <span>
    {{ __('Rules') }}
    <a class="text-primary" href="https://github.com/jjdejong/phpip/wiki/Tables#task_rules" target="_blank">
      <svg width="16" height="16" fill="currentColor"><use xlink:href="#question-circle-fill"/></svg>
    </a>
  </span>
  <a href="rule/create" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajaxModal" title="{{ __('Rule data') }}" data-source="/rule" data-resource="/rule/create/">{{ __('Create Rule') }}</a>
</legend>
<div class="row">
  <div class="col">
    <div class="card border-primary p-1">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr id="filter" class="table-primary align-middle">
            <th><input class="form-control form-control-sm" data-source="/rule" name="Task" placeholder="{{ __('Task') }}"></th>
            <th><input class="form-control form-control-sm" data-source="/rule" name="Detail" placeholder="{{ __('Detail') }}"></th>
            <th><input class="form-control form-control-sm" data-source="/rule" name="Trigger" placeholder="{{ __('Trigger event') }}" /></th>
            <th><input class="form-control form-control-sm" data-source="/rule" name="Category" placeholder="{{ __('Category') }}"></th>
            <th><input class="form-control form-control-sm" data-source="/rule" name="Country" placeholder="{{ __('Country') }}"></th>
            <th><input class="form-control form-control-sm" data-source="/rule" name="Origin" placeholder="{{ __('Origin') }}"></th>
            <th><input class="form-control form-control-sm" data-source="/rule" name="Type" placeholder="{{ __('Type') }}"></th>
          </tr>
        </thead>
        <tbody id="tableList">
          @foreach ($ruleslist as $rule)
          <tr data-id="{{ $rule->id }}" class="reveal-hidden">
            <td>
              <a href="/rule/{{ $rule->id }}" data-panel="ajaxPanel" title="{{ __('Rule data') }}">
                {{ $rule->taskInfo->name }}
              </a>
            </td>
            <td>{{ $rule->detail }}</td>
            <td>{{ empty($rule->trigger) ? '' : $rule->trigger->name }}</td>
            <td>{{ empty($rule->category) ? '' : $rule->category->category }}</td>
            <td>{{ empty($rule->country) ? '' : $rule->country->name }}</td>
            <td>{{ empty($rule->origin) ? '' : $rule->origin->name }}</td>
            <td>{{ empty($rule->type) ? '' : $rule->type->type }}</td>
          </tr>
          @endforeach
          <tr>
            <td colspan="5">
              {{ $ruleslist->links() }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-4">
    <div class="card border-info">
      <div class="card-header bg-info text-light">
        {{ __('Rule information') }}
      </div>
      <div class="card-body p-2" id="ajaxPanel">
        <div class="alert alert-info" role="alert">
          {{ __('Click on rule to view and edit details') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/tables.js') }}" defer></script>
@endsection
