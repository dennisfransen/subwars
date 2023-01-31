<?php

namespace App\Http\Controllers;

use App\Http\Enums\FightTeamResult;
use App\Http\Resources\FightResource;
use App\Models\Fight;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class FightController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return AnonymousResourceCollection
     * TODO Feature test
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        if ($request->has("tournament_id")) {
            $tournament = Tournament::find($request->tournament_id);

            $teamsInNeedOfMatch = Team::whereTournamentId($tournament->id)
                ->whereDoesntHave("fights", function (Builder $query) {
                    $query->where("result", FightTeamResult::UNDECIDED)
                        ->orWhere("result", FightTeamResult::LOSS);
                })
                ->get();

            if ($teamsInNeedOfMatch->count() >= 2) {
                $number = $tournament->fights()->count();
                for ($i = 0; $i < $teamsInNeedOfMatch->count(); $i++) {
                    if ($i % 2 === 0)
                        $fight = Fight::factory()->create([
                            "tournament_id" => $request->tournament_id,
                            "round" => $teamsInNeedOfMatch[$i]->fights()->count() + 1,
                            "number" => ++$number,
                        ]);

                    if ($lastFight = $teamsInNeedOfMatch[$i]->fights()->orderByDesc("id")->first()) {
                        $lastFight->child_id = $fight->id;
                        $lastFight->save();
                    }

                    $fight->teams()->attach($teamsInNeedOfMatch[$i]);
                }
            }
        }

        return $this->getFights($request->tournament_id);
    }

    /**
     * @param Fight $fight
     * @param Request $request
     * @return AnonymousResourceCollection|Response
     * TODO Feature test
     */
    public function set_winner(Fight $fight, Request $request)
    {
        $teams = $fight->teams;

        if ($teams->count() < 2)
            return new Response(null, 404);

        if (!$fight->teams()->where("id", $request->team_id)->count())
            return new Response(null, 404);

        $fight->teams()->sync([
            $teams[0]->id => [
                "result" => $request->team_id == $teams[0]->id ? FightTeamResult::WIN : FightTeamResult::LOSS,
            ],
            $teams[1]->id => [
                "result" => $request->team_id == $teams[1]->id ? FightTeamResult::WIN : FightTeamResult::LOSS,
            ],
        ]);

        return $this->getFights($fight->tournament_id);
    }

    /**
     * @param int $tournamentId
     * @return AnonymousResourceCollection
     */
    private function getFights(int $tournamentId): AnonymousResourceCollection
    {
        $fights = Fight::query()
            ->orderBy("tournament_id")
            ->orderBy("round")
            ->with(["teams"])
            ->whereChildId(null)
            ->whereTournamentId($tournamentId)
            ->get();

        return FightResource::collection($fights);
    }
}
