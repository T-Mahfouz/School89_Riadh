<?php

namespace App\Listeners;

use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;
use Laravel\Passport\Events\AccessTokenCreated;


class GenerateTokenEvent
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  AccessTokenCreated  $event
     * @return void
     */
    public function handle(AccessTokenCreated $event)
    {
        $user = User::find($event->userId);
        $user->last_login = Carbon::now();
        $user->update();
    }
}
