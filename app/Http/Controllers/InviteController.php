<?php

namespace App\Http\Controllers;

use App\Exceptions\ConflictException;
use App\Exceptions\ForbiddenException;
use App\Http\Requests\InviteRequest;
use App\Http\Requests\UpdateInviteRequest;
use App\Http\Resources\InviteResource;
use App\Models\Invite;
use App\Models\Member;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class InviteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        $invites = $user->invites;

        return response()->json([
            "data" => [
                "invites" => InviteResource::collection($invites),
            ]
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
    public function store(InviteRequest $request, Team $team)
    {
        $data = $request->validated();

        $teamUser = $team->members()->where("user_id", auth()->id())->first();

        if(!$teamUser || $teamUser->pivot->role !== "owner") throw new ForbiddenException();

        $user = User::where('email', $data['email'])->first();

        if (!$user) return response()->json(["message" => "success"]);

        if ($team->members()->where(['user_id' => $user->id, 'team_id' => $team->id])->exists()) throw new ConflictException(message: "User is already a member of this team");

        $invite = $team->invites()->where(
            [
                'user_id' => $user->id,
                'team_id' => $team->id
            ]
        )->first();

        if ($invite && $invite->status !== 'rejected') {
            throw new ConflictException("Invite is already active or has been processed");
        }

        Invite::create([
            "user_id" => $user->id,
            "team_id" => $team->id,
            "inviter_id" => auth()->id(),
            "status" => "pending",
        ]);

        return response()->json([
            "message" => "success"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invite $invite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invite $invite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInviteRequest $request, Invite $invite)
    {
        if ($invite->user_id !== auth()->id()) throw new ForbiddenException();
        if ($invite->status != "pending") throw new ForbiddenException();

        $data = $request->validated();
        $invite->update($data);

        if ($data['status'] === 'accepted') {
            Member::create([
                "user_id" => auth()->id(),
                "team_id" => $invite->team_id,
                "role"    => "member"
            ]);
        }

        return response()->json([
            "data" => [
                "invite" => InviteResource::make($invite),
            ]
        ]);
    }

    public function count()
    {
        $count = auth()->user()->invites()->where('status', 'pending')->count();

        return response()->json([
            "count" => $count
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invite $invite)
    {
        //
    }
}
