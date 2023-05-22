<?php

namespace App\Policies;

use App\Car;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CarPolicy
{
    use HandlesAuthorization;


    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Car  $car
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Car $car)
    {
        return $user->id === $car->user_id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Car  $car
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Car $car)
    {
        return $user->id === $car->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Car  $car
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Car $car)
    {
        return $user->id === $car->user_id;
    }
}
