<?php

namespace App\Http\Controllers;

use App\Models\Column;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Events\TaskMoved;

class TaskController extends Controller
{
    public function store(Request $request, Column $column)
    {
        $task = $column->tasks()->create([
            'team_id'     => $column->team_id,
            'name'        => $request->name,
            'description' => $request->description,
            'deadline'    => $request->deadline,
            'order'       => $column->tasks()->count()
        ]);

        return response()->json(['data' => $task], 201);
    }

    public function move(Request $request, Task $task) {
        broadcast(new TaskMoved($task->id, $request->column_id, $task->column->team_id))->toOthers();
    }
}