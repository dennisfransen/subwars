<?php

namespace App\Http\Controllers;

use App\Http\Requests\BracketStoreRequest;
use App\Http\Resources\BracketResource;
use App\Models\Bracket;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BracketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     * TODO Test
     */
    public function index(): AnonymousResourceCollection
    {
        return BracketResource::collection(Bracket::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BracketStoreRequest $request
     * @return BracketResource
     * @throws AuthorizationException
     */
    public function store(BracketStoreRequest $request): BracketResource
    {
        $this->authorize("create", Bracket::class);

        $bracket = new Bracket();
        $bracket->title = $request->title;
        $bracket->save();

        return new BracketResource($bracket);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bracket  $bracket
     * @return \Illuminate\Http\Response
     */
    public function show(Bracket $bracket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  \App\Models\Bracket  $bracket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bracket $bracket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bracket  $bracket
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bracket $bracket)
    {
        //
    }
}
