@extends('layouts.app')

@section('content')

    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    
                    <div class="row">
                        <div class="col-md-8">
                            Categories
                        </div>
                        <div class="col-md-4">
                            <a href="#newMatterModal" data-toggle="modal">New matter</a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <b>Category</b>
                        </div>
                        <div class="col-md-3">
                            <b>Count</b>
                        </div>
                        <div class="col-md-1">
                        </div>
                    </div>
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    @foreach ($categories as $group)
                        <div class="row">
                            <div class="col-md-8">
                                {{$group->category }}
                            </div>
                            <div class="col-md-3">
                                {{ $group->total }}
                            </div>
                            <div class="col-md-1">
                                <a href="#newMatterModal" data-toggle="modal"><b>+</b></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    
                    <div class="row">
                        <div class="col-md-12">
                            Users tasks
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <b>User</b>
                        </div>
                        <div class="col-md-3">
                            <b>Open tasks</b>
                        </div>
                        <div class="col-md-3">
                            <b>Most urgent</b>                        
                        </div>
                    </div>
                    @foreach ($taskscount as $group)
                        @if ($group->no_of_tasks > 0)
                        <div class="row">
                            <div class="col-md-6">
                                {{$group->login }}
                            </div>
                            <div class="col-md-3">
                                {{ $group->no_of_tasks }}
                            </div>
                                @if ($group->posix_urgent_date < date('Y-m-d'))
                                    <div class="col-md-3 text-danger">{{ $group->urgent_date }}</div>
                                @elseif ($group->posix_urgent_date < date('Y-m-d', strtotime("+1 week")))
                                    <div  class="col-md-3 text-warning">{{ $group->urgent_date }}</div>
                                @else
                                    <div  class="col-md-3">{{ $group->urgent_date }}</div>
                                @endif
                        </div>
                        @endif
                    @endforeach
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
                        <div class="col-md-4">
                            <button class="btn btn-primary float-right" id="clear-open-tasks">Clear selected on: </button>
                        </div>
                        <div class="col-md-2" id="tasklistdate">
                            <input class="form-control" size="10" name="datetaskcleardate" id="taskcleardate" type="text">
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
                @if (is_array($tasks) )
                  @foreach ($tasks as $task)
                    <div class="row">
                        <div class="col-md-6">
                          <a href="/matter/{{ $task->trigger->matter->id }}/tasks" data-toggle="modal" data-target="#homeModal" data-remote="false" title="All tasks" data-resource="/home/" data-source="/home?">
                            {{ $task->info->name }}{{ $task->detail ? " - ".$task->detail : "" }}
                          </a>
                        </div>
                        <div  class="col-md-3">
                          <a href="/matter/{{ $task->trigger->matter->id }}" >
                            {{ $task->trigger->matter->uid }}
                          </a>
                        </div>
                        @if ($task->due_date < date('Y-m-d'))
                            <div class="col-md-2 text-danger">{{ $task->due_date}}</div>
                        @elseif ($task->due_date < date('Y-m-d', strtotime("+1 week")))
                            <div  class="col-md-2 text-warning">{{ $task->due_date}}</div>
                        @else
                            <div  class="col-md-2">{{ $task->due_date}}</div>
                        @endif
                        <div  class="col-md-1"><input id="{{ $task->id }}" class="clear-open-task" type="checkbox" /></div>
                    </div>
                  @endforeach
                @else
                    <div class="row text-danger">The list is empty</div>
                @endif
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
                        <div class="col-md-4">
                            <button class="btn btn-primary float-right" id="clear-ren-tasks">Clear selected on: </button>
                        </div>
                        <div class="col-md-2" id="renewallistdate">
                            <input class="form-control" size="10" name="renewalcleardate" id="renewalcleardate" type="text">
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
                    @if (is_array($renewals) )
                      @foreach ($renewals as $task)
                        <div class="row">
                          <div class="col-md-6">
                            <a href="/matter/{{ $task->trigger->matter->id }}/renewals" data-toggle="modal" data-target="#homeModal" data-remote="false" title="All tasks" data-resource="/home/" data-source="/home?">
                              {{ $task->detail }}
                            </a>
                          </div>
                          <div  class="col-md-3">
                            <a href="/matter/{{ $task->trigger->matter->id }}" >
                              {{ $task->trigger->matter->uid }}
                            </a>
                          </div>
                          @if ($task->due_date < date('Y-m-d'))
                              <div class="col-md-2 text-danger">{{ $task->due_date}}</div>
                          @elseif ($task->due_date < date('Y-m-d', strtotime("+1 week")))
                              <div  class="col-md-2 text-warning">{{ $task->due_date}}</div>
                          @else
                              <div  class="col-md-2">{{ $task->due_date}}</div>
                          @endif
                          <div  class="col-md-1"><input id="{{ $task->id }}" class="clear-ren-task" type="checkbox" /></div>
                        </div>
                      @endforeach
                    @else
                        <div class="row text-danger">The list is empty</div>
                    @endif
                    </div>
                </div>
            </div>
        </div>



<div id="homeModal" class="modal fade" role="dialog">
@include('partials.generic-modals')
</div>

@stop

@section('script')

@include('home-js')

@stop
