<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;

/**
 * Handles CRUD operations for tasks and task filtering.
 *
 * Tasks represent actions to be completed related to matters, including
 * regular tasks and renewals. Provides filtering by assignment, client,
 * and renewal status.
 */
class TaskController extends Controller
{
    /**
     * Display a paginated list of open tasks with optional filtering.
     *
     * @param Request $request Query parameters for filtering tasks
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $task = new Task;
        $isrenewals = $request->isrenewals;
        $tasks = $task->openTasks();

        // $what_tasks, by default 0, is changed to 1 to see the "assigned_to" tasks or to the id of the client to see client specific tasks
        if ($request->what_tasks == 1) {
            $tasks->where('assigned_to', Auth::user()->login);
        }

        if ($request->what_tasks > 1) {
            $tasks->whereHas('matter.client', function (Builder $q) use ($request) { 
                $q->where('actor_id', $request->what_tasks);
            });
        }

        if ($isrenewals) {
            $tasks->where('code', 'REN');
        } else {
            $tasks->where('code', '!=', 'REN');
        }

        if (Auth::user()->default_role == 'CLI' || empty(Auth::user()->default_role)) {
            $tasks->whereHas('matter.client', function (Builder $q) {
                $q->where('actor_id', Auth::user()->id);
            });
        }

        if ($request->user_dashboard) {
            $tasks
                // Where needs encapsulation to avaid interference with others where conditions (caused by orWhere)
                ->where(function (Builder $query) use ($request) {
                    $query
                        ->where('assigned_to', $request->user_dashboard)
                        ->orWhereHas('matter', function (Builder $q) use ($request) {
                            $q->where('responsible', $request->user_dashboard);
                        });
                });
        }

        $query = $tasks->orderBy('due_date');

        if ($request->wantsJson()) {
            return response()->json($query->get());
        }

        $tasks = $query->simplePaginate(18)
            ->appends($request->input());

        return view('task.index', compact('tasks', 'isrenewals'));
    }

    /**
     * Store a newly created task.
     *
     * @param Request $request Task data including trigger_id, due_date, cost, and fee
     * @return Task The created task
     */
    public function store(Request $request)
    {
        Gate::authorize('readwrite');
        $request->validate([
            'trigger_id' => 'required|numeric',
            'due_date' => 'required',
            'cost' => 'nullable|numeric',
            'fee' => 'nullable|numeric',
        ]);
        $request->merge(['due_date' => Carbon::createFromLocaleIsoFormat('L', app()->getLocale(), $request->due_date)]);
        if ($request->filled('done_date')) {
            $request->merge(['done_date' => Carbon::createFromLocaleIsoFormat('L', app()->getLocale(), $request->done_date)]);
        }
        $request->merge(['creator' => Auth::user()->login]);

        return Task::create($request->except(['_token', '_method']));
    }

    /**
     * Display the specified task.
     *
     * @param Task $task The task to display
     * @return Task
     */
    public function show(Task $task)
    {
        return $task;
    }

    /**
     * Update the specified task.
     *
     * Handles manual due date changes (removes task rule), detail field translations,
     * and renewal task lifecycle management.
     *
     * @param Request $request Updated task data
     * @param Task $task The task to update
     * @return Task The updated task
     */
    public function update(Request $request, Task $task)
    {
        Gate::authorize('readwrite');
        $this->validate($request, [
            'due_date' => 'sometimes|filled',
            'cost' => 'nullable|numeric',
            'fee' => 'nullable|numeric',
            'detail' => 'nullable|string',
        ]);
        $request->merge(['updater' => Auth::user()->login]);
        if ($request->filled('done_date')) {
            $request->merge(['done_date' => Carbon::createFromLocaleIsoFormat('L', app()->getLocale(), $request->done_date)]);
        }
        
        // Handle detail field
        if ($request->has('detail')) {
            if (!$task->getTranslation('detail', 'en', false)) {
                // If setting a non-empty value and there was no previous English translation,
                // ensure it's set for both current locale and fallback
                $task->setTranslation('detail', 'en', $request->detail);
            }
        }
        // Remove task rule when due date is manually changed
        if ($request->filled('due_date')) {
            $request->merge(['due_date' => Carbon::createFromLocaleIsoFormat('L', app()->getLocale(), $request->due_date)]);
            $request->merge(['rule_used' => null]);
        }
        // Remove renewal from renewal management pipeline
        if (($request->filled('done_date') || $request->done) && $task->code == 'REN') {
            $request->merge(['step' => -1]);
        }
        $task->update($request->except(['_token', '_method']));

        return $task;
    }

    /**
     * Remove the specified task from storage.
     *
     * @param Task $task The task to delete
     * @return Task The deleted task
     */
    public function destroy(Task $task)
    {
        Gate::authorize('readwrite');
        $task->delete();

        return $task;
    }
}
