<?php

namespace App\Http\Controllers;

use App\Events\MultiChannelEvent;
use App\Http\Requests\AttachPlayerRequest;
use App\Http\Requests\DetachPlayerRequest;
use App\Http\Requests\TeamUpdateRequest;
use App\Http\Resources\TeamUpdatedResource;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;

class TeamController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param TeamUpdateRequest $request
     * @param Team $team
     * @return TeamUpdatedResource
     * @throws AuthorizationException
     */
    public function update(TeamUpdateRequest $request, Team $team): TeamUpdatedResource
    {
        $this->authorize("update", $team->tournament);

        $team->title = $request->title ?? $team->title;

        if ($team->isDirty())
            if ($team->save()) {
                $team->dispatchTeamUpdatedEvent();
                $team->tournament->dispatchTeamsUpdatedEvent();
            }

        return new TeamUpdatedResource($team);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Team $team
     * @return Response
     * @throws AuthorizationException
     */
    public function destroy(Team $team): Response
    {
        $this->authorize("update", $team->tournament);

        $team->users()->each(function (User $user) use ($team) {
            $user->dispatchTeamDestroyed($team);
        });

        $tournament = $team->tournament;

        if ($team->delete()) {
            $tournament->dispatchTeamsUpdatedEvent();
            $team->dispatchTeamDestroyedEvent();
        }

        return new Response();
    }

    /**
     * @param DetachPlayerRequest $request
     * @param Team $team
     * @return Response
     * @throws AuthorizationException
     * TODO Test creation of notification
     */
    public function detach_player(DetachPlayerRequest $request, Team $team): Response
    {
        $this->authorize("update", $team->tournament);

        $team->detachPlayer(User::find($request->user_id));

        $team->tournament->users()->updateExistingPivot($request->user_id, ["locked" => false]);

        $team->tournament->purgeEmptyTeams();

        $team->tournament->dispatchTeamsUpdatedEvent();
        $team->dispatchPlayerDetachedFromTeamEvent(
            $request->user_id,
            MultiChannelEvent::CHANNEL_TOURNAMENT,
            $team->tournament_id,
        );
        User::find($request->user_id)->dispatchPlayerDetachedFromTeam($team);

        return new Response();
    }
}
