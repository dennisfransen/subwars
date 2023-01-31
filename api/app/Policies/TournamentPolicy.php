<?php

namespace App\Policies;

use App\Http\Enums\UserType;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TournamentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Tournament $tournament
     * @return mixed
     */
    public function view(User $user, Tournament $tournament)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->type >= UserType::STREAMER;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Tournament $tournament
     * @return mixed
     */
    public function update(User $user, Tournament $tournament)
    {
        if ($user->type >= UserType::SUPERADMIN)
            return true;

        if ($user->id == $tournament->user_id)
            return true;

        return (bool)$tournament->coCasters()->where("user_id", $user->id)->count();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Tournament $tournament
     * @return mixed
     */
    public function delete(User $user, Tournament $tournament)
    {
        if ($user->type >= UserType::SUPERADMIN)
            return true;

        if ($user->id === $tournament->user_id)
            return true;

        return (bool)$tournament->coCasters()->where("user_id", $user->id)->count();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Tournament $tournament
     * @return mixed
     */
    public function restore(User $user, Tournament $tournament)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Tournament $tournament
     * @return mixed
     */
    public function forceDelete(User $user, Tournament $tournament)
    {
        //
    }
}
