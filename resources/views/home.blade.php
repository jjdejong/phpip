@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">New matter</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>
        </div>
        <div class="col-md-8" id="filter">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-3">
                            <b>Open tasks</b>
                        </div>
                        <div class="col-md-3">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-info active">
                                    <input type="radio" name="my_tasks" id="alltasks" value="0" />All
                                </label>
                                <label class="btn btn-info">
                                    <input type="radio" name="my_tasks" id="mytasks" value="1" />Mine
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button>Clear selected on : </button>
                        </div>
                        <div class="col-md-3" id="tasklistdate">
                            <input class="form-control form-control-sm noformat" size="10" name="datetaskcleardate" id="taskcleardate" type="text">
                        </div>
                    </div>
                </div>
                <div class="card-body"  id="tasklist">
                    <div class="row">
                        <div class="col-md-6">
                            Tasks
                        </div>
                        <div class="col-md-3">
                            Matter
                        </div>
                        <div class="col-md-2">
                            Due date
                        </div>
                        <div class="col-md-1">
                            Close
                        </div>
                    </div>

                @foreach ($tasks as $task)
                    <div class="row">
                        <div class="col-md-6"><a href="/matter/{{ $task->trigger->matter_id }}/tasks" class="hidden-action" data-toggle="modal" data-target="#homeModal" data-remote="false" title="All tasks" data-resource="/home/" data-source="/home?">
								                {{ $task->info->name }} {{ $task->detail ? "- ".$task->detail : "" }}</a></div>
                      <div  class="col-md-3"><a href="/matter/{{ $task->trigger->matter_id }}" >{{ empty($task->trigger) ? '' : $task->trigger->matter->uid }}</a></div>
                      
                        @if ($task->due_date < date('Y-m-d'))
                            <div class="col-md-2 text-danger">{{ $task->due_date}}</div>
                        @elseif ($task->due_date < date('Y-m-d', strtotime("+1 week")))
                            <div  class="col-md-2 text-warning">{{ $task->due_date}}</div>
                        @else
                            <div  class="col-md-2">{{ $task->due_date}}</div>
                        @endif
                      <div  class="col-md-1"><input id="{{ $task->id }}" type="checkbox" /></div>
                    </div>
                @endforeach
                </div>

            </div>
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-3">
                            <b>Open renewals</b>
                        </div>
                        <div class="col-md-3">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-info active">
                                    <input type="radio" name="my_renewals" id="allrenewals" value="0" />All
                                </label>
                                <label class="btn btn-info">
                                    <input type="radio" name="my_renewals" id="myrenewals" value="1" />Mine
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button>Clear selected on : </button>
                        </div>
                        <div class="col-md-3">
                            <input class="form-control form-control-sm hasDatepicker" size="10" name="renewalcleardate" id="taskcleardate" type="text">
                        </div>
                    </div>
                </div>

                <div class="card-body" id="renewallist">
                    <div class="row">
                        <div class="col-md-6">
                            Renewals
                        </div>
                        <div class="col-md-3">
                            Matter
                        </div>
                        <div class="col-md-2">
                            Due date
                        </div>
                        <div class="col-md-1">
                            Close
                        </div>
                    </div>
                    @if (count($renewals) == 0)
                        <div class="row text-danger">The list is empty</div>
                    @else
                        @foreach ($renewals as $task)
                            <div class="row">
                                <div class="col-md-6"><a href="/matter/{{ $task->trigger->matter_id }}/renewals" class="hidden-action" data-toggle="modal" data-target="#homeModal" data-remote="false" title="All tasks" data-resource="/home/" data-source="/home?">
								                {{ $task->info->name }} {{ $task->detail ? "- ".$task->detail : "" }}</a></div>
                              <div  class="col-md-3"><a href="/matter/{{ $task->trigger->matter_id }}" >{{ empty($task->trigger) ? '' : $task->trigger->matter->uid }}</a></div>
                              
                                @if ($task->due_date < date('Y-m-d'))
                                    <div class="col-md-2 text-danger">{{ $task->due_date}}</div>
                                @elseif ($task->due_date < date('Y-m-d', strtotime("+1 week")))
                                    <div  class="col-md-2 text-warning">{{ $task->due_date}}</div>
                                @else
                                    <div  class="col-md-2">{{ $task->due_date}}</div>
                                @endif
                              <div  class="col-md-1"><input id="{{ $task->id }}" type="checkbox" /></div>
                            </div>
                        @endforeach
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@include('partials.home-modals')
@section('script')

@include('home-js')

@stop

