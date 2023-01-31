<?php

namespace App\Policies;

use App\Http\Enums\UserType;
use App\Models\Bracket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BracketPolicy
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
     * @param Bracket $Bracket
     * @return mixed
     */
    public function view(User $user, Bracket $Bracket)
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
     * @param Bracket $Bracket
     * @return mixed
     */
    public function update(User $user, Bracket $Bracket)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Bracket $Bracket
     * @return mixed
     */
    public function delete(User $user, Bracket $Bracket)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Bracket $Bracket
     * @return mixed
     */
    public function restore(User $user, Bracket $Bracket)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Bracket $Bracket
     * @return mixed
     */
    public function forceDelete(User $user, Bracket $Bracket)
    {
        //
    }
}
