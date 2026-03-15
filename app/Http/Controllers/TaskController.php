<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Task;
use Src\Facades\Auth;

class TaskController extends Controller
{
    protected Task $task;

    public function __construct()
    {
        parent::__construct();
        $this->task = new Task();
    }

    public function index()
    {
        $userId = Auth::user()->id;
        $query = Task::where('user_id', '=', $userId);

        $status = $_GET['status'] ?? '';
        if ($status && in_array($status, ['pending', 'in_progress', 'done'])) {
            $query = $query->where('status', '=', $status);
        }

        $tasks = $query->orderBy('created_at', 'DESC')->get();

        return $this->view('tasks/index', compact('tasks', 'status'));
    }

    public function create()
    {
        return $this->view('tasks/create');
    }

    public function store()
    {
        $request = new TaskStoreRequest();

        if (!$request->validate()) {
            return $request->failedValidation();
        }

        $data = $request->validated();
        $data['user_id'] = Auth::user()->id;

        $task = $this->task->create($data);

        if ($task) {
            return $this->withSuccess('Task created successfully.')->redirect('/tasks');
        }

        return $this->withError('Failed to create task.')->redirect('/tasks/create');
    }

    public function show($id)
    {
        $task = Task::find($id);

        if (!$task || $task->user_id != Auth::user()->id) {
            return $this->withError('Task not found.')->redirect('/tasks');
        }

        return $this->view('tasks/show', compact('task'));
    }

    public function edit($id)
    {
        $task = Task::find($id);

        if (!$task || $task->user_id != Auth::user()->id) {
            return $this->withError('Task not found.')->redirect('/tasks');
        }

        return $this->view('tasks/edit', compact('task'));
    }

    public function update($id)
    {
        $task = Task::find($id);

        if (!$task || $task->user_id != Auth::user()->id) {
            return $this->withError('Task not found.')->redirect('/tasks');
        }

        $request = new TaskUpdateRequest();

        if (!$request->validate()) {
            return $request->failedValidation();
        }

        $data = $request->validated();
        $success = Task::update($id, $data);

        if ($success) {
            return $this->withSuccess('Task updated successfully.')->redirect('/tasks');
        }

        return $this->withError('Failed to update task.')->redirect('/tasks/' . $id . '/edit');
    }

    public function destroy($id)
    {
        $task = Task::find($id);

        if (!$task || $task->user_id != Auth::user()->id) {
            return $this->withError('Task not found.')->redirect('/tasks');
        }

        $success = Task::delete($id);

        if ($success) {
            return $this->withSuccess('Task deleted successfully.')->redirect('/tasks');
        }

        return $this->withError('Failed to delete task.')->redirect('/tasks');
    }
}
