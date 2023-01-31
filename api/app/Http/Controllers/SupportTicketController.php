<?php

namespace App\Http\Controllers;

use App\Http\Enums\SupportTicketType;
use App\Http\Requests\SupportTicketStoreRequest;
use App\Http\Requests\SupportTicketUpdateRequest;
use App\Http\Resources\SupportTicketResource;
use App\Models\SupportTicket;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     * TODO Add parameters to alter order and visibility
     */
    public function index(): AnonymousResourceCollection
    {
        $this->authorize("viewAny", SupportTicket::class);

        return SupportTicketResource::collection(SupportTicket::with(["sender"])->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return SupportTicketResource
     */
    public function store(SupportTicketStoreRequest $request): SupportTicketResource
    {
        $supportTicket = new SupportTicket();
        $supportTicket->description = $request->description;
        $supportTicket->type = $request->type ?? SupportTicketType::UNSPECIFIED;

        if (auth()->check())
            $supportTicket->sender_id = auth()->user()->id;
        else
            $supportTicket->email = $request->email;

        $supportTicket->save();

        return new SupportTicketResource($supportTicket);
    }

    /**
     * Display the specified resource.
     *
     * @param SupportTicket $supportTicket
     * @return SupportTicketResource
     * @throws AuthorizationException
     */
    public function show(SupportTicket $supportTicket): SupportTicketResource
    {
        $this->authorize("view", $supportTicket);

        return new SupportTicketResource($supportTicket->load(["sender"]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param SupportTicket $supportTicket
     * @return SupportTicketResource
     * @throws AuthorizationException
     */
    public function update(SupportTicketUpdateRequest $request, SupportTicket $supportTicket): SupportTicketResource
    {
        $this->authorize("update", $supportTicket);

        $supportTicket->description = $request->description ?? $supportTicket->description;
        $supportTicket->type = $request->type ?? $supportTicket->type;
        $supportTicket->priority = $request->priority ?? $supportTicket->priority;
        $supportTicket->email = $request->email ?? $supportTicket->email;

        if ($supportTicket->isDirty())
            $supportTicket->save();

        return new SupportTicketResource($supportTicket);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SupportTicket $supportTicket
     * @return Response
     */
    public function destroy(SupportTicket $supportTicket)
    {
        //
    }
}
