<?php

namespace App\Http\Controllers;

use App\Http\Requests\SponsorStoreRequest;
use App\Http\Requests\SponsorUpdateRequest;
use App\Http\Resources\SponsorResource;
use App\Models\Sponsor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SponsorController extends Controller
{
    /**
     * @param SponsorStoreRequest $request
     * @return SponsorResource
     * TODO Unit test
     */
    public function store(SponsorStoreRequest $request): SponsorResource
    {
        $sponsor = new Sponsor();
        $sponsor->title = $request->title;
        $sponsor->save();

        return new SponsorResource($sponsor);
    }

    /**
     * @param SponsorUpdateRequest $request
     * @param Sponsor $sponsor
     * @return SponsorResource
     * TODO Unit test
     */
    public function update(SponsorUpdateRequest $request, Sponsor $sponsor): SponsorResource
    {
        $sponsor->title = $request->title ?? $sponsor->title;

        if ($sponsor->isDirty())
            $sponsor->save();

        return new SponsorResource($sponsor);
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     * TODO Test user_id filter
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(): AnonymousResourceCollection
    {
        $query = Sponsor::query();

        if ($userId = request()->get("user_id"))
            $query->whereHas("users", function (Builder $query) use ($userId) {
                $query->where("id", $userId);
            });

        return SponsorResource::collection($query->get());
    }

    /**
     * @param Request $request
     * @param Sponsor $sponsor
     * @return SponsorResource
     * TODO Unit test
     */
    public function attachment(Request $request, Sponsor $sponsor): SponsorResource
    {
        if ($request->hasFile("image")) {
            $file = $request->file("image");
            $name = date("YmdHi") . $file->getClientOriginalName();
            $request->file("image")->storeAs("public", $name);
            $sponsor->image_url = $name;
        }

        if ($sponsor->isDirty())
            $sponsor->save();

        return new SponsorResource($sponsor);
    }

    /**
     * @param Sponsor $sponsor
     * @return Response
     * TODO Unit test
     */
    public function destroy(Sponsor $sponsor): Response
    {
        $sponsor->delete();

        return new Response();
    }
}
