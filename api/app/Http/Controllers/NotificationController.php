<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexNotificationsRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(IndexNotificationsRequest $request): AnonymousResourceCollection
    {
        $query = Notification::query();

        if ($request->has("user_id"))
            $query->where("user_id", $request->user_id);

        return NotificationResource::collection($query->get());
    }
}
