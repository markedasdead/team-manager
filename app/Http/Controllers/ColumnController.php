<?php

namespace App\Http\Controllers;

use App\Models\Column;
use App\Models\Team;
use Illuminate\Http\Request;

class ColumnController extends Controller
{
    public function index(Team $team)
    {
        return response()->json(['data' => $team->columns]);
    }

    public function store(Request $request, Team $team)
    {
        $column = $team->columns()->create($request->validate([
            'title' => 'required|string|max:255'
        ]));
        return response()->json(['data' => $column]);
    }

    public function destroy(Column $column)
    {
        $column->delete();
        return response()->json(null, 204);
    }
}