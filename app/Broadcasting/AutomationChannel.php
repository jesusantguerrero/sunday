<?php

namespace App\Broadcasting;

use App\Models\User;
use Automation;

class AutomationChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\Models\User  $user
     * @return array|bool
     */
    public function join(User $user, Automation $automation)
    {
        return $user->id === $automation->user_id;
    }
}
