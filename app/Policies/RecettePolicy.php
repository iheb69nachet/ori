<?php

namespace App\Policies;

use App\Models\Recette;
use Botble\ACL\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecettePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \Botble\ACL\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Botble\ACL\Models\User  $user
     * @param  \App\Models\Recette  $recette
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Recette $recette)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \Botble\ACL\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Botble\ACL\Models\User  $user
     * @param  \App\Models\Recette  $recette
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Recette $recette)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Botble\ACL\Models\User  $user
     * @param  \App\Models\Recette  $recette
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Recette $recette)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \Botble\ACL\Models\User  $user
     * @param  \App\Models\Recette  $recette
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Recette $recette)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \Botble\ACL\Models\User  $user
     * @param  \App\Models\Recette  $recette
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Recette $recette)
    {
        //
    }
}
