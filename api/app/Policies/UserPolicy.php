<?php

namespace App\Policies;

use App\Http\Enums\UserType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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
     * @param User $record
     * @return mixed
     */
    public function view(User $user, User $record)
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
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param User $record
     * @return mixed
     */
    public function update(User $user, User $record)
    {
        return ($user->type >= UserType::SUPERADMIN);
    }

    /**
     * @param User $user
     * @param User $record
     * @return bool
     */
    public function attachSponsor(User $user, User $record): bool
    {
        if ($user->id === $record->id)
            return true;

        return ($user->type >= UserType::SUPERADMIN);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param User $tournament
     * @return mixed
     */
    public function delete(User $user, User $tournament)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param User $record
     * @return mixed
     */
    public function restore(User $user, User $record)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param User $record
     * @return mixed
     */
    public function forceDelete(User $user, User $record)
    {
        //
    }
}
