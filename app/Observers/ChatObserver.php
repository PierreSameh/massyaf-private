<?php

namespace App\Observers;

use App\Models\Chat;

class ChatObserver
{
    /**
     * Handle the Chat "created" event.
     */
    public function created(Chat $chat): void
    {
        //
    }

        /**
     * Handle the Chat "creating" event.
     *
     * @param  \App\Models\Chat  $chat
     * @return void
     */
    public function creating(Chat $chat)
    {
        if (is_null($chat->created_at)) {
            $chat->created_at = now();
        }
    }

    /**
     * Handle the Chat "updated" event.
     */
    public function updated(Chat $chat): void
    {
        //
    }

    /**
     * Handle the Chat "deleted" event.
     */
    public function deleted(Chat $chat): void
    {
        //
    }

    /**
     * Handle the Chat "restored" event.
     */
    public function restored(Chat $chat): void
    {
        //
    }

    /**
     * Handle the Chat "force deleted" event.
     */
    public function forceDeleted(Chat $chat): void
    {
        //
    }
}
