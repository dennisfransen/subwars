<?php

namespace App\Http\Controllers;

use App\Events\TournamentUpdatedEvent;
use App\Http\Requests\PriceStoreRequest;
use App\Http\Requests\PriceUpdateRequest;
use App\Http\Resources\PriceResource;
use App\Models\Price;
use App\Models\Tournament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Log;

class PriceController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * @param PriceStoreRequest $request
     * @return PriceResource
     * @throws AuthorizationException
     */
    public function store(PriceStoreRequest $request): PriceResource
    {
        $this->authorize("update", Tournament::find($request->tournament_id));

        $price = new Price();
        $price->tournament_id = $request->tournament_id;
        $price->title = $request->title;
        $price->save();

        try {
            event(new TournamentUpdatedEvent(Tournament::find($request->tournament_id), TournamentUpdatedEvent::CHANNEL_TOURNAMENT, $request->tournament_id));
        } catch (BroadcastException $e) {
            Log::error("Failed broadcasting payment event.");
        }

        return new PriceResource($price);
    }

    /**
     * Update the specified resource in storage.
     * @param PriceUpdateRequest $request
     * @param Price $price
     * @return PriceResource
     * TODO Feature test
     */
    public function update(PriceUpdateRequest $request, Price $price): PriceResource
    {
        $price->title = $request->title ?? $price->title;
        $price->tournament_id = $request->tournament_id ?? $price->tournament_id;

        if ($price->isDirty())
            $price->save();

        return new PriceResource($price);
    }

    /**
     * @param Request $request
     * @param Price $price
     * @return PriceResource
     */
    public function attachment(Request $request, Price $price): PriceResource
    {
        if ($request->hasFile("image")) {
            $file = $request->file("image");
            $name = date("YmdHi") . $file->getClientOriginalName();
            $request->file("image")->storeAs("public", $name);
            $price->image_url = $name;
        }

        if ($price->isDirty())
            $price->save();

        return new PriceResource($price);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Price $price
     * @return Response
     * TODO Feature test
     */
    public function destroy(Price $price): Response
    {
        $price->delete();

        return new Response();
    }
}
