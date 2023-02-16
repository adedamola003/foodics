<?php

namespace App\Listeners;

use App\Events\OrderSuccessful;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CheckIngredientsLevel
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
     * @param  \App\Events\OrderSuccessful  $event
     * @return void
     */
    public function handle(OrderSuccessful $event)
    {
        //
    }
}
