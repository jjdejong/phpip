@extends('layouts.app')

@section('content')

    <div class="row card-deck">
        <div class="col-4">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            Categories
                        </div>
                        <div class="col-4">
                            <a href="#newMatterModal" data-toggle="modal">New matter</a>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-1">
                    <div class="row sticky-top bg-light font-weight-bold">
                        <div class="col-8">
                            Category
                        </div>
                        <div class="col-3">
                            Count
                        </div>
                        <div class="col-1">
                        </div>
                    </div>
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    @foreach ($categories as $group)
                        <div class="row reveal-hidden">
                            <div class="col-8">
                                {{$group->category }}
                            </div>
                            <div class="col-3">
                                {{ $group->total }}
                            </div>
                            <div class="col-1">
                                <a class="badge badge-primary hidden-action"
                                    href="/matter/create?operation=new&category={{$group->category_code}}"
                                    data-target="#homeModal"
                                    data-remote="false"
                                    title="Create new {{ $group->category }}"
                                    data-toggle="modal"
                                    data-size="modal-sm" >
                                    &plus;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card mt-1">
                <div class="card-header">
                    
                    <div class="row">
                        <div class="col-12">
                            Users tasks
                        </div>
                    </div>
                </div>

                <div class="card-body pt-1">
                    <div class="row sticky-top bg-light font-weight-bold">
                        <div class="col-6">
                            User
                        </div>
                        <div class="col-3">
                            Open tasks
                        </div>
                        <div class="col-3">
                            Most urgent                        
                        </div>
                    </div>
                    @foreach ($taskscount as $group)
                        @if ($group->no_of_tasks > 0)
                        <div class="row">
                            <div class="col-6">
                                {{$group->login }}
                            </div>
                            <div class="col-3">
                                {{ $group->no_of_tasks }}
                            </div>
                                @if ($group->posix_urgent_date < date('Y-m-d'))
                                    <div class="col-3 text-danger">{{ $group->urgent_date }}</div>
                                @elseif ($group->posix_urgent_date < date('Y-m-d', strtotime("+1 week")))
                                    <div  class="col-3 text-warning">{{ $group->urgent_date }}</div>
                                @else
                                    <div  class="col-3">{{ $group->urgent_date }}</div>
                                @endif
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-8" id="filter">
            <div class="card">
                <div class="card-header py-1">
                    <div class="row">
                        <div class="col-3">
                            <b>Open tasks</b>
                        </div>
                        <div class="col-5">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-info active">
                                    <input type="radio" name="my_tasks" id="alltasks" value="0">Everyone
                                </label>
                                <label class="btn btn-info">
                                    <input type="radio" name="my_tasks" id="mytasks" value="1">{{ Auth::user()->login }} 
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                  <button class="btn btn-outline-primary" type="button" id="clear-open-tasks">Clear selected on</button>
                                </div>
                                <input type="date" class="form-control" name="datetaskcleardate" id="taskcleardate">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-1"  id="tasklist">
                    <div class="row sticky-top bg-light font-weight-bold">
                        <div class="col-6">
                            Tasks
                        </div>
                        <div class="col-3">
                            Matter
                        </div>
                        <div class="col-2">
                            Due date
                        </div>
                        <div class="col-1">
                            Clear
                        </div>
                    </div>
                @if (is_array($tasks) )
                  @foreach ($tasks as $task)
                    <div class="row">
                        <div class="col-6">
                          <a href="/matter/{{ $task->trigger->matter->id }}/tasks" 
                                data-toggle="modal" 
                                data-target="#homeModal" 
                                data-size="modal-lg"
                                data-remote="false"
                                title="All tasks">
                            {{ $task->info->name }}{{ $task->detail ? " - ".$task->detail : "" }}
                          </a>
                        </div>
                        <div  class="col-3">
                          <a href="/matter/{{ $task->trigger->matter->id }}" >
                            {{ $task->trigger->matter->uid }}
                          </a>
                        </div>
                        @if ($task->due_date < date('Y-m-d'))
                            <div class="col-2 text-danger">{{ $task->due_date}}</div>
                        @elseif ($task->due_date < date('Y-m-d', strtotime("+1 week")))
                            <div  class="col-2 text-warning">{{ $task->due_date}}</div>
                        @else
                            <div  class="col-2">{{ $task->due_date}}</div>
                        @endif
                        <div  class="col-1"><input id="{{ $task->id }}" class="clear-open-task" type="checkbox" /></div>
                    </div>
                  @endforeach
                @else
                    <div class="row text-danger">The list is empty</div>
                @endif
                </div>

            </div>
            <div class="card mt-1">
                <div class="card-header py-1">
                    <div class="row">
                        <div class="col-3">
                            <b>Open renewals</b>
                        </div>
                        <div class="col-5">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-info active">
                                    <input type="radio" name="my_renewals" id="allrenewals" value="0">Everyone
                                </label>
                                <label class="btn btn-info">
                                    <input type="radio" name="my_renewals" id="myrenewals" value="1">{{ Auth::user()->login }}
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                  <button class="btn btn-outline-primary" type="button" id="clear-ren-tasks">Clear selected on</button>
                                </div>
                                <input type="date" class="form-control" name="renewalcleardate" id="renewalcleardate">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-1" id="renewallist">
                    <div class="row sticky-top bg-light font-weight-bold">
                        <div class="col-6">
                            Renewals
                        </div>
                        <div class="col-3">
                            Matter
                        </div>
                        <div class="col-2">
                            Due date
                        </div>
                        <div class="col-1">
                            Clear
                        </div>
                    </div>
                    @if (is_array($renewals) )
                      @foreach ($renewals as $task)
                        <div class="row">
                          <div class="col-6">
                            <a href="/matter/{{ $task->trigger->matter->id }}/renewals"
                                    data-toggle="modal"
                                    data-target="#homeModal"
                                    data-remote="false"
                                    title="All tasks" 
                                    data-size="modal-lg">
                              {{ $task->detail }}
                            </a>
                          </div>
                          <div  class="col-3">
                            <a href="/matter/{{ $task->trigger->matter->id }}" >
                              {{ $task->trigger->matter->uid }}
                            </a>
                          </div>
                          @if ($task->due_date < date('Y-m-d'))
                              <div class="col-2 text-danger">{{ $task->due_date}}</div>
                          @elseif ($task->due_date < date('Y-m-d', strtotime("+1 week")))
                              <div  class="col-2 text-warning">{{ $task->due_date}}</div>
                          @else
                              <div  class="col-2">{{ $task->due_date}}</div>
                          @endif
                          <div  class="col-1"><input id="{{ $task->id }}" class="clear-ren-task" type="checkbox" /></div>
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
