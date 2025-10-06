<?php

namespace App\Policies;

use App\Models\ActionType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class ActionTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return Gate::allows('moderator');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\ActionType $actionType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ActionType $actionType)
    {
        return Gate::allows('moderator');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return Gate::allows('super');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\ActionType $actionType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ActionType $actionType)
    {
        return Gate::allows('moderator');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\ActionType $actionType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ActionType $actionType)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\ActionType $actionType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ActionType $actionType)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\ActionType $actionType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ActionType $actionType)
    {
        //
    }
}
