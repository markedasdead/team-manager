<?php

namespace App\Http\Controllers;

use App\Events\TeamUpdate;
use App\Exceptions\ForbiddenException;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamDataRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Http\Resources\MemberResource;
use App\Http\Resources\TeamResource;
use App\Models\Member;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teams = auth()->user()->teams;

        return response()->json([
            "data" => TeamResource::collection($teams)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $team = Team::create([
            'name' => $request->name,
            'user_id' => auth()->id(),
        ]);

        \App\Models\Member::create([
            'team_id' => $team->id,
            'user_id' => auth()->id(),
            'role'    => 'owner'
        ]);

        return TeamResource::make($team);
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $currentUser = $team->members()->where('user_id', auth()->id())->first();
        
        if (!$currentUser) throw new ForbiddenException();

        $team->load(['columns.tasks', 'members']);

        return response()->json([
            "data" => [
                "team" => [
                    "id"          => $team->id,
                    "name"        => $team->name,
                    "description" => $team->description,
                    "columns"     => $team->columns, 
                    "members"     => MemberResource::collection($team->members)
                ],
                "user" => [
                    "role" => $currentUser->pivot->role
                ]
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeamRequest $request, Team $team)
    {
        $member = $team->members()->where('user_id', auth()->id())->first();

        if(!$member || $member->role !== "owner") throw new ForbiddenException();

        $data = $request->validated();

        $team->update($data);

        return response()->json([
            "data" => [
                "team" => TeamResource::make($team)
            ]
        ]);
    }

    public function updateData(UpdateTeamDataRequest $request, Team $team)
    {
        $member = $team->members()->where('user_id', auth()->id())->first();

        if(!$member) throw new ForbiddenException();

        $data = $request->validated();

        $team->update($data);
        $team->refresh();

        event(
            new TeamUpdate($team)
        );

        return response()->json([
            "data" => [
                "team" => TeamResource::make($team)
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        if ($team->user_id !== auth()->id()) {
            return response()->json(['message' => 'У вас нет прав на удаление этой команды'], 403);
        }

        $team->delete();

        return response()->json(['message' => 'Команда удалена']);
    }

    public function getMembers(Team $team)
    {
        $member = $team->members()->where('user_id', auth()->id())->first();

        if(!$member || $member->role !== "owner") throw new ForbiddenException();

        $members = $team->members;

        return response()->json([
           "data" => [
                "members" => MemberResource::collection($members)
           ]
        ]);
    }
}
