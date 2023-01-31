<?php

namespace App\Http\Controllers;

use App\Http\Enums\UserType;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\SearchResource;
use App\Models\Tournament;
use App\Models\User;
use stdClass;

class SearchController extends Controller
{
    /**
     * @param SearchRequest $request
     * @return SearchResource
     */
    public function search(SearchRequest $request): SearchResource
    {
        $tournamentsQuery = Tournament::where("title", "like", "%" . $request->needle . "%");
        $streamersQuery = User::where("username", "like", "%" . $request->needle . "%")
            ->where("type", ">=", UserType::STREAMER);

        $object = new stdClass();
        $object->tournaments = $tournamentsQuery->get();
        $object->streamers = $streamersQuery->get()
            ->load("ownedTournaments");

        return new SearchResource($object);
    }
}
