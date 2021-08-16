<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    /**
     * Get all tasks for a user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        $tasks = Task::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($item) {
                $item['editing'] = false;
                $item['new_name'] = $item->name;
                return $item;
            });

        return response()->json($tasks);

    }

    /**
     * Save a new task.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $task = new Task([
            'name' => $request->name,
            'done' => false
        ]);

        $task->user_id = Auth::id();

        $task->save();

    }

    /**
     * Edit a task.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $task = Task::find($id);

        // abort if the current user is not the owner of the task
        if (!Gate::allows('update-task', $task)) {
            abort(403);
        }

        $task->name = $request->name;
        $task->done = $request->done;
        $task->save();
    }

    /**
     * Delete a task.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::find($id);

        // abort if the current user is not the owner of the task
        if (!Gate::allows('update-task', $task)) {
            abort(403);
        }

        $task->delete();
    }
}
