<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Task;
use Illuminate\Http\Request;

class DashboardTasks extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['autoComplete'];
    private $user_dashboard;
    private $ptasks; // Paginated tasks for rendering
    public $tasks = []; // IDs of the tasks to be cleared
    public $clear_date;
    public $isrenewals = 0;
    public $what_tasks = 0;
    public $client_id = 2;

    public function mount()
    {
        $this->clear_date = Now()->isoFormat('L');
    }

    public function autoComplete($actor_id)
    {
        $this->what_tasks = $actor_id;
        $this->client_id = $actor_id;
        $this->resetPage();
    }
    
    public function render(Request $request)
    {
        $this->user_dashboard = $request->user_dashboard;
        $task = new Task;
        $this->ptasks = $task->openTasks($this->isrenewals, $this->what_tasks, $this->user_dashboard)->simplePaginate(18);
        return view('livewire.dashboard-tasks', [
            'ptasks' => $this->ptasks,
            'user_dashboard' => $this->user_dashboard,
        ]);
    }

    public function save()
    {
        foreach ($this->tasks as $task_id) {
            $task = Task::find($task_id);
            $task->done_date = $this->clear_date;
            $task->save();
        }
    }

    public function updatingWhatTasks()
    {
        $this->resetPage();
    }
}
